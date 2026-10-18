<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::all();
        return response()->json([
            'success' => true,
            'message' => 'បញ្ជីសិទ្ធិទាំងអស់',
            'data'    => $permissions
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        try {

            $permission = Permission::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission បានបង្កើតជោគជ័យ!',
                'data'    => $permission
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការបង្កើត Permission',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
 

    public function edit($id): JsonResponse
    {
        try {

            $permission = Permission::findOrFail($id);

            return response()->json([
                'success' => true,
                'data'    => $permission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញ Permission នេះទេ',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
            'description' => 'nullable|string',
        ]);

        try {
            $permission = Permission::findOrFail($id);
            $permission->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permission បានកែប្រែជោគជ័យ!',
                'data'    => $permission
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការកែប្រែ Permission',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permission បានលុបជោគជ័យ!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការលុប Permission',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return response()->json([
                'success' => true,
                'data'    => $role->permissions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role មិនមាន',
                'error'   => $e->getMessage()
            ], 404);
        }
    }


    public function assignPermissionToRole(Request $request, $id): JsonResponse
    {
        $request->validate([
            'permission_ids'   => 'present|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);

        // 🚀 sync() នឹងស្វ័យប្រវត្តិលុបចោលទាំងអស់ប្រសិនបើ $request->permission_ids ជា Array ទទេ []
        $role->permissions()->sync($request->permission_ids);

        return response()->json([
            'success' => true,
            'message' => 'បានកែសម្រួលសិទ្ធិប្រគល់ជូន Role ជោគជ័យ!'
        ]);
    }
}
