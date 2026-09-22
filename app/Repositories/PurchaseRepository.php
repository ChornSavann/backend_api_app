<?php
namespace App\Repositories;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
class PurchaseRepository implements \App\RepositoryInterface\PurchaseRepositoryInterface
{
    public function createPurchase(array $data)
    {
        return \App\Models\Purchase::create($data);
    }

    public function createPurchaseItem(array $data)
    {
        return \App\Models\PurchaseItem::create($data);
    }

    public function updateProductStock(int $productId, int $quantity)
    {
        $product = \App\Models\Product::find($productId);
        if ($product) {
            $product->stock_quantity += $quantity;
            $product->save();
        }
    }
    public function getPurchaseById(int $purchaseId)
    {
        return \App\Models\Purchase::with('supplier','items.product')->find($purchaseId);
    }

    public function getAllPurchases()
    {
        return \App\Models\Purchase::with(['user','supplier', 'items'])->latest()->get();
    }

    public function deletePurchase(int $purchaseId)
    {
        $purchase = \App\Models\Purchase::find($purchaseId);
        if ($purchase) {
            // Delete associated purchase items first
            $purchase->items()->delete();
            // Then delete the purchase
            $purchase->delete();
        }
    }
    public function updatePurchase(int $purchaseId, array $data)
    {
        $purchase = \App\Models\Purchase::find($purchaseId);
        if ($purchase) {
            $purchase->update($data);
            return $purchase;
        }
        return null;
    }

    public function updatePurchaseItem(int $itemId, array $data)
    {
        $purchaseItem = \App\Models\PurchaseItem::find($itemId);
        if ($purchaseItem) {
            $purchaseItem->update($data);
            return $purchaseItem;
        }
        return null;
    }
}