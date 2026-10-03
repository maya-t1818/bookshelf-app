<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreBookRequest;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function getRules(): array
    {
        return (new StoreBookRequest())->rules();
    }

    public function test_正常なデータでバリデーションが通過すること()
    {
        $genre = Genre::factory()->create();

        $data = [
            'title'        => 'テスト書籍',
            'author'       => 'テスト著者',
            'isbn'         => '1234567890123',
            'published_date' => '2026-01-01',
            'genres'    => [$genre->id],
            'image_url'    => 'https://example.com/image.jpg',
        ];

        $validator = Validator::make($data, $this->getRules());
        $this->assertTrue($validator->passes());
    }

    public function test_必須項目が未入力の場合に失敗すること()
    {
        $validator = Validator::make([], $this->getRules());
        $this->assertTrue($validator->fails());

        $errors = $validator->errors();
        $this->assertTrue($errors->has('title'));
        $this->assertTrue($errors->has('author'));
        $this->assertTrue($errors->has('isbn'));
        $this->assertTrue($errors->has('published_date'));
        $this->assertTrue($errors->has('genres'));
    }

    public function test_isbnが13桁以外または一意でない場合に失敗すること()
    {
        Book::factory()->create(['isbn' => '1234567890123']);

        $validator = Validator::make(['isbn' => '1234567890'], $this->getRules());
        $this->assertTrue($validator->fails());

        $validator = Validator::make(['isbn' => '1234567890123'], $this->getRules());
        $this->assertTrue($validator->fails());
    }
}