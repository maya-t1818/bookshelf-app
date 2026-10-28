<?php

namespace App\Http\Controllers;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Summary of __construct
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * マイ読書レポート一覧
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $userReviews = Review::where('user_id', $user->id)->get();

        // 基本サマリー
        $totalReviews = $userReviews->count();
        $uniqueBooksCount = ReadingPlan::where('user_id', $user->id)
            ->where('status', ReadingPlanStatus::Completed)
            ->count();
        $averageRating = $userReviews->avg('rating');

        // 評価分布
        // 星1〜星5の順で件数を集計（レビューがない評価は0件）
        $ratingDistribution = collect(range(1, 5))->map(
            fn ($rating) => $userReviews->where('rating', $rating)->count()
        );

        // 3. 高評価書籍TOP5
        $topRatedBooks = Book::withAvg('reviews', 'rating')
            ->get()
            ->filter(fn ($book) => $book->reviews_avg_rating >= 4.0)
            ->sortByDesc('reviews_avg_rating')
            ->take(5)
            ->map(function ($book) use ($user) {

                $latestReview = $book->reviews()->where('user_id', $user->id)->latest()->first();

                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author ?? '',
                    'rating' => $latestReview ? $latestReview->rating : round($book->reviews_avg_rating ?? 0),
                ];
            })->values();

        // 4. ジャンル別評価傾向 TOP5
        $genreRatings = Genre::with('books.reviews')
            ->get()
            ->map(function ($genre) {
                $ratings = $genre->books->flatMap->reviews->pluck('rating');

                return [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'count' => $genre->books->flatMap->reviews->count(),
                    'average_rating' => $ratings->avg() ?? 0,
                ];
            })
            ->sortByDesc('average_rating')
            ->take(5)
            ->values();

        $stats = [
            'summary' => [
                'total_reviews' => $totalReviews,
                'books_read' => $uniqueBooksCount,
                'average_rating' => $averageRating ?? 0,
            ],
            'rating_distribution' => $ratingDistribution,
            'top_rated_books' => $topRatedBooks,
            'genre_ratings' => $genreRatings,
        ];

        return view('reports.index', compact('stats'));
    }
}
