<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use App\Services\Interface\OrderServiceInterface;
use App\Models\Invoice;
use App\Models\InvoiceItem;

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
        ],200);
    }

   
    public function create()
    {
        
    }

 

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
            ], 201);

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
    public function show(Order $order)
    {
        //
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
}