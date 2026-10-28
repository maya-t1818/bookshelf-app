<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    /**
     * 指定されたレビューに対する「いいね」トグルを切り替える
     *
     * @param  Request  $request  リクエストインスタンス
     * @param  Review  $review  対象のレビューモデルインスタンス
     * @return RedirectResponse 前画面へのリダイレクトレスポンス
     */
    public function toggle(Request $request, Review $review): RedirectResponse
    {
        $request->user()->LikedReviews()->toggle($review->id);

        return back();
    }
}
