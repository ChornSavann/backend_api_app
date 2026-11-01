<?php
namespace App\RepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\Promotion;
interface PromotionInterface
{
   public function getAllWithProduct(): Collection;
    public function getAllProducts(): Collection;
    public function findById(int $id): ?Promotion;
    public function create(array $data): Promotion;
    public function update(Promotion $promotion, array $data): bool;
    public function delete(Promotion $promotion): bool;
    public function expirePromotions(): int;
}