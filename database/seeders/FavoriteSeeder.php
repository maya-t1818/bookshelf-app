<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $randomCount = rand(3, 5);
            $favoriteBookIds = $books->random(min($randomCount, $books->count()))->pluck('id');

            $user->favorites()->syncWithoutDetaching($favoriteBookIds);
        }
    }
}