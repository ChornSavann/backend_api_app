<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpireProduct;
use Illuminate\Http\JsonResponse;


class ExpireProductController extends Controller
{
    public function index():JsonResponse
    {
        $expireProducts = ExpireProduct::with('product')->get();
        return response()->json([
            'success' => true,
            'message' => 'Expire products retrieved successfully.',
            'data' => $expireProducts
        ]);
    }
    public function getAvailableProductsForExpire():JsonResponse
    {
        try {
        
            $recordedProductIds = \App\Models\ExpireProduct::pluck('product_id')->toArray();
            $availableProducts = \App\Models\Product::whereNotIn('id', $recordedProductIds)->get();

            return response()->json([
                'success' => true,
                'data'    => $availableProducts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(int $id): JsonResponse
    {
        $expireProduct = ExpireProduct::with('product')->find($id);
        if (!$expireProduct) {
            return response()->json([
                'success' => false,
                    'message' => 'Expire product not found.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Expire product retrieved successfully.',
                'data' => $expireProduct
            ]);
        }
        public function getExpiringProductsFromTable(): JsonResponse
        {

            $expiringItems = ExpireProduct::with('product')
                ->where('expire_date', '<=', now()->addDays(30))
                ->where('quantity', '>', 0)
            ->orderBy('expire_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Expiring products retrieved successfully.',
            'data' => $expiringItems
        ]);
    }

    public function storeExpireProduct(Request $request): JsonResponse
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'start_date'   => 'required|date',
            'expire_date'  => 'required|date|after_or_equal:start_date',
            'batch_number' => 'nullable|string',
        ]);

        try {
    
            $expireProduct = ExpireProduct::create([
                'product_id'   => $request->product_id,
                'batch_number' => $request->batch_number ?? 'BATCH-' . time(),
                'quantity'     => $product->stock_quantity ?? 0,
                'start_date'   => $request->start_date,
                'expire_date'  => $request->expire_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'បានបញ្ចូលស្តុក និងថ្ងៃផុតកំណត់ជោគជ័យ!',
                'data'    => $expireProduct
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការរក្សាទុក: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id):JsonResponse
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'quantity'     => 'nullable|integer|min:1',
            'start_date'   => 'required|date',
            'expire_date'  => 'required|date|after_or_equal:start_date',
            'batch_number' => 'nullable|string',
        ]);

        try {
            $expireProduct = ExpireProduct::findOrFail($id);

            $expireProduct->update([
                'product_id'   => $request->product_id,
                'batch_number' => $request->batch_number ?? $expireProduct->batch_number,
                'start_date'   => $request->start_date,
                'expire_date'  => $request->expire_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Expire product updated successfully.',
                'data'    => $expireProduct
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating expire product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id):JsonResponse
    {
        try {
            $expireProduct = ExpireProduct::findOrFail($id);
            $expireProduct->delete();

            return response()->json([
                'success' => true,
                'message' => 'Expire product deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting expire product: ' . $e->getMessage()
            ], 500);
        }
    }
}