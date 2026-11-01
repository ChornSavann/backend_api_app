<?php

namespace App\RepositoryInterface;

use App\Models\Order;
use App\Models\Delivery;

interface OrderInterface
{
    public function getAllOrders();
    public function getOrderById(int $id);
    public function createOrder(array $data): Order;
    public function updateOrder(int $id, array $data);
    public function deleteOrder(int $id);
    public function createPayment(array $data): void;
    public function createOrderDetail(array $data): void;
    public function showInvoice(int $orderId);
    public function updateDeliveryStatus(Delivery $delivery, string $status);
    public function findOrderByDelivery(Delivery $delivery);
    public function updateOrderOnCancel(Order $order);
    public function restoreStock(Order $order);
    public function deleteCustomer(?int $customerId);
    public function updateOrderDetails(int $orderId);
    public function updatePayments(int $orderId);
    public function updateInvoices(int $orderId);
    public function updateInvoiceItems(int $orderId);
    public function getDeliveryOrders();
    public function updateStatus(Delivery $delivery, string $status);
    public function updateOrderStatus(Order $order, string $status);
    public function updateOrCreatePayment(int $orderId, array $data);

}
