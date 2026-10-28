<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReadingPlanController extends Controller
{
    /**
     * 認証済みユーザーのみアクセス可能にする
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * ログインユーザーの読書計画一覧を表示する
     *
     * @param  Request  $request  リクエストインスタンス
     * @return View 読書計画一覧ビュー
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentStatus = $request->input('status');

        $query = ReadingPlan::with('book')
            ->where('user_id', $user->id);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $readingPlans = $query->latest('target_date')->paginate(10);
        $readingPlans->appends($request->query());

        return view('reading-plans.index', compact('readingPlans', 'currentStatus'));
    }

    /**
     * 書籍プルダウンと期日入力フォームを表示する
     *
     * @return View 書籍プルダウンと期日入力フォームビュー
     */
    public function create()
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画を新規保存する
     *
     * @param  StoreReadingPlanRequest  $request  リクエストインスタンス
     * @return RedirectResponse 読書計画一覧へのリダイレクトレスポンス
     */
    public function store(StoreReadingPlanRequest $request)
    {
        $validated = $request->validated();

        ReadingPlan::create([
            'user_id' => Auth::id(),
            'book_id' => $request->book_id,
            'target_date' => $request->target_date,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        return redirect()->route('reading-plans.index')->with('success', '読書計画を作成しました。');
    }

    /**
     * 読書計画を「読了」にする
     *
     * @param  ReadingPlan  $readingPlan  読書計画モデルインスタンス
     * @return RedirectResponse 読書計画一覧へのリダイレクトレスポンス
     */
    public function complete(ReadingPlan $readingPlan)
    {
        // 認可チェック
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        // ステータスを「読了（Completed）」に変更し、完了日に現在日時を記録
        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '本を読了にしました。');
    }

    /**
     * 期日変更フォームを表示する
     *
     * @param  ReadingPlan  $readingPlan  読書計画モデルインスタンス
     * @return View 期日変更フォームビュー
     */
    public function edit(ReadingPlan $readingPlan)
    {
        // 認可チェック
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reading-plans.edit', compact('readingPlan'));
    }

    /**
     * 読書計画の期日を更新する
     *
     * @param  UpdateReadingPlanRequest  $request  リクエストインスタンス
     * @param  ReadingPlan  $readingPlan  読書計画モデルインスタンス
     * @return RedirectResponse 読書計画一覧へのリダイレクトレスポンス
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan)
    {
        // 認可チェック
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();

        $readingPlan->update([
            'target_date' => $validated['target_date'],
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画を削除する
     *
     * @param  ReadingPlan  $readingPlan  読書計画モデルインスタンス
     * @return RedirectResponse 読書計画一覧へのリダイレクトレスポンス
     */
    public function destroy(ReadingPlan $readingPlan)
    {
        // 認可チェック
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        $readingPlan->delete();

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
}
