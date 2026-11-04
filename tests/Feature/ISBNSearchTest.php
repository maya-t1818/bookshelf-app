<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ISBNSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_isbnで書籍を検索できること()
    {

        Book::factory()->create(['isbn' => '9784873119038']);
        Book::factory()->create(['isbn' => '9784297121242']);

        $response = $this->getJson('/api/v1/books?isbn=9038');

        $response->assertStatus(200)
            ->assertJsonFragment(['isbn' => '9784873119038'])
            ->assertJsonMissing(['isbn' => '9784297121242']);
    }
}
