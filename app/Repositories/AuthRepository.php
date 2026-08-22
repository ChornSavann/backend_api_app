<?php
namespace App\Repositories;
use Illuminate\Support\Collection;
use App\Models\User;
use App\RepositoryInterface\AuthInterface;
class AuthRepository implements AuthInterface
{
    public function getAllUsers(): Collection
    {
        return User::all();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }
}