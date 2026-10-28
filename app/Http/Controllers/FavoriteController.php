<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * ログインユーザーがお気に入りに登録した書籍の一覧画面を表示する
     *
     * @param  Request  $request  リクエストインスタンス
     * @return View お気に入り一覧ビュー
     */
    public function index(Request $request): View
    {
        $books = $request->user()
            ->favoritebooks()
            ->with('genres')
            ->latest()
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    /**
     * 指定された書籍のお気に入りトグルを切り替える
     *
     * @param  Request  $request  リクエストインスタンス
     * @param  Book  $book  対象の書籍モデルインスタンス
     * @return RedirectResponse 前のページへのリダイレクトレスポンス
     */
    public function toggle(Request $request, Book $book): RedirectResponse
    {
        $request->user()->favoritebooks()->toggle($book->id);

        return back();
    }
}
