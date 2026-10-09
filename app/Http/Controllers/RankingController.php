<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        $rankedBooks = Book::has('reviews') 
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->take(10)
            ->get();

        return view('ranking.index', compact('rankedBooks'));
    }
}