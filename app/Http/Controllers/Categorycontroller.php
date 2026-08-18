<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
class Categorycontroller extends Controller
{
    protected $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function index():JsonResponse
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json([
            'success' => true,
            'data' => $category
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->only(['name', 'description']);
        $category = $this->categoryService->createCategory($data);
        return response()->json([
            'success' => true,
            'data' => $category
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $data = $request->only(['name', 'description']);
        $category = $this->categoryService->updateCategory($id, $data);
        return response()->json([
            'success' => true,
            'data' => $category
        ], 200);
    }
    public function destroy($id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);
        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ], 200);
    }
}
