<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')->get();

        return view('genres.index', compact('genres'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        Genre::create($request->validated());

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを作成しました。');
    }

    public function show(Genre $genre)
    {
        $books = $genre->books()->latest()->paginate(10);

        return view('genres.show', compact('genre', 'books'));
    }

    public function edit(Genre $genre)
    {
        $this->authorize('update', $genre);
        
        return view('genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $this->authorize('update', $genre);

        $genre->update($request->validated());

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを更新しました。');
    }

    public function destroy(Genre $genre)
    {
        if ($genre->books()->exists()) {
            return back()->withErrors(['error' => 'このジャンルには書籍が紐付いているため削除できません。']);
        }

        $genre->delete();

        return redirect()->route('genres.index')
            ->with('status', 'ジャンルを削除しました。');
    }
}
