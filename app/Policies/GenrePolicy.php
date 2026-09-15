<?php

namespace App\Policies;

use app\Models\Genre;
use App\Models\User;

class GenrePolicy
{
        public function update(User $user, Genre $genre): bool
    {
        return $user !== null;
    }
}
