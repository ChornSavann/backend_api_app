<?php

namespace App\Services\Interface;

use App\Models\Order;

interface OrderServiceInterface
{
  public function getAllOrders();
  public function getOrderById($id);
  public function createOrder(array $data): Order;
  public function updateOrder($id, array $data);
  public function deleteOrder($id);
  public function createPayment(array $data): void;
  public function createOrderDetail(array $data): void;
  public function showInvoice($orderId);
  public function processOrder(array $data);
  public function cancelDelivery($deliveryId);
  public function fetchDeliveryOrders();
  public function updateDeliveryStatusProcess($id, string $status);
  public function completeDeliveryProcess($id, array $validatedData);
}
