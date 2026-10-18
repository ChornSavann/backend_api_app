<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\Interface\AuthServiceInterface;
use Illuminate\Validation\ValidationException;
use App\Models\AuthLog;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function index(): jsonResponse
    {
        $users = $this->authService->getAllUsers();

        return response()->json([
            'status' => 'success',
            'message' => 'បញ្ជីអ្នកប្រើប្រាស់ទាំងអស់',
            'count' => $users->count(),
            'data' => $users
        ], 200);
    }

    public function getProfile(Request $request)
    {

        $user = $request->user()->load('role');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'image_url' => $user->image_url,
                'role_id' => $user->role_id,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'description' => $user->role->description,
                ] : null,
            ]
        ], 200);
    }
    
    public function createUser(Request $request): jsonResponse
    {

        $result = $this->authService->register(
            $request->all(),
            $request->file('image')
        );

        return response()->json([
            'status' => 'success',
            'token' => $result['token'],
            'user' => $result['user']
        ], 201);
    }


    public function refreshToken(Request $request): jsonResponse
    {
        $newToken = $this->authService->refreshToken($request->user());

        return response()->json([
            'status' => 'success',
            'token' => $newToken,
        ], 200);
    }


    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $result = $this->authService->login($request->email, $request->password);

            return response()->json([
                'success' => true,
                'message' => 'Login successful..!',
                'token' => $result['token'],
                'user' => $result['user']
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authService->logout($user);

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function getLogs(): JsonResponse
    {
        $logs = $this->authService->getLogs();
        return response()->json([
            'success' => true,
            'message' => 'Auth logs retrieved successfully',
            'data' => $logs
        ], 200);
    }
    public function getLogDetail($id): jsonResponse
    {
        $log = $this->authService->getLogDetail($id);

        if (!$log) {
            return response()->json([
                'success' => false,
                'message' => 'Auth log not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Auth log detail retrieved successfully',
            'data' => $log
        ], 200);
    }
}
