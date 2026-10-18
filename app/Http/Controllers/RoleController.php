<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();
        return response()->json([
            'success' => true,
            'message' => 'បញ្ជី Roles ទាំងអស់',
            'data'    => $roles
        ], 200);
    }

    public function getrolewithpermissions($id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $role 
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role មិនមាន',
                'error'   => $e->getMessage()
            ], 404);
        }
    }
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $role = Role::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role បានបង្កើតជោគជ័យ!',
                'data'    => $role
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការបង្កើត Role',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            return response()->json([
                'success' => true,
                'data'    => $role
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role មិនមាន',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role បានលុបជោគជ័យ!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការលុប Role',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $role = Role::findOrFail($id);
            $role->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role បានកែប្រែជោគជ័យ!',
                'data'    => $role
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកែប្រែ Role',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function assignRole(Request $request, $id): JsonResponse
    {
        $request->validate([
            'roleId' => 'required|exists:roles,id',
        ]);

        try {
            $user = User::findOrFail($id);
            $user->role_id = $request->roleId;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'បានកំណត់ Role ជូនបុគ្គលិកជោគជ័យ!',
                'data'    => $user->load('role')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកំណត់ Role',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
