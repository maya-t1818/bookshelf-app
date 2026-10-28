<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * 書籍一覧を取得する
     *
     * @param  IndexBookRequest  $request  絞り込み・ページネーションのリクエストデータ
     * @return AnonymousResourceCollection 書籍リソースコレクション
     */
    public function index(IndexBookRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $query = Book::query()
            ->with('genres')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        // キーワード検索（タイトル・著者名）
        if (! empty($validated['keyword'])) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        // ISBN検索を追加
        if (! empty($validated['isbn'])) {
            $isbn = $validated['isbn'];
            $query->where('isbn', 'like', "%{$isbn}%");
        }

        // ジャンルフィルタ
        if (! empty($validated['genre_id'])) {
            $query->whereHas('genres', function ($q) use ($validated) {
                $q->where('genres.id', $validated['genre_id']);
            });
        }

        // 並び順ソート
        $sort = $validated['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest('id');
                break;
            case 'reviews_count_desc':
                $query->orderByDesc('reviews_count');
                break;
            case 'rating_desc':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'latest':
            default:
                $query->latest('id');
                break;
        }

        $perPage = $validated['per_page'] ?? 20;
        $books = $query->paginate($perPage);

        // ページネーションに検索条件を引き継がせる
        $books->appends($request->query());

        return BookResource::collection($books);
    }

    /**
     * 指定されたIDの書籍詳細情報を取得する
     *
     * @param  Book  $book  対象の書籍モデルインスタンス
     * @return BookResource 単一の書籍リソース
     */
    public function show(Book $book): BookResource
    {
        $book->load(['genres', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    /**
     * 書籍情報を新規登録する
     *
     * @param  StoreBookRequest  $request  書籍登録用のリクエストデータ
     * @return JsonResponse ステータスコード201のJSONレスポンス
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $book = DB::transaction(function () use ($validated, $request) {
            $book = $request->user()->books()->create($validated);
            if (isset($validated['genre_id'])) {
                $book->genres()->attach($validated['genre_id']);
            }

            return $book;
        });

        return (new BookResource($book->load('genres')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * 指定された書籍の情報を更新する
     *
     * @param  UpdateBookRequest  $request  書籍更新用のリクエストデータ
     * @param  Book  $book  更新対象の書籍モデルインスタンス
     * @return BookResource 更新後の書籍リソース
     */
    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        DB::transaction(function () use ($book, $validated) {
            $book->update($validated);

            if (isset($validated['genre_ids'])) {
                $book->genres()->sync((array) $validated['genre_ids']);
            }
        });

        return new BookResource($book->fresh(['genres']));
    }

    /**
     * 指定された書籍および関連データを削除する
     *
     * @param  Book  $book  削除対象の書籍モデルインスタンス
     * @return JsonResponse ステータスコード204の空JSONレスポンス
     */
    public function destroy(Book $book): JsonResponse
    {
        $this->authorize('delete', $book);

        DB::transaction(function () use ($book) {
            $book->reviews()->delete();
            $book->genres()->detach();
            $book->favoriteBooks()->detach();
            $book->delete();
        });

        return response()->json(null, 204);
    }
}
