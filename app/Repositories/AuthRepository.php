<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\User;
use App\RepositoryInterface\AuthInterface;
use App\Models\AuthLog;

class AuthRepository implements AuthInterface
{
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    // public function findByEmail(string $email): ?User
    // {
    //     return User::where('email', $email)->first();
    // }

    public function findByEmail(string $email): ?User
    {
        // 🟢 បន្ថែម with(['role.permissions']) ត្រង់នេះ
        return User::with(['role.permissions'])->where('email', $email)->first();
    }
    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function getLogs(): Collection
    {
        return AuthLog::with('user')->orderBy('timestamp', 'desc')->get();
    }
    public function getLogDetail(int $id): AuthLog
    {
        return AuthLog::with('user')->findOrFail($id);
    }
}
