<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\RepositoryInterface\OrderInterface;

class OrderRepository implements OrderInterface
{
    protected $orderModel;

    public function __construct(Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }

    public function getAllOrders()
    {
        return $this->orderModel->with(['details', 'customer', 'user', 'payment'])->get();
    }

    public function getOrderById($id)
    {
        return $this->orderModel->with(['details', 'customer', 'user', 'payment'])->findOrFail($id);
    }


    public function createOrder(array $data): Order
    {
        return $this->orderModel->create($data);
    }

    public function createPayment(array $data): void
    {
        Payment::create($data);
    }

    public function createOrderDetail(array $data): void
    {
        OrderDetail::create($data);
    }
    
    public function updateOrder($id, array $data)
    {
        $order = $this->orderModel->findOrFail($id);
        $order->update($data);
        return $order;
    }

    public function deleteOrder($id)
    {
        $order = $this->orderModel->findOrFail($id);
        return $order->delete();
    }

    public function showInvoice($orderId)
    {
        return Order::with(['invoice.items.product', 'customer', 'user', 'payment'])
                    ->findOrFail($orderId);
    }
}