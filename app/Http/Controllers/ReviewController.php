<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * コンストラクタで認証ミドルウェアを適用
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 指定された書籍に対するレビューを新規投稿する
     *
     * @param  StoreReviewRequest  $request  レビュー投稿用のリクエストデータ
     * @param  Book  $book  レビュー対象の書籍モデルインスタンス
     * @return RedirectResponse 前画面へのリダイレクトレスポンス
     */
    public function store(StoreReviewRequest $request, Book $book): RedirectResponse
    {
        $book->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->validated('rating'),
            'comment' => $request->validated('comment'),
        ]);

        return back()->with('status', 'レビューを投稿しました。');
    }

    /**
     * レビュー編集画面を表示する
     *
     * @param  Review  $review  編集対象のレビューモデルインスタンス
     * @return View レビュー編集ビュー
     */
    public function edit(Review $review): View
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * 指定されたレビュー情報を更新する
     *
     * @param  UpdateReviewRequest  $request  レビュー更新用のリクエストデータ
     * @param  Review  $review  更新対象のレビューモデルインスタンス
     * @return RedirectResponse 書籍詳細画面へのリダイレクトレスポンス
     */
    public function update(UpdateReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $review->update($request->validated());

        return redirect()->route('books.show', $review->book_id)
            ->with('status', 'レビューを更新しました。');
    }

    /**
     * 指定されたレビューを削除する
     *
     * @param  Review  $review  削除対象のレビューモデルインスタンス
     * @return RedirectResponse 前画面へのリダイレクトレスポンス
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('status', 'レビューを削除しました。');
    }
}
