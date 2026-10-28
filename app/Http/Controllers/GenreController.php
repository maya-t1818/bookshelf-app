<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GenreController extends Controller
{
    /**
     * 認証ユーザのみアクセス可能にする
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * ジャンル一覧画面を表示
     *
     * @return View ジャンル一覧ビュー
     */
    public function index(): View
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    /**
     * ジャンル新規作成画面を表示
     *
     * @return View ジャンル作成ビュー
     */
    public function create(): View
    {
        return view('genres.create');
    }

    /**
     * 新しいジャンルを登録
     *
     * @param  StoreGenreRequest  $request  ジャンル登録用のリクエストデータ
     * @return RedirectResponse 一覧画面へのリダイレクトレスポンス
     */
    public function store(StoreGenreRequest $request): RedirectResponse
    {
        Genre::create($request->validated());

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを作成しました。');
    }

    /**
     * 指定されたジャンルの詳細画面を表示
     *
     * @param  Genre  $genre  対象のジャンルモデルインスタンス
     * @return View ジャンル詳細ビュー
     */
    public function show(Genre $genre): View
    {
        $books = $genre->books()->latest()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    /**
     * ジャンル編集画面を表示
     *
     * @param  Genre  $genre  編集対象のジャンルモデルインスタンス
     * @return View ジャンル編集ビュー
     */
    public function edit(Genre $genre): View
    {
        $this->authorize('update', $genre);

        return view('genres.edit', compact('genre'));
    }

    /**
     * 指定されたジャンル情報を更新
     *
     * @param  UpdateGenreRequest  $request  ジャンル更新用のリクエストデータ
     * @param  Genre  $genre  更新対象のジャンルモデルインスタンス
     * @return RedirectResponse 一覧画面へのリダイレクトレスポンス
     */
    public function update(UpdateGenreRequest $request, Genre $genre): RedirectResponse
    {
        $this->authorize('update', $genre);

        $genre->update($request->validated());

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを更新しました。');
    }

    /**
     * 指定されたジャンルを削除（書籍が紐付いている場合はエラーを返す）
     *
     * @param  Genre  $genre  削除対象のジャンルモデルインスタンス
     * @return RedirectResponse 一覧画面または前画面へのリダイレクトレスポンス
     */
    public function destroy(Genre $genre): RedirectResponse
    {
        if ($genre->books()->exists()) {
            return back()->withErrors(['error' => 'このジャンルには書籍が紐付いているため削除できません。']);
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを削除しました。');
    }
}
