<?php

namespace App\Policies;

use app\Models\Genre;
use App\Models\User;

class GenrePolicy
{
    /**
     * ジャンル編集の認可
     */
    public function update(User $user, Genre $genre): bool
    {
        return true;
    }
}
