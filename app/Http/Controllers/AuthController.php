<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\Request;
use App\Services\Interface\AuthServiceInterface;
use Illuminate\Validation\ValidationException;

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

        try {

            $result = $this->authService->login($request->email, $request->password);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
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

    public function createUser(Request $request)
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
