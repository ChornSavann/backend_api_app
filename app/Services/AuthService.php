<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\RepositoryInterface\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use App\Services\Interface\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    protected $authRepository;

    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function getAllUsers()
    {
        return $this->authRepository->getAllUsers();
    }

    // public function login(string $email, string $password): array
    // {
    //     $user = $this->authRepository->findByEmail($email);

    //     if (!$user || !Hash::check($password, $user->password)) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The Email and password credentials are incorrect.'],
    //             'password' => ['The Email and password credentials are incorrect.'],    
    //         ]);
    //     }

    //     $token = $user->createToken('login_token')->plainTextToken;

    //     return ['token' => $token, 'user' => $user];
    // }

    public function login(string $email, string $password): array
    {

        $user = $this->authRepository->findByEmail($email);
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The email address you entered is incorrect.'],
            ]);
        }

        if (!Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password you entered is incorrect.'],
            ]);
        }

        $token = $user->createToken('login_token')->plainTextToken;

        // Load store មកជាមួយ user តែម្តង (ធានាថា User ស្ថិតក្នុង Model មាន relation with store)
        // $user->load('store');

        return [
            'token' => $token,
            'user' => $user
        ];
    }

    public function register(array $userDetails, ?UploadedFile $image): array
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('users', 'public');
        }

        $user = $this->authRepository->createUser([
            'name' => $userDetails['name'],
            'email' => $userDetails['email'],
            'phone' => $userDetails['phone'] ?? null,
            'image' => $imagePath,
            'password' => Hash::make($userDetails['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    public function refreshToken(User $user): string
    {
        $user->currentAccessToken()->delete();
        return $user->createToken('login_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
