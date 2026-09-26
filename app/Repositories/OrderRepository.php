<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\InvoiceItem;
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
        return $this->orderModel->with([
            'details.product',
            'customer',
            'user',
            'payment',
            'delivery' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }
        ])
            ->where('status', '!=', 'cancelled')
            ->latest()
            ->get();
    }

    public function getOrderById($id)
    {

        $order = $this->orderModel->with(['details.product', 'customer', 'user', 'payment', 'delivery'])->find($id);

        if (!$order) {
            throw new \Exception("Order with ID {$id} not found.");
        }
        return $order;
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

    public function findById($deliveryId)
    {
        return Delivery::find($deliveryId);
    }

    public function getDeliveryOrders()
    {
        
        $orders = Order::with(['details.product', 'customer', 'user', 'payment', 'delivery'])
            ->where('order_type', 'delivery')
            ->latest()
            ->get();

        return $orders;
    }

    public function updateStatus(Delivery $delivery, string $status)
    {
        $delivery->status = $status;
        $delivery->save();
        return $delivery;
    }

    public function findOrderById(int $orderId)
    {
        return Order::find($orderId);
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        $order->status = $status;
        $order->save();
        return $order;
    }

    public function updateOrCreatePayment(int $orderId, array $data)
    {
        return Payment::updateOrCreate(
            ['order_id' => $orderId],
            [
                'payment_method' => $data['payment_method'],
                'amount' => $data['amount'],
                'change_amount' => $data['change_amount'],
                'status' => $data['status'],
            ]
        );
    }
    
    public function updateDeliveryStatus(Delivery $delivery, string $status)
    {
        $delivery->status = $status;
        $delivery->save();
        return $delivery;
    }

    public function findOrderByDelivery(Delivery $delivery)
    {
        return Order::find($delivery->order_id);
    }

    public function updateOrderOnCancel(Order $order)
    {
        $order->status = 'cancelled';
        $order->customer_id = null;
        $order->subtotal = 0.00;
        $order->discount_amount = 0.00;
        $order->tax_amount = 0.00;
        $order->total_amount = 0.00;
        $order->save();
    }

    public function restoreStock(Order $order)
    {
        $orderDetails = OrderDetail::where('order_id', $order->id)->get();
        foreach ($orderDetails as $detail) {
            $product = Product::find($detail->product_id);
            if ($product) {
                $product->stock_quantity += $detail->quantity;
                $product->save();
            }
        }
    }

    public function deleteCustomer(?int $customerId)
    {
        if ($customerId) {
            Customer::where('id', $customerId)->delete();
        }
    }

    public function updateOrderDetails(int $orderId)
    {
        OrderDetail::where('order_id', $orderId)->update([
            'unit_price' => 0.00,
            'total_price' => 0.00,
            'quantity' => 0
        ]);
    }

    public function updatePayments(int $orderId)
    {
        Payment::where('order_id', $orderId)->update([
            'amount' => 0.00,
            'change_amount' => 0.00,
            'status' => 'cancelled',
            'payment_method' => 'cancelled',
        ]);
    }

    public function updateInvoices(int $orderId)
    {
        Invoice::where('order_id', $orderId)->update([
            'subtotal' => 0.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'grand_total' => 0.00,
            'status' => 'cancelled',
            'payment_method' => 'cancelled',
        ]);
    }

    public function updateInvoiceItems(int $orderId)
    {
        $invoice = Invoice::where('order_id', $orderId)->first();
        if ($invoice) {
            InvoiceItem::where('invoice_id', $invoice->id)->update([
                'unit_price' => 0.00,
                'total_price' => 0.00,
                'quantity' => 0
            ]);
        }
    }
}
