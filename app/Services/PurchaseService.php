<?php
namespace App\Services;

use App\Services\Interface\PurchaseServiceInterface;
use App\RepositoryInterface\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseService implements PurchaseServiceInterface
{
    protected $purchaseRepository;

    public function __construct(PurchaseRepositoryInterface $purchaseRepository)
    {
        $this->purchaseRepository = $purchaseRepository;
    }

    public function createPurchase(array $requestData)
    {
        DB::beginTransaction();
    
        try {
            $purchaseData = [
                'purchase_number' => $requestData['purchase_number'] ?? 'PUR-' . date('Ymd') . '-' . rand(1000, 9999),
                'supplier_id'     => $requestData['supplier_id'] ?? null,
                'user_id'         => $requestData['user_id'] ?? 1,
                'subtotal'        => $requestData['subtotal'],
                'discount'        => $requestData['discount'] ?? 0,
                'tax'             => $requestData['tax'] ?? 0,
                'total'           => $requestData['total'],
                'payment_method'  => $requestData['payment_method'] ?? 'cash',
                'status'          => $requestData['status'] ?? 'completed',
                'notes'           => $requestData['notes'] ?? null,
            ];

            $purchase = $this->purchaseRepository->createPurchase($purchaseData);

            if (!empty($requestData['items'])) 
            {
                foreach ($requestData['items'] as $item) 
                {
                    $this->purchaseRepository->createPurchaseItem([
                            'purchase_id'  => $purchase->id,
                            'product_id'   => $item['product_id'],
                            'product_name' => $item['product_name'],
                            'unit_cost'    => $item['unit_cost'],
                            'quantity'     => $item['quantity'],
                            'total_price'  => $item['total_price'],
                    ]);

                    $this->purchaseRepository->updateProductStock($item['product_id'], $item['quantity']);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'data'    => $this->purchaseRepository->getPurchaseById($purchase->id)
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    
    public function updatePurchase(int $purchaseId, array $requestData)
    {
        DB::beginTransaction();

        try {
            // ១. រកមើល Purchase ចាស់ជាមួយ Items របស់มัน
            $purchase = $this->purchaseRepository->getPurchaseById($purchaseId);
            if (!$purchase) {
                throw new Exception("Purchase not found.");
            }

            // ២. 🔄 ដកស្តុកចាស់ចេញពី Products វិញទាំងអស់ជាមុនសិន (Reverse old stock)
            foreach ($purchase->items as $oldItem) {
                
                $this->purchaseRepository->updateProductStock($oldItem->product_id, -$oldItem->quantity);
            }

            // ៣. លុប PurchaseItems ចាស់ចោលទាំងអស់
            \App\Models\PurchaseItem::where('purchase_id', $purchaseId)->delete();

    
            $purchaseData = [
                'purchase_number' => $requestData['purchase_number'] ?? $purchase->purchase_number,
                'supplier_id'     => $requestData['supplier_id'] ?? $purchase->supplier_id,
                'user_id'         => $requestData['user_id'] ?? $purchase->user_id,
                'subtotal'        => $requestData['subtotal'] ?? $purchase->subtotal,
                'discount'        => $requestData['discount'] ?? $purchase->discount,
                'tax'             => $requestData['tax'] ?? $purchase->tax,
                'total'           => $requestData['total'] ?? $purchase->total,
                'payment_method'  => $requestData['payment_method'] ?? $purchase->payment_method,
                'status'          => $requestData['status'] ?? $purchase->status,
                'notes'           => $requestData['notes'] ?? $purchase->notes,
            ];

            $this->purchaseRepository->updatePurchase($purchaseId, $purchaseData);

            if (!empty($requestData['items'])) {
                foreach ($requestData['items'] as $item) {
                    $this->purchaseRepository->createPurchaseItem([
                        'purchase_id'  => $purchaseId,
                        'product_id'   => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'unit_cost'    => $item['unit_cost'],
                        'quantity'     => $item['quantity'], // ចំនួនថ្មី (ឧ. 4)
                        'total_price'  => $item['total_price'],
                    ]);

                    // 🟢 បូកចំនួនថ្មីចូលទៅក្នុងស្តុកផលិតផលពិតប្រាកដ
                    $this->purchaseRepository->updateProductStock($item['product_id'], $item['quantity']);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'data'    => $this->purchaseRepository->getPurchaseById($purchaseId)
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function createPurchaseItem(array $data)
    {
        return $this->purchaseRepository->createPurchaseItem($data);
    }

    public function updateProductStock(int $productId, int $quantity)
    {
        return $this->purchaseRepository->updateProductStock($productId, $quantity);
    }

    public function getPurchaseById(int $purchaseId)
    {
        return $this->purchaseRepository->getPurchaseById($purchaseId);
    }

    public function getAllPurchases()
    {
        return $this->purchaseRepository->getAllPurchases();
    }

    public function deletePurchase(int $purchaseId)
    {
        return $this->purchaseRepository->deletePurchase($purchaseId);
    }

    public function updatePurchaseItem(int $itemId, array $data)
    {
        return $this->purchaseRepository->updatePurchaseItem($itemId, $data);
    }
}