<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    public function test_ジャンルは複数の本に関連付けられること()
    {
        $genre = Genre::factory()->create(['name' => '技術書']);
        $book = Book::factory()->create();

        $genre->books()->attach($book->id);

        $this->assertTrue($genre->books->contains($book));
        $this->assertEquals('技術書', $genre->name);
    }
}