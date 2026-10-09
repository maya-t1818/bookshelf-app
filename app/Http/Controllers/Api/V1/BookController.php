<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Http\Resources\Api\V1\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(IndexBookRequest $request): AnonymousResourceCollection
    {
        $validated = $request->validated();

        $query = Book::query()
            ->with('genres') 
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');

        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        if (!empty($validated['genre_id'])) {
            $query->whereHas('genres', function ($q) use ($validated) {
                $q->where('genres.id', $validated['genre_id']);
            });
        }

        $perPage = $validated['per_page'] ?? 10;
        $books = $query->latest('id')->paginate($perPage);

        return BookResource::collection($books);
    }

    public function show(Book $book): BookResource
    {
        $book->load(['genres', 'reviews.user'])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return new BookResource($book);
    }

    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $book = DB::transaction(function () use ($validated) {
            $book = Book::create($validated);

            if (isset($validated['genre_id'])) {
                $book->genres()->attach($validated['genre_id']);
            }

            return $book;
        });

        return (new BookResource($book->load('genres')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, Book $book): BookResource
    {
        $validated = $request->validated();

        DB::transaction(function () use ($book, $validated) {
            $book->update($validated);

            if (isset($validated['genre_id'])) {
                $book->genres()->sync((array) $validated['genre_id']);
            }
        });

        return new BookResource($book->fresh(['genres']));
    }

    public function destroy(Book $book): JsonResponse
    {
        DB::transaction(function () use ($book) {
            $book->reviews()->delete();
            $book->genres()->detach();        
            $book->favoriteBooks()->detach(); 
            $book->delete();
        });

        return response()->json(null, 204);
    }
}