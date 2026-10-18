<?php
namespace App\RepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\AuthLog;
use App\Models\User;
interface AuthInterface
{
    public function getAllUsers(): Collection;
    // public function findByEmail(string $email,$name): ?User;
    public function findByEmail(string $login): ?User;
    public function createUser(array $data): User;
    public function getLogs(): Collection;
    public function getLogDetail(int $id): AuthLog; 
}