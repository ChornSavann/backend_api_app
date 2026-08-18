<?php
namespace App\RepositoryInterface;
use App\Models\Order;
interface OrderInterface
{
    public function getAllOrders();
    public function getOrderById($id);
    public function createOrder(array $data): Order;
    public function updateOrder($id, array $data);
    public function deleteOrder($id);
    public function createPayment(array $data): void;
    public function createOrderDetail(array $data): void;
    public function showInvoice($orderId);
}