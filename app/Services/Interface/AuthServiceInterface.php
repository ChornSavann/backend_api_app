<?php

namespace App\Services\Interface;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface AuthServiceInterface
{
    public function login(string $email, string $password): array;
    
    public function register(array $userDetails, ?UploadedFile $image): array;
    
    public function logout(User $user): void;
    
    public function refreshToken(User $user): string;
    
    public function getAllUsers();
}
