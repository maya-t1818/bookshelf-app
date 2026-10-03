<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreGenreRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRules(): array
    {
        return (new StoreGenreRequest())->rules();
    }

    public function test_ジャンル名の重複時にバリデーションエラーとなること()
    {
        Genre::factory()->create(['name' => 'SF']);

        $validator = Validator::make(['name' => 'SF'], $this->getRules());
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }
}