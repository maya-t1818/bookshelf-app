<?php

namespace App\Http\Controllers;

use app\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favoriteBooks = $request->user()
            ->favorites()
            ->with('genres')
            ->latest()
            ->paginate(10);

        return view('favorites.index', compact('favoriteBooks'));
    }

    public function toggle(Request $request, Book $book)
    {
        $request->user()->favorites()->toggle($book->id);

        return back();
    }
}
