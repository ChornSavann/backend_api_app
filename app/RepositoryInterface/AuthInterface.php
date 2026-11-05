<?php

namespace App\RepositoryInterface;

use Illuminate\Support\Collection;
use App\Models\AuthLog;
use App\Models\User;

interface AuthInterface
{
    public function getAllUsers(): Collection;
    public function findByEmail(string $login): ?User;
    public function createUser(array $data): User;
    public function getLogs(): Collection;
    public function getLogDetail(int $id): AuthLog;
    public function deleteLog(AuthLog $log): bool;

    public function updateOrCreateLatestLogout(int $userId): void;
    public function findLogById(int $id): ?AuthLog;
}