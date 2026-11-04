<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_自身のisbnのままでも更新バリデーションが通過すること()
    {
        $genre = Genre::factory()->create();
        // あらかじめ特定のISBNで本を作成する
        $book = Book::factory()->create(['isbn' => '9784123456789']);

        Route::put('/test-books/{book}', function (UpdateBookRequest $request) {
            return response()->json(['success' => true]);
        });

        $request = UpdateBookRequest::create("/test-books/{$book->id}", 'PUT', [
            'title' => '更新タイトル',
            'author' => '更新著者',
            'isbn' => '9784123456789',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $request->setContainer($this->app);
        $request->setRedirector($this->app->make('redirect'));

        $route = Route::getRoutes()->match($request);
        $request->setRouteResolver(fn () => $route);

        $validator = Validator::make($request->all(), $request->rules());

        // 自分のIDが unique の除外（ignore）対象になっているため、重複エラーにならず通る
        $this->assertTrue($validator->passes());
    }
}
