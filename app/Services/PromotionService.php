<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\Interface\PromotionServiceInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\RepositoryInterface\PromotionInterface;

class PromotionService implements PromotionServiceInterface
{
    protected PromotionInterface $promotionRepository;

    public function __construct(PromotionInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    public function getAllPromotions(): Collection
    {
        return $this->promotionRepository->getAllWithProduct();
    }

    public function getAllProducts(): Collection
    {
        return $this->promotionRepository->getAllProducts();
    }

    public function getPromotionById(int $id): ?Promotion
    {
        return $this->promotionRepository->findById($id);
    }

    public function createPromotion(array $data): Promotion
    {
        return DB::transaction(function () use ($data) {
            $productId = $data['product_id'];
            $promoQty = $data['quantity'] ?? 0;

            $product = Product::lockForUpdate()->find($productId);

            if ($promoQty > 0 && $product->stock_quantity < $promoQty) {
                throw new HttpException(400, 'Insufficient product stock to allocate for promotion.');
            }

            if ($promoQty > 0) {
                $product->stock_quantity -= $promoQty;
                $product->save();
            }

            $data['promotion_code'] = strtoupper($data['promotion_code']);
            $data['quantity'] = $promoQty;
            $data['is_active'] = $data['is_active'] ?? true;

            $promotion = $this->promotionRepository->create($data);
            return $promotion->load('product');
        });
    }

    public function updatePromotion(int $id, array $data): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new HttpException(404, 'Promotion not found.');
        }

        return DB::transaction(function () use ($promotion, $data) {
            $product = Product::lockForUpdate()->find($promotion->product_id);

            $newIsActive = isset($data['is_active']) ? $data['is_active'] : $promotion->is_active;
            $oldQty = $promotion->quantity;
            $newQty = isset($data['quantity']) ? $data['quantity'] : $oldQty;

            if (($promotion->is_active && !$newIsActive) || (isset($data['quantity']) && $newQty < $oldQty)) {
                $diff = $promotion->is_active && !$newIsActive ? $oldQty : ($oldQty - $newQty);
                if ($product) {
                    $product->stock_quantity += $diff;
                    $product->save();
                }
                if ($promotion->is_active && !$newIsActive) {
                    $newQty = 0;
                }
            } else if (isset($data['quantity']) && $newQty > $oldQty) {
                $diff = $newQty - $oldQty;
                if ($product->stock_quantity < $diff) {
                    throw new HttpException(400, 'Insufficient main product stock to increase promotion quantity.');
                }
                $product->stock_quantity -= $diff;
                $product->save();
            }

            $data['quantity'] = $newQty;
            $data['is_active'] = $newIsActive;

            $this->promotionRepository->update($promotion, array_merge($promotion->toArray(), $data));
            return $promotion->load('product');
        });
    }

    public function updateActiveStatus(int $id): Promotion
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new HttpException(404, 'Promotion not found.');
        }

        $promotion->is_active = !$promotion->is_active;
        $this->promotionRepository->update($promotion, ['is_active' => $promotion->is_active]);

        return $promotion;
    }

    public function deletePromotion(int $id): bool
    {
        $promotion = $this->promotionRepository->findById($id);
        if (!$promotion) {
            throw new HttpException(404, 'Promotion not found.');
        }

        return DB::transaction(function () use ($promotion) {
            if ($promotion->is_active && $promotion->quantity > 0) {
                $product = Product::lockForUpdate()->find($promotion->product_id);
                if ($product) {
                    $product->stock_quantity += $promotion->quantity;
                    $product->save();
                }
            }

            return $this->promotionRepository->delete($promotion);
        });
    }

    public function expirePromotions(): int
    {
        return $this->promotionRepository->expirePromotions();
    }
}