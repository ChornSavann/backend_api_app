<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interface\ExpenseTypeServiceInterface;
use Illuminate\Http\JsonResponse;

class ExpenseTypeController extends Controller
{
    protected $expenseTypeService;
    public function __construct(ExpenseTypeServiceInterface $expenseTypeService)
    {
        $this->expenseTypeService = $expenseTypeService;
    }
    public function index(): jsonResponse
    {
        $expenseTypes = $this->expenseTypeService->getAll();
        return response()->json([
            'success' => true,
            'message' => 'Expense Type List',
            'data' => $expenseTypes
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $expenseType = $this->expenseTypeService->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Expense Type created successfully',
            'data' => $expenseType
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->all();
        $expenseType = $this->expenseTypeService->findById($id);
        if (!$expenseType) {
            return response()->json([
                'success' => false,
                'message' => 'Expense Type not found',
            ], 404);
        }
        $this->expenseTypeService->update($data, $id);
        return response()->json([
            'success' => true,
            'message' => 'Expense Type updated successfully',
            'data' => $expenseType
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $expenseType = $this->expenseTypeService->findById($id);
        if (!$expenseType) {
            return response()->json([
                'success' => false,
                'message' => 'Expense Type not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Expense Type details',
            'data' => $expenseType
        ]);
    }
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->expenseTypeService->delete($id);
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Expense Type not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Expense Type deleted successfully',
        ]);
    }
}
