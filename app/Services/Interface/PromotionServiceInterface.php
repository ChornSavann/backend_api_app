<?php

namespace App\Services\Interface;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection;

interface PromotionServiceInterface
{
    public function getAllPromotions(): Collection;
    public function getAllProducts(): Collection;
    public function getPromotionById(int $id): ?Promotion;
    public function createPromotion(array $data): Promotion;
    public function updatePromotion(int $id, array $data): Promotion;
    public function updateActiveStatus(int $id): Promotion;
    public function deletePromotion(int $id): bool;
    public function expirePromotions(): int;
}