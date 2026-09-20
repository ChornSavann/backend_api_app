<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Interface\StoreServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;

class StoreController extends Controller
{
    protected $storeService;

    public function __construct(StoreServiceInterface $storeService)
    {
        $this->storeService = $storeService;
    }


    public function index(): JsonResponse
    {
        $stores = $this->storeService->getAllStores();
        return response()->json([
            'success' => true,
            'data' => $stores
        ], 200);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('stores');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $validated['logo'] = 'stores/' . $filename;
        }

        $store = $this->storeService->createStore($validated);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully',
            'data' => $store
        ], 201);
    }

    // 🔍 មើលព័ត៌មាន Store តាម ID
    public function edit($id): JsonResponse
    {
        $store = $this->storeService->getStoreById($id);

        return response()->json([
            'success' => true,
            'data' => $store
        ], 200);
    }


    // public function update(Request $request, $id): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'name' => 'sometimes|string|max:255',
    //         'phone' => 'nullable|string|max:20',
    //         'address' => 'nullable|string',
    //         'website' => 'nullable|url',
    //         'email' => 'nullable|email',
    //         'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);


    //     $store = $this->storeService->getStoreById($id);

    //     if ($request->hasFile('logo')) {

    //         if ($store->logo && File::exists(public_path($store->logo))) {
    //             File::delete(public_path($store->logo));
    //         }

    //         $file = $request->file('logo');
    //         $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

    //         $destinationPath = public_path('stores');
    //         if (!File::exists($destinationPath)) {
    //             File::makeDirectory($destinationPath, 0755, true);
    //         }

    //         $file->move($destinationPath, $filename);
    //         $validated['logo'] = 'stores/' . $filename;
    //     }
    //     $store = $this->storeService->updateStore($id, $validated);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Store updated successfully',
    //         'data' => $store
    //     ], 200);
    // }

    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $store = $this->storeService->getStoreById($id);

        if ($request->hasFile('logo')) {
            if ($store->logo && File::exists(public_path($store->logo))) {
                File::delete(public_path($store->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('stores');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);
            $validated['logo'] = 'stores/' . $filename;
        }

        $store = $this->storeService->updateStore($id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully',
            'data' => $store
        ], 200);
    }




    public function destroy($id): JsonResponse
    {
        $this->storeService->deleteStore($id);

        return response()->json([
            'success' => true,
            'message' => 'Store and its logo deleted successfully'
        ], 200);
    }
}
