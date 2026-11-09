<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use App\RepositoryInterface\AuthInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\UploadedFile;
use App\Models\AuthLog;
use Carbon\Carbon;
use App\Services\Interface\AuthServiceInterface;

class AuthService implements AuthServiceInterface
{
    protected  AuthInterface $authRepository;

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

    //     if (!$user) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The email address you entered is incorrect.'],
    //         ]);
    //     }

    //     if (!Hash::check($password, $user->password)) {
    //         throw ValidationException::withMessages([
    //             'password' => ['The password you entered is incorrect.'],
    //         ]);
    //     }
    //     $user->tokens()->delete();
    //     AuthLog::updateOrCreate(
    //         ['user_id' => $user->id],
    //         [
    //             'action' => 'LOGIN',
    //             'timestamp' => Carbon::now(),
    //         ]
    //     );
    //     $token = $user->createToken('login_token')->plainTextToken;

    //     return [
    //         'token' => $token,
    //         'user' => $user,
    //     ];
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
        if (!$user->role_id && !$user->role) {
            throw ValidationException::withMessages([
                'email' => ['user របស់អ្នកមិនមានសិទ្ធិប្រើប្រាស់ប្រព័ន្ធនេះទេ (No Role Assigned)។'],
            ]);
        }
        $user->tokens()->delete();

        AuthLog::updateOrCreate(
            ['user_id' => $user->id],
            [
                'action' => 'LOGIN',
                'timestamp' => Carbon::now(),
            ]
        );

        // $token = $user->createToken('login_token')->plainTextToken;
        $token = $user->createToken('login_token', ['*'], now()->addDays(30))
        ->plainTextToken;
        // $permissions = $user->role && $user->role->permissions
        //     ? $user->role->permissions->pluck('name')->toArray()
        //     : [];
        $permissions = $user->role->permissions
        ? $user->role->permissions->pluck('name')->toArray()
        : [];
        return [
            'token' => $token,
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'phone'       => $user->phone ?? '',
                'image_url'   => $user->image_url ?? '',
                'role_id'     => $user->role_id ?? ($user->role ? $user->role->id : null),
                // 'role'        => $user->role ? $user->role->name : 'Cashier',
                'role'        => $user->role ? $user->role->name : 'User',
                'permissions' => $permissions,
            ],
        ];
    }

    public function register(array $userDetails, ?UploadedFile $image): array
    {
        $imagePath = null;
        if ($image) {
            $imagePath = $image->store('users', 'public');
        }
        $isFirstUser = \App\Models\User::count() === 0;
        $roleName = $isFirstUser ? 'Supper Admin' : 'Cashier';

        $role = \App\Models\Role::where('name', $roleName)->first();
        $roleId = $role ? $role->id : ($isFirstUser ? 3 : 1);
        $user = $this->authRepository->createUser([
            'name' => $userDetails['name'],
            'email' => $userDetails['email'],
            'phone' => $userDetails['phone'] ?? null,
            'image' => $imagePath,
            'password' => Hash::make($userDetails['password']),
            'role_id' => $roleId,
        ]);

        $user->load('role');

        $token = $user->createToken('auth_token')->plainTextToken;

        return ['token' => $token, 'user' => $user];
    }

    public function refreshToken(User $user): string
    {
        $token = $user->currentAccessToken();
        if ($token) {
            \Laravel\Sanctum\PersonalAccessToken::where('id', $token->id)->delete();
        }

        return $user->createToken('login_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        if ($user) {
            AuthLog::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'action' => 'LOGOUT',
                    'timestamp' => Carbon::now(),
                ]
            );
        }
        $token = $user->currentAccessToken();
        if ($token) {
            \Laravel\Sanctum\PersonalAccessToken::where('id', $token->id)->delete();
        }
    }

    public function getLogs()
    {
        return $this->authRepository->getLogs();
    }
    public function getLogDetail(int $id)
    {
        return $this->authRepository->getLogDetail($id);
    }

    public function forceLogoutUser(int $id): ?array
    {
        $user = User::find($id);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'រកមិនឃើញអតិថិជន/យូសែរនេះទេ',
                'status' => 404
            ];
        }

        $this->authRepository->updateOrCreateLatestLogout($user->id);
        $user->tokens()->delete();

        return [
            'success' => true,
            'message' => 'បានបណ្តេញ User នោះចេញពីប្រព័ន្ធដោយជោគជ័យ',
            'status' => 200
        ];
    }

    public function deleteLog(int $id): array
    {
        $log = $this->authRepository->findLogById($id);

        if (!$log) {
            return [
                'success' => false,
                'message' => 'Auth log not found',
                'status' => 404
            ];
        }

        $this->authRepository->deleteLog($log);

        return [
            'success' => true,
            'message' => 'Auth log deleted successfully',
            'status' => 200
        ];
    }
}