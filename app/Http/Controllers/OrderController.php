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
