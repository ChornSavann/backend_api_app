<?php

namespace App\Http\Controllers;

use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UnitsController extends Controller
{
    protected $unitService;

    public function __construct(\App\Services\Interface\UnitServiceInterface $unitService)
    {
        $this->unitService = $unitService;
    }
    
    public function index(): JsonResponse
    {
        $units = $this->unitService->getAllUnits();
        return response()->json([
            'success' => true,
            'data' => $units
        ], 200);
    }

    // សម្រាប់បង្ហាញ Form ក្នុង Web (ចំពោះ API ភាគច្រើនមិនបាច់ប្រើទេ តែទុកក៏បាន)
    public function create()
    {
        // មុខងារនេះជាទូទៅសម្រាប់ Web UI តែបើប្រើសម្រាប់ API ត្រូវຮັບ Request
        return response()->json(['message' => 'Create form endpoint'], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50', 
            'base_unit_id' => 'nullable|exists:units,id', 
            'operator' => 'nullable|string|in:*,/', 
            'value' => 'nullable|numeric|min:0',
        ]);

        $unit = $this->unitService->createUnit($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully',
            'data' => $unit
        ], 201);
    }


    public function show($id): JsonResponse
    {
        $unit = $this->unitService->getUnitById($id);
        
        return response()->json([
            'success' => true,
            'data' => $unit
        ], 200);
    }

    public function edit($id): JsonResponse
    {
        $unit = $this->unitService->getUnitById($id);
        
        return response()->json([
            'success' => true,
            'data' => $unit
        ], 200);
    }


    public function update(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50', 
            'base_unit_id' => 'nullable|exists:units,id', 
            'operator' => 'nullable|string|in:*,/', 
            'value' => 'nullable|numeric|min:0',
        ]);

        $unit = $this->unitService->updateUnit($id, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully',
            'data' => $unit
        ], 200);
    }

   
    public function destroy($id): JsonResponse
    {
        $this->unitService->deleteUnit($id);

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully'
        ], 200);
    }
}