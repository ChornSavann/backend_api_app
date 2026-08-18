<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interface\PurchaseServiceInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class PurchaseController extends Controller
{
    protected $purchaseService;

    public function __construct(PurchaseServiceInterface $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }


    public function index(): JsonResponse
    {
        try {
            $purchases = $this->purchaseService->getAllPurchases();
            return response()->json([
                'success' => true,
                'data'    => $purchases,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchases.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $purchase = $this->purchaseService->getPurchaseById($id);
            if (!$purchase) {
                return response()->json([
                    'success' => false,
                    'message' => 'Purchase not found.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data'    => $purchase,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve purchase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function create(): JsonResponse
    {
       
        return response()->json([
            'success' => true,
            'message' => 'Display form for creating a new purchase.',
        ], 200);
    }

    public function store(Request $request):JsonResponse
    {
        try {
            $result = $this->purchaseService->createPurchase($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Purchase recorded and stock updated successfully!',
                'data'    => $result['data'],
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process purchase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $result = $this->purchaseService->updatePurchase($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Purchase updated and stock adjusted successfully!',
                'data'    => $result['data'],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update purchase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->purchaseService->deletePurchase($id);
            return response()->json([
                'success' => true,
                'message' => 'Purchase deleted successfully!',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purchase.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}