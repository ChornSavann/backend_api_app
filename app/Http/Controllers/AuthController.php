<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\Interface\AuthServiceInterface;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        $users = $this->authService->getAllUsers();

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'data' => $users
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $result = $this->authService->login($request->email, $request->password);

        return response()->json([
            'status' => 'success',
            'token' => $result['token'],
            'user' => $result['user']
        ], 200);
    }
    
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $result = $this->authService->login($request->email, $request->password);

    //     // 🟢 สมมติว่า $result['user'] មាន relation ជាមួយ store 
    //     // ឬអ្នកអាចទាញយក store ផ្ទាល់: $store = $result['user']->store;
    //     $store = $result['user']->store ?? null; // អាស្រ័យលើ Database relation របស់អ្នក

    //     return response()->json([
    //         'status' => 'success',
    //         'token' => $result['token'],
    //         'user' => $result['user'],
    //         'store' => $store ? [                   // 👈 ថែម Key 'store' នេះចូល
    //             'id' => $store->id,
    //             'name' => $store->name,
    //             'logo' => $store->logo,
    //         ] : null
    //     ], 200);
    // }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|unique:users',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

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

    public function refreshToken(Request $request)
    {
        $newToken = $this->authService->refreshToken($request->user());

        return response()->json([
            'status' => 'success',
            'token' => $newToken,
        ], 200);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
