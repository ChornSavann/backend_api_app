<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Payment;
use App\Models\OrderDetail;
use App\Repositories\OrderRepository;
use App\Services\Interface\OrderServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderService implements OrderServiceInterface
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders()
    {
        return $this->orderRepository->getAllOrders();
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->getOrderById($id);
    }

    public function createOrder(array $data): Order
    {
        return $this->orderRepository->createOrder($data);
    }

    public function createOrderDetail(array $data): void
    {
        $this->orderRepository->createOrderDetail($data);
    }

    public function createPayment(array $data): void
    {
        Payment::create([
            'order_id'       => $data['order_id'],
            'payment_method' => $data['payment_method'],
            'amount'         => $data['amount'],
            'change_amount'  => $data['change_amount'],
            'status'         => 'success',
        ]);
    }

    public function updateOrder($id, array $data)
    {
        return $this->orderRepository->updateOrder($id, $data);
    }

    public function deleteOrder($id)
    {
        return $this->orderRepository->deleteOrder($id);
    }

  
    public function processOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            
            $customer = Customer::updateOrCreate(
                [
                    'phone' => $data['customer_phone'] ?? '000000000' 
                ], 
                [
                    'name'    => $data['customer_name'] ?? 'Guest', 
                    'email'   => $data['customer_email'] ?? ('guest_' . time() . '@example.com'),
                    'address' => $data['customer_address'] ?? null,
                    'points'   => $data['customer_point'] ?? 1,
                ]
            );

            $order = Order::create([
                'order_number'    => $data['order_number'],
                'customer_id'     => $customer->id ?? null,
                'user_id'         => $data['user_id'],
                'subtotal'        => $data['subtotal'],
                'discount_amount' => $data['discount_amount'] ?? 0.00,
                'tax_amount'      => $data['tax_amount'] ?? 0.00,
                'total_amount'    => $data['total_amount'],
                'status'          => 'completed',
            ]);

        
            $invoice = Invoice::create([
                'order_id'       => $data['order_id'] ?? $order->id,
                'invoice_number' => 'INV-' . $data['order_number'],
                'user_id'        => $data['user_id'],
                'subtotal'       => $data['subtotal'],
                'tax'            => $data['tax_amount'] ?? 0.00,
                'discount'       => $data['discount_amount'] ?? 0.00,
                'grand_total'    => $data['total_amount'],
                'payment_method' => $data['payment_method'],
                'status'         => 'paid',
            ]);

            foreach ($data['items'] as $item) {
                
                OrderDetail::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['product_id'],
                    'product_name' => $item['product_name'] ?? (Product::find($item['product_id'])->name ?? 'Unknown'),
                    'unit_price'   => $item['unit_price'],
                    'quantity'     => $item['quantity'],
                    'total_price'  => $item['total_price'],
                ]);

            
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total_price' => $item['total_price'],
                ]);

                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->stock_quantity -= $item['quantity'];
                    $product->save();
                }
            
                StockMovement::create([
                    'product_id'   => $item['product_id'],
                    'user_id'      => $data['user_id'],
                    'type'         => 'out',
                    'quantity'     => $item['quantity'],
                    'reference_no' => $data['order_number'],
                    'note'         => 'Sale via POS Order',
                ]);
            }

        
            Payment::create([
                'order_id'       => $order->id,
                'payment_method' => $data['payment_method'],
                'amount'         => $data['amount_paid'],
                'change_amount'  => $data['change_amount'],
                'status'         => 'success',
            ]);
            
            // return $order->load(['details', 'payment']);
            // 🚀 បន្ថែមមុខងារផ្ញើ Invoice ទៅកាន់ Telegram Bot នៅទីនេះ
            $orderLoaded = $order->load(['details', 'payment', 'customer']);
            $this->sendInvoiceToTelegram($orderLoaded, $invoice);

            return $orderLoaded;
        });
    }


    public function showInvoice($orderId)
    {
        return $this->orderRepository->showInvoice($orderId);
    }
        // --- Helper Methods សម្រាប់ Business Logic ---

    protected function createOrUpdateCustomer(array $data)
    {
        return Customer::updateOrCreate(
            [
                'phone' => $data['customer_phone'] ?? '000000000'
            ], 
            [
                'name'    => $data['customer_name'] ?? 'Guest',
                'email'   => $data['customer_email'] ?? ('guest_' . time() . '@example.com'),
                'address' => $data['customer_address'] ?? null,
            ]
        );
    }

    protected function prepareOrderData(array $data, $customerId)
    {
        return [
            'order_number'    => $data['order_number'],
            'customer_id'     => $customerId,
            'user_id'         => $data['user_id'],
            'subtotal'        => $data['subtotal'],
            'discount_amount' => $data['discount_amount'] ?? 0.00,
            'tax_amount'      => $data['tax_amount'] ?? 0.00,
            'total_amount'    => $data['total_amount'],
            'status'          => 'completed',
        ];
    }

    protected function createInvoice(array $data, $order)
    {
        return Invoice::create([
            'invoice_number' => 'INV-' . $order->order_number,
            'user_id'        => $order->user_id,
            'subtotal'       => $order->subtotal,
            'tax'            => $order->tax_amount ?? 0.00,
            'discount'       => $order->discount_amount ?? 0.00,
            'grand_total'    => $order->total_amount,
            'payment_method' => $data['payment_method'],
            'status'         => 'paid',
        ]);
    }

    protected function updateStockAndMovement(array $item, $userId, $orderNumber)
    {
        $product = Product::findOrFail($item['product_id']); 

        if ($product->stock_quantity < $item['quantity']) {
            throw new Exception("ផលិតផល {$product->name} មិនគ្រប់គ្រាន់ក្នុងស្តុកទេ។");
        }

        $product->decrement('stock_quantity', $item['quantity']); 

        StockMovement::create([
            'product_id'   => $product->id,
            'user_id'      => $userId,
            'type'         => 'out',
            'quantity'     => $item['quantity'],
            'reference_no' => $orderNumber,
            'note'         => 'Sale via POS Order',
        ]);
    }


    protected function sendInvoiceToTelegram($order, $invoice)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            \Log::warning("Telegram Token or Chat ID is missing.");
            return;
        }

        $cashierName = $order->user->name ?? 'System Admin';
        $amountPaid  = $order->payment->amount ?? 0.00;
        $changeAmount = $order->payment->change_amount ?? 0.00;

        $message  = "<b>🛍 POS SYSTEM - វិក្កយបត្រថ្មី (NEW INVOICE)</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📄 <b>លេខវិក្កយបត្រ:</b> <code>{$invoice->invoice_number}</code>\n";
        $message .= "📦 <b>លេខបញ្ជាទិញ:</b> <code>{$order->order_number}</code>\n";
        $message .= "👤 <b>អតិថិជន:</b> " . ($order->customer->name ?? 'Guest') . " (" . ($order->customer->phone ?? 'N/A') . ")\n";
        $message .= " cashier <b>អ្នកគិតលុយ:</b> {$cashierName}\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🛒 <b>បញ្ជីទំនិញ (Items):</b>\n";
        
        foreach ($order->details as $item) {
            $message .= "▫️ <b>{$item->product_name}</b>\n";
            $message .= "   └ <i>{$item->quantity}x</i> @ \${$item->unit_price} = <b>\${$item->total_price}</b>\n";
        }

        $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 <b>សរុបទឹកប្រាក់ (Grand Total):</b> <b>\${$invoice->grand_total}</b>\n";
        $message .= "💵 <b>ប្រាក់ទទួលបាន (Amount Paid):</b> \${$amountPaid}\n";
        $message .= "🪙 <b>ប្រាក់អាប់ (Change):</b> \${$changeAmount}\n";
        $message .= "💳 <b>ការទូទាត់ (Payment):</b> <i>{$invoice->payment_method}</i>\n";
        $message .= "✅ <b>ស្ថានភាព (Status):</b> <b>Paid (បានបង់ប្រាក់)</b>\n";
        $message .= "🕒 <b>ម៉ោងចេញវិក្កយបត្រ:</b> <code>" . \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d H:i:s') . "</code>";

        try {
            $response = Http::withoutVerifying()
                         ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                             'chat_id'    => $chatId,
                             'text'       => $message,
                             'parse_mode' => 'HTML',
                         ]);

            if ($response->failed()) {
                \Log::error("Telegram API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error("Telegram Exception: " . $e->getMessage());
        }
    }
}