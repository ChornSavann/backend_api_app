<?php

namespace App\Services\Interface;

use App\Models\Order;

interface OrderServiceInterface
{
  public function getAllOrders();
  public function getOrderById(int $id);
  public function createOrder(array $data): Order;
  public function updateOrder(int $id, array $data);
  public function deleteOrder(int $id);
  public function createPayment(array $data): void;
  public function createOrderDetail(array $data): void;
  public function showInvoice(int $orderId);
  public function processOrder(array $data);
  public function cancelDelivery(int $deliveryId);
  public function fetchDeliveryOrders();
  public function updateDeliveryStatusProcess(int $id, string $status);
  public function completeDeliveryProcess(int  $id, array $validatedData);
}
