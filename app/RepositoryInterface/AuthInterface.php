<?php
namespace App\RepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\User;
interface AuthInterface
{
    public function getAllUsers(): Collection;
    public function findByEmail(string $email): ?User;
    public function createUser(array $data): User;
}