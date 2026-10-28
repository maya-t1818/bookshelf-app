<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use App\Services\GoogleBookService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class BookController
 *
 * 書籍のCRUD操作、検索・ジャンルフィルタリング、ソート、認可を制御するコントローラー
 */
class BookController extends Controller
{
    use AuthorizesRequests;

    protected GoogleBookService $googleBookService;

    /**
     * 書籍一覧画面（キーワード検索・ジャンルフィルタ・ソート・ページネーション対応）
     *
     * @param  Request  $request  HTTPリクエストインスタンス
     * @return View 書籍一覧ビュー
     */
    public function index(Request $request): View
    {
        $keyword = $request->input('keyword');
        $genreId = $request->input('genre');
        $sort = $request->input('sort', 'newest');

        $query = Book::with(['genres', 'user']);

        // キーワード検索
        if (! empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        // ジャンルフィルタ
        if (! empty($genreId)) {
            $query->whereHas('genres', function ($q) use ($genreId) {
                $q->where('genres.id', $genreId);
            });
        }

        // 並び順ソート
        match ($sort) {
            'oldest' => $query->oldest(),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            'title' => $query->orderBy('title', 'asc'),
            default => $query->latest(),
        };

        // ページネーション
        $books = $query->paginate(10)->appends($request->only(['keyword', 'genre', 'sort']));

        $genres = Genre::all();

        return view('books.index', compact('books', 'genres', 'keyword', 'genreId', 'sort'));
    }

    /**
     * 書籍詳細画面を表示する
     *
     * @param  Book  $book  対象の書籍モデル
     * @return View 書籍詳細ビュー
     */
    public function show(Book $book): View
    {
        $book->load([
            'genres',
            'reviews.user',
            'reviews.likedByUsers',
        ])->loadCount(['favoritedByUsers']);

        return view('books.show', compact('book'));
    }

    /**
     * 書籍新規登録画面を表示する
     *
     * @return View 書籍登録ビュー
     */
    public function create(): View
    {
        $this->authorize('create', Book::class);

        $genres = Genre::all();

        return view('books.create', compact('genres'));
    }

    /**
     * 新規書籍をデータベースに保存する
     *
     * @param  StoreBookRequest  $request  バリデーション済みリクエスト
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $book = Auth::user()->books()->create([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'description' => $validated['description'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        if (isset($validated['genres'])) {
            $book->genres()->sync($validated['genres']);
        }

        return redirect()->route('books.index')
            ->with('status', '書籍を登録しました。');
    }

    /**
     * 書籍編集画面を表示する
     *
     * @param  Book  $book  対象の書籍モデル
     * @return View 書籍編集ビュー
     */
    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $genres = Genre::all();

        return view('books.edit', compact('book', 'genres'));
    }

    /**
     * 既存の書籍情報を更新する
     *
     * @param  UpdateBookRequest  $request  バリデーション済みリクエスト
     * @param  Book  $book  対象の書籍モデル
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $book->update([
            'title' => $validated['title'],
            'author' => $validated['author'],
            'description' => $validated['description'] ?? null,
            'isbn' => $validated['isbn'] ?? null,
            'published_date' => $validated['published_date'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
        ]);

        $book->genres()->sync($validated['genres'] ?? []);

        return redirect()->route('books.show', $book)
            ->with('status', '書籍情報を更新しました。');
    }

    /**
     * 指定された書籍情報を削除する
     *
     * @param  Book  $book  対象の書籍モデル
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()->route('books.index')
            ->with('status', '書籍情報を削除しました。');
    }

    /**
     * コンストラクタ
     */
    public function __construct(GoogleBookService $googleBookService)
    {
        $this->googleBookService = $googleBookService;
    }

    /**
     * ISBNをもとにGoogle Books APIから書籍情報を取得しJSONで返す
     *
     * @param  string  $isbn  URLから渡される13桁のISBN
     */
    public function searchByIsbn(string $isbn): JsonResponse
    {
        if (strlen($isbn) !== 13) {
            return response()->json([
                'error' => 'ISBNは13桁で入力してください。',
            ], 400);
        }

        try {
            $bookData = $this->googleBookService->searchByIsbn($isbn);

            if (! $bookData) {
                return response()->json([
                    'error' => '書籍が見つかりませんでした。',
                ], 404);
            }

            return response()->json($bookData);

        } catch (\Exception $e) {

            $status = $e->getCode();

            $status = ($status >= 400 && $status < 600) ? $status : 500;

            return response()->json([
                'error' => $e->getMessage(),
            ], $status);
        }
    }
}
