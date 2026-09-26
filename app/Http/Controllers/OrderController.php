<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\DB;
use App\Services\Interface\OrderServiceInterface;

use App\Models\Delivery;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }


    public function index()
    {
        $orders = $this->orderService->getAllOrders();
        return response()->json([
            'success' => true,
            'data'    => $orders
        ], 200);
    }


    public function create() {}


    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'order_number'   => 'required|string|unique:orders',
            'user_id'        => 'required|integer',
            'subtotal'       => 'required|numeric',
            'total_amount'   => 'required|numeric',
            'payment_method' => 'required|string',
            'amount_paid'    => 'required|numeric',
            'change_amount'  => 'required|numeric',
            'items'          => 'required|array|min:1',
        ]);

        try {
            $order = $this->orderService->processOrder($request->all());
            return response()->json([
                'success' => true,
                'message' => 'ការទូទាត់ប្រាក់ និងបង្កើតវិក្កយបត្របានជោគជ័យ!',
                'data'    => $order
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'មានបញ្ហា៖ ' . $e->getMessage()
            ], 500);
        }
    }

    public function showInvoice($id): JsonResponse
    {
        try {
            $invoice = $this->orderService->showInvoice($id);

            return response()->json([
                'success' => true,
                'message' => 'ទាញយកព័ត៌មានវិក្កយបត្របានជោគជ័យ',
                'data'    => $invoice
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញវិក្កយបត្រនេះទេ៖ ' . $e->getMessage()
            ], 404);
        }
    }

    public function show($id): JsonResponse
    {

        Log::info('Requested Order ID: ' . $id);

        try {
            $orderDetails = $this->orderService->getOrderById($id);

            return response()->json([
                'success' => true,
                'message' => 'ទាញយកព័ត៌មានលំអិតនៃការបញ្ជាទិញបានជោគជ័យ',
                'data'    => $orderDetails
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញព័ត៌មានលំអិតនៃការបញ្ជាទិញទេ៖ ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    
    public function getDeliveryOrders(): JsonResponse
    {
        $orders = $this->orderService->fetchDeliveryOrders();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // public function getDeliveryOrders()
    // {
    //     // 🟢 ទាញយក Order និង Delivery ទាំងអស់មក (រួមទាំង status cancelled ផង ដើម្បីឱ្យវាបង្ហាញក្នុង Tab Cancelled បាន)
    //     $orders = Order::with(['details.product', 'customer', 'user', 'payment', 'delivery'])
    //         ->where('order_type', 'delivery')
    //         ->latest()
    //         ->get();

    //     return response()->json([
    //         'success' => true,
    //         'data' => $orders
    //     ]);
    // }

    // 2. មុខងារសម្រាប់ Update Status របស់ Delivery (pending -> on_the_way -> completed)
    // public function updateDeliveryStatus(Request $request, $id)
    // {
    //     $request->validate([
    //         'status' => 'required|in:pending,preparing,picked_up,on_the_way,completed,cancelled'
    //     ]);

    //     $delivery = Delivery::find($id);

    //     if (!$delivery) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'រកមិនឃើញទិន្នន័យការដឹកជញ្ជូន (Delivery) នេះទេ។'
    //         ], 404);
    //     }

    //     $delivery->status = $request->status;
    //     $delivery->save();

    //     if ($request->status === 'completed') {
    //         $order = Order::find($delivery->order_id);
    //         if ($order) {
    //             $order->status = 'completed';
    //             $order->save();
    //         }
    //     }

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Delivery status updated successfully.',
    //         'data' => $delivery
    //     ], 200);
    // }

    // public function completeDelivery(Request $request, $id)
    // {
    //     // 1. ស្វែងរក Order តាម ID
    //     $order = Order::find($id);

    //     if (!$order) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'រកមិនឃើញ Order នេះទេ'
    //         ], 404);
    //     }

    //     // 2. Validate ទិន្នន័យដែលផ្ញើមកពី App
    //     $request->validate([
    //         'status' => 'required|string',         // ឧ. 'completed' ឬ 'success'
    //         'payment_method' => 'required|string', // ឧ. 'cash', 'khqr', 'card'
    //         'amount_paid' => 'required|numeric',   // ទឹកប្រាក់ទទួលបាន
    //         'change_amount' => 'required|numeric', // ប្រាក់អាប់
    //     ]);

    //     // 3. Update ស្ថានភាព Order ជា success ឬ completed
    //     $order->update([
    //         'status' => $request->status,
    //     ]);

    //     // 4. บันทึก ឬ Update ព័ត៌មានចូល Table `payments` តាម Model Payment
    //     $payment = Payment::updateOrCreate(
    //         ['order_id' => $order->id], // ស្វែងរកតាម order_id ប្រសិនបើមានរួចហើយ
    //         [
    //             'payment_method' => $request->payment_method,
    //             'amount' => $request->amount_paid,
    //             'change_amount' => $request->change_amount,
    //             'status' => $request->status,
    //         ]
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'ការទូទាត់ប្រាក់ និងកត់ត្រាការដឹកជញ្ជូនបានជោគជ័យ',
    //         'order' => $order,
    //         'payment' => $payment
    //     ], 200);
    // }

    // public function updateDeliveryPaymentAndStatus(Request $request, $deliveryId)
    // {
    //     $delivery = Delivery::find($deliveryId);
    //     if (!$delivery) {
    //         return response()->json(['success' => false, 'message' => 'រកមិនឃើញទិន្នន័យ Delivery នេះទេ'], 404);
    //     }

    //     DB::beginTransaction();
    //     try {
    //         // 1. Update Delivery Status មកជា cancelled
    //         $delivery->status = 'cancelled';
    //         $delivery->save();

    //         $order = Order::find($delivery->order_id);

    //         if ($order) {
    //             // 🟢 2. ដាក់ Order Status មកជា cancelled ដែរ (កន្លែងដែលខ្វះកាលពីមុន)
    //             $order->status = 'cancelled';

    //             // 3. បុក Stock ផលិតផលទំនិញចូលវិញ
    //             $orderDetails = OrderDetail::where('order_id', $order->id)->get();
    //             foreach ($orderDetails as $detail) {
    //                 $product = Product::find($detail->product_id);
    //                 if ($product) {
    //                     $product->stock_quantity += $detail->quantity;
    //                     $product->save();
    //                 }
    //             }

    //             // 4. លុប Customer (បើត្រូវការ)[cite: 4]
    //             if ($order->customer_id) {
    //                 Customer::where('id', $order->customer_id)->delete();
    //             }

    //             // 5. Update Order មកជា 0 និង Status cancelled[cite: 4]
    //             $order->customer_id = null;
    //             $order->subtotal = 0.00;
    //             $order->discount_amount = 0.00;
    //             $order->tax_amount = 0.00;
    //             $order->total_amount = 0.00;
    //             $order->save();

    //             // 6. Update OrderDetails មកជា 0[cite: 4]
    //             OrderDetail::where('order_id', $order->id)->update([
    //                 'unit_price' => 0.00,
    //                 'total_price' => 0.00,
    //                 'quantity' => 0
    //             ]);

    //             // 7. Update Payments មកជា 0 និង cancelled[cite: 4]
    //             Payment::where('order_id', $order->id)->update([
    //                 'amount' => 0.00,
    //                 'change_amount' => 0.00,
    //                 'status' => 'cancelled',
    //                 'payment_method' => 'cancelled',
    //             ]);

    //             // 8. Update Invoices មកជា 0 និង cancelled[cite: 4]
    //             Invoice::where('order_id', $order->id)->update([
    //                 'subtotal' => 0.00,
    //                 'tax' => 0.00,
    //                 'discount' => 0.00,
    //                 'grand_total' => 0.00,
    //                 'status' => 'cancelled',
    //                 'payment_method' => 'cancelled',
    //             ]);

    //             // 9. Update InvoiceItems មកជា 0[cite: 4]
    //             $invoice = Invoice::where('order_id', $order->id)->first();
    //             if ($invoice) {
    //                 InvoiceItem::where('invoice_id', $invoice->id)->update([
    //                     'unit_price' => 0.00,
    //                     'total_price' => 0.00,
    //                     'quaintity' => 0
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'បានបោះបង់ការដឹកជញ្ជូន និងកែប្រែទិន្នន័យ Order មកជា cancelled ជោគជ័យ'
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'មានបញ្ហា៖ ' . $e->getMessage()
    //         ], 500);
    //     }
    // }


    public function updateDeliveryStatus(Request $request, $id):JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,picked_up,on_the_way,completed,cancelled'
        ]);

        $result = $this->orderService->updateDeliveryStatusProcess($id, $request->status);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['data']
        ], $result['status']);
    }

    public function completeDelivery(Request $request, $id):JsonResponse
    {
       
        $request->validate([
            'status' => 'required|string',         
            'payment_method' => 'required|string', 
            'amount_paid' => 'required|numeric',   
            'change_amount' => 'required|numeric', 
        ]);

        $result = $this->orderService->completeDeliveryProcess($id, $request->all());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], $result['status']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'order' => $result['order'],
            'payment' => $result['payment']
        ], $result['status']);
    }

    public function updateDeliveryPaymentAndStatus(Request $request, $deliveryId): JsonResponse
    {

        $result = $this->orderService->cancelDelivery($deliveryId);
        $isSuccess = $result['success'] ?? false;
        $statusCode = $result['status'] ?? ($isSuccess ? 200 : 400);

        return response()->json([
            'success' => $isSuccess,
            'message' => $result['message'] ?? '',
            'data'    => $result['data'] ?? null
        ], $statusCode);
    }
}
