<?php
namespace App\Repositories;
use App\Models\Promotion;
use App\RepositoryInterface\PromotionInterface;
use Illuminate\Support\Collection;
use App\Models\Product;
class PromotionRepository implements PromotionInterface
{
    public function getAllWithProduct(): Collection
    {
        return Promotion::with('product')->get();
    }

    public function getAllProducts(): Collection
    {
        return Product::all();
    }

    public function findById(int  $id): ?Promotion
    {
        return Promotion::with('product')->find($id);
    }

    public function create(array $data): Promotion
    {
        return Promotion::create($data);
    }

    public function update(Promotion $promotion, array $data): bool
    {
        return $promotion->update($data);
    }

    public function delete(Promotion $promotion): bool
    {
        return $promotion->delete();
    }

    public function expirePromotions(): int
    {
        $now = now();
        return Promotion::where('end_date', '<', $now)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}