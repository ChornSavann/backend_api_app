<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProductCount(): JsonResponse
    {
        $totalProducts = $this->productService->getCountProducts();
        return response()->json([
            'success' => true,
            'count' => $totalProducts
        ], 200);
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    public function show($id): JsonResponse
    {
        $product = $this->productService->getProductById($id);
        return response()->json([
            'success' => true,
            'data' => $product
        ], 200);
    }


    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'sku'            => 'required|string|unique:products,sku',
            'barcode'        => 'nullable|string|unique:products,barcode',
            'cost_price'     => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0',
            'stock_quantity' => 'required|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'category_id'    => 'required|exists:category,id',
            'brand_id'       => 'required|exists:brands,id',
            'unit_id'        => 'required|exists:units,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except('image');
            $data['slug'] = \Illuminate\Support\Str::slug($request->name);

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $product = $this->productService->createProduct($data);

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data'    => $product
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាមិនអាចបង្កើតផលិតផលបានឡើយ',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getCategorybyId(): JsonResponse
    {
        $categories = $this->productService->getCategorybyId();
        return response()->json([
            'success' => true,
            'data' => $categories
        ], 200);
    }

    public function getUnits(): JsonResponse
    {
        $units = $this->productService->getUnits();
        return response()->json([
            'success' => true,
            'data' => $units
        ], 200);
    }
     
    public function getBrands(): JsonResponse
    {
        $brands = $this->productService->getBrands();
        return response()->json([
            'success' => true,
            'data' => $brands
        ], 200);
    }
    
    public function getProductsByCategoryId($category_id): JsonResponse
    {

        if ($category_id == 0) {
            $products = Product::with('category')->get();
        } else {
            $products = Product::with('category')->where('category_id', $category_id)->get();
        }

        return response()->json([
            'success' => true,
            'data' => $products
        ], 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'sku'            => 'sometimes|required|string|unique:products,sku,' . $id,
            'barcode'        => 'nullable|string|unique:products,barcode,' . $id,
            'cost_price'     => 'sometimes|required|numeric|min:0',
            'selling_price'  => 'sometimes|required|numeric|min:0',
            'stock_quantity' => 'sometimes|required|numeric|min:0',
            'alert_quantity' => 'nullable|numeric|min:0',
            'category_id'    => 'sometimes|required|exists:category,id',
            'brand_id'       => 'sometimes|required|exists:brands,id',
            'unit_id'        => 'sometimes|required|exists:units,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $data = $request->except('image');

            if ($request->has('name')) {
                $data['slug'] = \Illuminate\Support\Str::slug($request->name);
            }

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $product = $this->productService->updateProduct($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data'    => $product
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាមិនអាចធ្វើបច្ចុប្បន្នភាពផលិតផលបានឡើយ',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            return response()->json([
                'success' => true,
                'message' => 'Product and its image deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
    }

    

    
}


