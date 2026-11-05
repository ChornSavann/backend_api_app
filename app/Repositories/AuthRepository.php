<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use App\Models\User;
use App\RepositoryInterface\AuthInterface;
use App\Models\AuthLog;
use Carbon\Carbon;
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

   public function updateOrCreateLatestLogout(int $userId): void
    {
        $latestLog = AuthLog::where('user_id', $userId)->latest('id')->first();

        if ($latestLog) {
            $latestLog->update([
                'action' => 'LOGOUT',
                'timestamp' => Carbon::now(),
            ]);
        } else {
            AuthLog::create([
                'user_id' => $userId,
                'action' => 'LOGOUT',
                'timestamp' => Carbon::now(),
            ]);
        }
    }

    public function findLogById(int $id): ?AuthLog
    {
        return AuthLog::find($id);
    }

    public function deleteLog(AuthLog $log): bool
    {
        return $log->delete();
    }
}