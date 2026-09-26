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
use App\Models\Delivery;
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

            
            $customer = null;
            if (!empty($data['customer_id'])) {
                $customer = Customer::find($data['customer_id']);
            }

            if (!$customer) {
                $customer = Customer::updateOrCreate(
                    [
                        'phone' => $data['customer_phone'] ?? '000000000'
                    ],
                    [
                        'name'    => $data['customer_name'] ?? 'Guest',
                        'email'   => $data['customer_email'] ?? ('guest_' . time() . '@example.com'),
                        'address' => $data['customer_address'] ?? null,
                        'points'  => $data['customer_point'] ?? 1,
                    ]
                );
            }

            
            $order = Order::create([
                'order_number'    => $data['order_number'],
                'customer_id'     => $customer->id ?? null,
                'user_id'         => $data['user_id'],
                'subtotal'        => $data['subtotal'],
                'discount_amount' => $data['discount_amount'] ?? 0.00,
                'tax_amount'      => $data['tax_amount'] ?? 0.00,
                'total_amount'    => $data['total_amount'],
                'order_type'      => $data['order_type'] ?? 'dine_in', 
                'status'          => 'completed',
            ]);

            
            if (($data['order_type'] ?? '') === 'delivery') {
                Delivery::create([
                    'order_id'         => $order->id,
                    'pickup_address'   => $data['pickup_address'] ?? 'Store Address',
                    'delivery_address' => $data['delivery_address'] ?? $customer->address,
                    'delivery_fee'     => $data['delivery_fee'] ?? 0.00,
                    'delivery_partner' => $data['delivery_partner'] ?? 'Grab Express',
                    'receiver_name'    => $data['receiver_name'] ?? $customer->name,
                    'receiver_phone'   => $data['receiver_phone'] ?? $customer->phone,
                    'note'             => $data['note'] ?? null,
                    'status'           => 'pending', 
                ]);
            }

            
            $invoice = Invoice::create([
                'order_id'       => $order->id,
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

            $orderLoaded = $order->load(['details', 'payment', 'customer', 'delivery']);
            // $this->sendInvoiceToTelegram($orderLoaded, $invoice); 

            return $orderLoaded;
        });
    }

    public function showInvoice($orderId)
    {
        return $this->orderRepository->showInvoice($orderId);
    }


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


    // protected function sendInvoiceToTelegram($order, $invoice)
    // {
    //     $token = env('TELEGRAM_BOT_TOKEN');
    //     $chatId = env('TELEGRAM_CHAT_ID');

    //     if (!$token || !$chatId) {
    //         Log::warning("Telegram Token or Chat ID is missing.");
    //         return;
    //     }

    //     $cashierName = $order->user->name ?? 'System Admin';
    //     $amountPaid  = $order->payment->amount ?? 0.00;
    //     $changeAmount = $order->payment->change_amount ?? 0.00;

    //     $message  = "<b>🛍 POS SYSTEM - វិក្កយបត្រថ្មី (NEW INVOICE)</b>\n";
    //     $message .= "━━━━━━━━━━━━━━━━━━━━━\n";
    //     $message .= "📄 <b>លេខវិក្កយបត្រ:</b> <code>{$invoice->invoice_number}</code>\n";
    //     $message .= "📦 <b>លេខបញ្ជាទិញ:</b> <code>{$order->order_number}</code>\n";
    //     $message .= "👤 <b>អតិថិជន:</b> " . ($order->customer->name ?? 'Guest') . " (" . ($order->customer->phone ?? 'N/A') . ")\n";
    //     $message .= " cashier <b>អ្នកគិតលុយ:</b> {$cashierName}\n";
    //     $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    //     $message .= "🛒 <b>បញ្ជីទំនិញ (Items):</b>\n";

    //     foreach ($order->details as $item) {
    //         $message .= "▫️ <b>{$item->product_name}</b>\n";
    //         $message .= "   └ <i>{$item->quantity}x</i> @ \${$item->unit_price} = <b>\${$item->total_price}</b>\n";
    //     }

    //     $message .= "━━━━━━━━━━━━━━━━━━━━━━\n";
    //     $message .= "💰 <b>សរុបទឹកប្រាក់ (Grand Total):</b> <b>\${$invoice->grand_total}</b>\n";
    //     $message .= "💵 <b>ប្រាក់ទទួលបាន (Amount Paid):</b> \${$amountPaid}\n";
    //     $message .= "🪙 <b>ប្រាក់អាប់ (Change):</b> \${$changeAmount}\n";
    //     $message .= "💳 <b>ការទូទាត់ (Payment):</b> <i>{$invoice->payment_method}</i>\n";
    //     $message .= "✅ <b>ស្ថានភាព (Status):</b> <b>Paid (បានបង់ប្រាក់)</b>\n";
    //     $message .= "🕒 <b>ម៉ោងចេញវិក្កយបត្រ:</b> <code>" . \Carbon\Carbon::now('Asia/Phnom_Penh')->format('Y-m-d H:i:s') . "</code>";

    //     try {
    //         $response = Http::withoutVerifying()
    //             ->post("https://api.telegram.org/bot{$token}/sendMessage", [
    //                 'chat_id'    => $chatId,
    //                 'text'       => $message,
    //                 'parse_mode' => 'HTML',
    //             ]);

    //         if ($response->failed()) {
    //             Log::error("Telegram API Error: " . $response->body());
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Telegram Exception: " . $e->getMessage());
    //     }
    // }

    protected function sendInvoiceToTelegram($order, $invoice)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            Log::warning("Telegram Token or Chat ID is missing.");
            return;
        }

        $cashierName  = $order->user->name ?? 'System Admin';
        $amountPaid   = $order->payment->amount ?? 0.00;
        $changeAmount = $order->payment->change_amount ?? 0.00;
        $customerName = $order->customer->name ?? 'Guest';
        $customerPhone = $order->customer->phone ?? 'N/A';
        $paymentMethod = strtoupper($invoice->payment_method);
        $timeNow = \Carbon\Carbon::now('Asia/Phnom_Penh')->format('d/m/Y | H:i:s');


        $message  = "🏢 <b>Khmer Store - វិក្កយបត្រថ្មី</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📄 <b>វិក្កយបត្រ :</b> <code>{$invoice->invoice_number}</code>\n";
        $message .= "🛒 <b>បញ្ជាទិញ :</b> <code>{$order->order_number}</code>\n";
        $message .= "👤 <b>អតិថិជន :</b> {$customerName} ({$customerPhone})\n";
        $message .= " cashier <b>អ្នកគិតលុយ :</b> {$cashierName}\n";
        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📦 <b>បរិយាយទំនិញ :</b>\n";

        foreach ($order->details as $index => $item) {
            $no = $index + 1;
            $message .= "<b>{$no}. {$item->product_name}</b>\n";
            $message .= "    └ <i>{$item->quantity}</i> x \${$item->unit_price} = <b>\${$item->total_price}</b>\n";
        }

        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 <b>សរុបទឹកប្រាក់ :</b> \${$invoice->grand_total}\n";
        $message .= "💵 <b>ប្រាក់បានទទួល :</b> \${$amountPaid}\n";
        if ($changeAmount > 0) {
            $message .= "🪙 <b>ប្រាក់អាប់ជូន :</b> \${$changeAmount}\n";
        }
        $message .= "💳 <b>ទូទាត់តាម :</b> <code>{$paymentMethod}</code>\n";
        $message .= "✅ <b>ស្ថានភាព :</b> <b>PAID (បានបង់ប្រាក់)</b>\n";
        $message .= "━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🕒 <i>កាលបរិច្ឆេទ : {$timeNow}</i>";

        try {
            $response = Http::withoutVerifying()
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id'    => $chatId,
                    'text'       => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            if ($response->failed()) {
                Log::error("Telegram API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Telegram Exception: " . $e->getMessage());
        }
    }

    public function fetchDeliveryOrders()
    {
        return $this->orderRepository->getDeliveryOrders();
    }

    public function updateDeliveryStatusProcess($id, string$status)
    {
        $delivery = $this->orderRepository->findById($id);

        if (!$delivery) {
            return [
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យការដឹកជញ្ជូន (Delivery) នេះទេ។',
                'status' => 404
            ];
        }

        $delivery =$this->orderRepository->updateStatus($delivery,$status);

        if ($status === 'completed') {$order = $this->orderRepository->findOrderById($delivery->order_id);
            if ($order) {
                $this->orderRepository->updateOrderStatus($order, 'completed');
            }
        }

        return [
            'success' => true,
            'message' => 'Delivery status updated successfully.',
            'data' => $delivery,
            'status' => 200
        ];
    }

    public function completeDeliveryProcess($id, array$validatedData)
    {
        $order = $this->orderRepository->findOrderById($id);

        if (!$order) {
            return [
                'success' => false,
                'message' => 'រកមិនឃើញ Order នេះទេ',
                'status' => 404
            ];
        }

        // 2. Update ស្ថានភាព Order ជា success ឬ completed
        $this->orderRepository->updateOrderStatus($order,$validatedData['status']);

        $payment = $this->orderRepository->updateOrCreatePayment($order->id, [
            'payment_method' => $validatedData['payment_method'],
            'amount' => $validatedData['amount_paid'],
            'change_amount' => $validatedData['change_amount'],
            'status' => $validatedData['status'],
        ]);

        return [
            'success' => true,
            'message' => 'ការទូទាត់ប្រាក់ និងកត់ត្រាការដឹកជញ្ជូនបានជោគជ័យ',
            'order' => $order,
            'payment' => $payment,
            'status' => 200
        ];
    }
    public function cancelDelivery($deliveryId)
    {
        $delivery = $this->orderRepository->findById($deliveryId);

        if (!$delivery) {
            return [
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យ Delivery នេះទេ',
                'status' => 404
            ];
        }

        DB::beginTransaction();
        try {
            // 1. Update Delivery Status មកជា cancelled
            $this->orderRepository->updateDeliveryStatus($delivery, 'cancelled');

            $order = $this->orderRepository->findOrderByDelivery($delivery);

            if ($order) {
                $customerId = $order->customer_id;
                $orderId = $order->id;

                // 2. ដាក់ Order Status មកជា cancelled
                $this->orderRepository->updateOrderOnCancel($order);

                // 3. បុក Stock ផលិតផលទំនិញចូលវិញ
                $this->orderRepository->restoreStock($order);

                // 4. លុប Customer
                $this->orderRepository->deleteCustomer($customerId);

                // 5. Update Order details មកជា 0
                $this->orderRepository->updateOrderDetails($orderId);

                // 6. Update Payments
                $this->orderRepository->updatePayments($orderId);

                // 7. Update Invoices
                $this->orderRepository->updateInvoices($orderId);

                // 8. Update InvoiceItems
                $this->orderRepository->updateInvoiceItems($orderId);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'បានបោះបង់ការដឹកជញ្ជូន និងកែប្រែទិន្នន័យ Order មកជា cancelled ជោគជ័យ',
                'status' => 200
            ];
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'មានបញ្ហា៖ ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }
}
