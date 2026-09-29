<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    public function toggle(Request $request, Review $review)
    {
        $request->user()->LikedReviews()->toggle($review->id);

        return back();
    }
}
