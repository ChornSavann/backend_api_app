<?php
namespace App\RepositoryInterface;

interface PurchaseRepositoryInterface
{
    public function createPurchase(array $data);
    public function createPurchaseItem(array $data);
    public function updateProductStock(int $productId, int $quantity);
    public function getPurchaseById(int $purchaseId);
    public function getAllPurchases();
    public function deletePurchase(int $purchaseId);
    public function updatePurchase(int $purchaseId, array $data);
    public function updatePurchaseItem(int $itemId, array $data);

}