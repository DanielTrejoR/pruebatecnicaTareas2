<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class UserService
{
    public function listUsers(): Collection
    {
        return User::query()
            ->orderBy('name')
            ->get();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }
}
