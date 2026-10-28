<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * ログインユーザーの通知一覧を表示
     *
     * @return View 通知一覧ビュー
     */
    public function index()
    {
        $user = Auth::user();

        // ユーザーに届いた通知を新しい順に取得
        $notifications = $user->notifications()->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * 特定の通知を既読にする
     *
     * @param  int  $id  通知ID
     * @return RedirectResponse 前のページへのリダイレクトレスポンス
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        return back()->with('success', '通知を既読にしました。');
    }
}
