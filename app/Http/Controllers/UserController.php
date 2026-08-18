<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Interface\UserServiceInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class UserController extends Controller
{
    private UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return response()->json([
            "success" => true,
            "message" => "Users retrieved successfully",
            "data" => $users
        ]);
    }

    public function show($id): JsonResponse
    {
        $user = $this->userService->getUserById($id);
        if ($user) {
            return response()->json([
                "success" => true,
                "message" => "User retrieved successfully",
                "data" => $user
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ], 404);
        }
    }

   public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->userService->createUser($validatedData, $request->file('image'));
        
        event(new Registered($user));

        return response()->json([
            "success" => true,
            "message" => "User created successfully.",
            "data" => $user
        ], 201);
    }

    

    public function update(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->userService->updateUser($id, $validatedData, $request->file('image'));

        if(!$user) {
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ], 404);
        }else {
            return response()->json([
                "success" => true,
                "message" => "User updated successfully",
                "data" => $user
            ], 200);
        }
       
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->userService->deleteUser($id);
        if ($deleted) {
            return response()->json([
                "success" => true,
                "message" => "User deleted successfully"
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "User not found"
            ], 404);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $user = $this->userService->loginUser($credentials);

        if ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "success" => true,
                "message" => "Login successful",
                "data" => [
                    "user" => $user,
                    "token" => $token
                ]
            ]);
        } else {
            return response()->json([
                "success" => false,
                "message" => "Invalid credentials or email and password are incorrect"
            ], 401);
        }
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $user = $request->user(); 
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'ពាក្យសម្ងាត់បច្ចុប្បន្នមិនត្រឹមត្រូវទេ'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'ពាក្យសម្ងាត់ត្រូវបានផ្លាស់ប្ដូរដោយជោគជ័យ'
        ], 200);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'អុីនធឺណិតនេះមិនមានក្នុងប្រព័ន្ធទេ'
            ], 422);
        }

    
        return response()->json([
            'success' => true,
            'message' => 'Instructions have been sent to your email.'
        ], 200);
    }

    public function updatePassword(Request $request): JsonResponse
    {
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }


        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញគណនីនេះទេ។'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'ពាក្យសម្ងាត់ត្រូវបានកែប្រែដោយជោគជ័យ។'
        ], 200);
    }

    public function handleGoogleApiLogin(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string', // Token ដែលបានពី Flutter google_sign_in
        ]);

        try {
            // ប្រើប្រាស់ Socialite ដើម្បីទាញយកព័ត៌មាន User ពី Google Token
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->access_token);

            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $googleId = $googleUser->getId();

            // ឆែកមើលថាមាន User នេះក្នុង Database ឬยัง
            $user = User::where('email', $email)->first();

            if (!$user) {
                // បើមិនទាន់មាន បង្កើត Account ថ្មីស្វ័យប្រវត្តិ
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(16)), // Random Password
                ]);
            } else {
                // បើមានហើយ ធ្វើបច្ចុប្បន្នភាព google_id ទុកបើចាំបាច់
                $user->update(['google_id' => $googleId]);
            }

            // បង្កើត Sanctum Token សម្រាប់ផ្ញើត្រឡប់ទៅ Flutter វិញ
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Google Login Successfully',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Google Token or Connection Error',
                'error' => $e->getMessage()
            ], 401);
        }
    }
}