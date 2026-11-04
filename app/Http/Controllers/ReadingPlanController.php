<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Http\Requests\StoreReadingPlanRequest;
use App\Http\Requests\UpdateReadingPlanRequest;
use App\Models\Book;
use App\Models\ReadingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * ログインユーザーの読書計画一覧を表示する
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentStatus = $request->input('status');

        ReadingPlan::where('user_id', $user->id)
            ->where('status', ReadingPlanStatus::InProgress)
            ->whereDate('target_date', '<', now()->toDateString())
            ->update(['status' => ReadingPlanStatus::Expired]);

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
     */
    public function create()
    {
        $books = Book::all();

        return view('reading-plans.create', compact('books'));
    }

    /**
     * 読書計画を新規保存する
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
     * 読書計画を「完了」にする
     */
    public function complete(ReadingPlan $readingPlan)
    {
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        $readingPlan->update([
            'status' => ReadingPlanStatus::Completed,
            'completed_at' => now(),
        ]);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を完了にしました。');
    }

    /**
     * 期日変更フォームを表示する
     */
    public function edit(ReadingPlan $readingPlan)
    {
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        return view('reading-plans.edit', compact('readingPlan'));
    }

    /**
     * 読書計画の期日を更新する
     */
    public function update(UpdateReadingPlanRequest $request, ReadingPlan $readingPlan)
    {
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validated();
        $newTargetDate = $validated['target_date'];

        $updateData = ['target_date' => $newTargetDate];
        if ($readingPlan->status === ReadingPlanStatus::Expired && $newTargetDate >= now()->toDateString()) {
            $updateData['status'] = ReadingPlanStatus::InProgress;
        }

        $readingPlan->update($updateData);

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を更新しました。');
    }

    /**
     * 読書計画を削除する
     */
    public function destroy(ReadingPlan $readingPlan)
    {
        if ($readingPlan->user_id !== Auth::id()) {
            abort(403);
        }

        $readingPlan->delete();

        return redirect()->route('reading-plans.index')
            ->with('success', '読書計画を削除しました。');
    }
}
