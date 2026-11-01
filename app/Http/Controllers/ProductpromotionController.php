<?php

namespace App\Http\Controllers;

use App\Requests\StorePromotionRequest;
use App\Requests\UpdatePromotionRequest;
use App\Services\Interface\PromotionServiceInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductpromotionController extends Controller
{
    protected PromotionServiceInterface $promotionService;

    public function __construct(PromotionServiceInterface $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    public function index()
    {
        $promotions = $this->promotionService->getAllPromotions();
        return response()->json([
            'success' => true,
            'message' => 'Product promotions retrieved successfully.',
            'data' => $promotions
        ]);
    }

    public function getallproduct()
    {
        $products = $this->promotionService->getAllProducts();
        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products
        ]);
    }

    public function store(StorePromotionRequest $request)
    {
        try {
            $promotion = $this->promotionService->createPromotion($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Product promotion created and stock allocated successfully.',
                'data' => $promotion
            ], 201);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        }
    }

    public function expirepromotion()
    {
        try {
            $affectedRows = $this->promotionService->expirePromotions();
            return response()->json([
                'success' => true,
                'message' => "$affectedRows promotions have been expired successfully.",
                'expired_count' => $affectedRows
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to expire promotions: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id)
    {
        $promotion = $this->promotionService->getPromotionById($id);

        if (!$promotion) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product promotion retrieved successfully.',
            'data' => $promotion
        ]);
    }

    public function update(UpdatePromotionRequest $request,int $id)
    {
        try {
            $promotion = $this->promotionService->updatePromotion($id, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Product promotion updated and stock adjusted successfully.',
                'data' => $promotion
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        }
    }

    public function updateActive(int $id)
    {
        try {
            $promotion = $this->promotionService->updateActiveStatus($id);
            return response()->json([
                'success' => true,
                'message' => 'Promotion active status updated successfully.',
                'data' => $promotion
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->promotionService->deletePromotion($id);
            return response()->json([
                'success' => true,
                'message' => 'Product promotion deleted and remaining stock returned successfully.'
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getStatusCode());
        }
    }
}
