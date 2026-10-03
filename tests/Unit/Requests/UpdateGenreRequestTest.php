<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateGenreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_自身と同じジャンル名での更新は通過すること()
    {
        $genre = Genre::factory()->create(['name' => 'SF']);

        Route::put('/test-genres/{genre}', function (UpdateGenreRequest $request) {
            return response()->json(['success' => true]);
        });

        $request = UpdateGenreRequest::create("/test-genres/{$genre->id}", 'PUT', [
            'name' => 'SF', 
        ]);

        $request->setContainer($this->app);
        $request->setRedirector($this->app->make('redirect'));

        $route = Route::getRoutes()->match($request);
        $request->setRouteResolver(fn () => $route);

        $validator = Validator::make($request->all(), $request->rules());
        $this->assertTrue($validator->passes());
    }
}