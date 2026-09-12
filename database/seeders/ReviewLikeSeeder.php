<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    
    {
        $reviews = Review::all();
        $allUsers = User::all();

        if ($reviews->isEmpty() || $allUsers->isEmpty()) {
            return;
        }

        foreach ($reviews as $review) {
            $eligibleUsers = $allUsers->where('id', '!=', $review->user_id);

            if ($eligibleUsers->isEmpty()) {
                continue;
            }

            $likeCount = rand(0, min(3, $eligibleUsers->count()));

            if ($likeCount > 0) {
                $likerUserIds = $eligibleUsers->random($likeCount)->pluck('id');
                $review->ReviewLikes()->syncWithoutDetaching($likerUserIds);
            }
        }
    }
}
