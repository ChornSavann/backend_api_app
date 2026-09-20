<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interface\ExpenseServiceInterface;
use Illuminate\Http\JsonResponse;

use  Carbon\Carbon;

class ExpenseController extends Controller
{
    protected $expenseService;
    public function __construct(ExpenseServiceInterface $expenseService)
    {
        $this->expenseService = $expenseService;
    }
    public function index(): JsonResponse
    {
        $expense = $this->expenseService->getAll();
        return response()->json([
            'success' => true,
            'message' => 'Expense List',
            'data' => $expense
        ]);
    }

    public function getAllExpensetype(): JsonResponse
    {
        $expense = $this->expenseService->getAllexpensetype();
        return response()->json([
            'success' => true,
            'message' => 'Expense Type List',
            'data' => $expense
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        if (isset($data['expense_date'])) {
            // 🟢 ប្រើ parse() ជំនួសឱ្យ createFromFormat() ដើម្បីឱ្យវាស្គាល់គ្រប់ទម្រង់ដែលផ្ញើមកពី Flutter ដោយស្វ័យប្រវត្តិ
            $data['expense_date'] = Carbon::parse($data['expense_date'])->format('Y-m-d');
        }
        $expense = $this->expenseService->create($data);
        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully',
            'data' => $expense
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->all();
        if (isset($data['expense_date'])) {
            try {

                $data['expense_date'] = Carbon::parse($data['expense_date'])->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }
        $expense = $this->expenseService->update($data, $id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data' => $expense
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $expense = $this->expenseService->findById($id);
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Expense details',
            'data' => $expense
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->expenseService->delete($id);
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }
}
