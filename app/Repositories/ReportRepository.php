<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\RepositoryInterface\ReportInterface;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Expense;
use Illuminate\Support\Facades\Log;

class ReportRepository implements ReportInterface
{

    public function getReportData($filters)
    {
        $query = Order::with(['customer', 'user', 'details', 'payment']);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->latest()->get();
    }


    public function getReportSummary($filters)
    {
        $query = Order::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }
        return [
            'total_orders'    => $query->count(),
            'total_revenue'   => $query->sum('total_amount'),
            'total_discount'  => $query->sum('discount_amount'),
            'total_tax'       => $query->sum('tax_amount'),
        ];
    }



    public function DailyReport($filters)
    {
        $query = Order::with(['customer', 'payment', 'user', 'store', 'details.product']);

        $query->where('status', '!=', 'cancelled');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59'
            ]);
        } elseif (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        } else {
            $query->whereDate('created_at', now());
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }

    public function WeeklyReport($filters)
    {
        $query = Order::with(['customer', 'payment', 'details.product']);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        } else {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }

        return $query->orderBy('created_at', 'DESC')->get();
    }


    public function MonthlyReport($filters)
    {
        $query = Order::query();

        if (!empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year']);
        }

        return $query->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        )
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month', 'ASC')
            ->get();
    }


    public function YearlyReport($filters = [])
    {
        $query = Order::select(
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('COUNT(*) as total_orders'),
            DB::raw('SUM(total_amount) as total_revenue')
        );

        if (!empty($filters['start_year']) && !empty($filters['end_year'])) {
            $query->whereBetween(DB::raw('EXTRACT(YEAR FROM created_at)'), [$filters['start_year'], $filters['end_year']]);
        }

        return $query->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'year' => (int) $item->year,
                    'orders' => (int) $item->total_orders,
                    'revenue' => (float) $item->total_revenue,
                ];
            });
    }


    public function getReportById($id)
    {
        return Order::with(['customer', 'user', 'details.product', 'payment', 'invoice'])
            ->find($id);
    }


    public function saleReport($data)
    {
        $query = Order::with(['details.product', 'payment', 'customer']);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        if (!empty($data['payment_method'])) {
            $query->whereHas('payment', function ($q) use ($data) {
                $q->where('payment_method', $data['payment_method']);
            });
        }

        return $query->latest()->get();
    }


    public function historyReport($data)
    {

        $query = Order::with(['details.product', 'payment', 'customer', 'user']);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        if (!empty($data['customer_id'])) {
            $query->where('customer_id', $data['customer_id']);
        }

        return $query->latest()->get();
    }

    public function getProductsByMonth($year, $month)
    {
        return OrderDetail::with('product')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select(
                'product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('AVG(unit_price) as selling_price'),
                DB::raw('SUM(quantity * unit_price) as total_revenue')
            )
            ->groupBy('product_id')
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product->name ?? 'Unknown Product',
                    'qty' => (int) $item->total_qty,
                    'price' => (float) $item->selling_price,
                    'total' => (float) $item->total_revenue,
                    'image' => $item->product->image_url ?? null,
                ];
            });
    }



    public function topSellingProducts($data)
    {
        $limit = $data['limit'] ?? 6;

        $query = OrderDetail::query()
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->select(
                'order_details.product_id',
                'order_details.product_name',
                'products.image',
                DB::raw('SUM(order_details.quantity) as total_quantity_sold'),
                DB::raw('SUM(order_details.total_price) as total_revenue')
            );

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('order_details.created_at', [$data['start_date'], $data['end_date']]);
        }

        return $query->groupBy(
            'order_details.product_id',
            'order_details.product_name',
            'products.image'
        )
            ->orderByDesc('total_quantity_sold')
            ->limit($limit)
            ->get();
    }


    public function lowStockReport($data = [])
    {
        $query = Product::with(['category', 'brand', 'unit']);
        if (isset($data['use_alert_column']) && $data['use_alert_column'] == true) {
            $query->whereColumn('stock_quantity', '<=', 'alert_quantity');
        } else {

            $limitStock = $data['limit_stock'] ?? 10;
            $query->where('stock_quantity', '<=', $limitStock);
        }

        if (!empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        return $query->get();
    }


    public function purchaseHistoryReport($data)
    {
        $query = Purchase::with(['supplier', 'user', 'purchaseItems.product']);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        if (!empty($data['supplier_id'])) {
            $query->where('supplier_id', $data['supplier_id']);
        }

        return $query->latest()->get();
    }


    public function purchaseHistoryReportbyid($purchaseId)
    {
        return Purchase::with([
            'supplier',
            'user',
            'purchaseItems.product'
        ])
            ->where('id', $purchaseId)
            ->first();
    }


    public function customerHistoryReport($customerId, $data)
    {
        $query = Order::with(['details.product', 'payment', 'user'])
            ->where('customer_id', $customerId);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        return $query->latest()->get();
    }

    public function getAllcustomers()
    {
        return Customer::withSum('orders', 'total_amount')
            ->withCount('orders')
            ->latest()
            ->get();
    }

    public function totalSalesReport()
    {
        $totalSales = Order::sum('total_amount');
        $totalOrders = Order::count();
        $totalCustomers = Customer::count();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_customers' => $totalCustomers,
        ];
    }


    //  Profit & lose
    public function getTotalIncome($startDate, $endDate)
    {
        $query = Order::where('status', 'completed');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->sum('total_amount');
    }


    public function getGeneralExpenses($startDate, $endDate)
    {

        $expenseQuery = Expense::query();
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('expense_date', [$startDate, $endDate]);
        }
        $totalExpenses = $expenseQuery->sum('amount');
        $deliveryQuery = \App\Models\Delivery::query()
            ->whereIn('status', ['success', 'completed']);

        if ($startDate && $endDate) {
            $deliveryQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $totalDeliveryFees = $deliveryQuery->sum('delivery_fee');
        return $totalExpenses + $totalDeliveryFees;
    }

    public function getPurchaseExpenses($startDate, $endDate)
    {
        $query = Purchase::where('status', 'completed');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $query->sum('total');
    }


    public function getExpenseBreakdown($startDate, $endDate)
    {

        $expenses = Expense::with('expenseType')
            ->select('expense_type_id', DB::raw('SUM(amount) as total_amount'))
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('expense_date', [$startDate, $endDate]);
            })
            ->groupBy('expense_type_id')
            ->get();

        $deliveries = \App\Models\Delivery::query()
            ->select('delivery_partner', DB::raw('SUM(delivery_fee) as total_fee'))
            ->whereIn('status', ['success', 'completed'])
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
            })
            ->groupBy('delivery_partner')
            ->get();

        $breakdown = [];
        // ដាក់ទិន្នន័យ Expense ចូលមុន
        foreach ($expenses as $item) {
            $typeName = optional($item->expenseType)->name ?? 'ផ្សេងៗ';
            if (!isset($breakdown[$typeName])) {
                $breakdown[$typeName] = [
                    'total_amount' => 0.0,
                    'expense_type' => [
                        'name' => $typeName,
                        'image' => optional($item->expenseType)->image ?? null,
                    ]
                ];
            }
            $breakdown[$typeName]['total_amount'] += (float) $item->total_amount;
        }

        foreach ($deliveries as $delivery) {
            $partnerName = $delivery->delivery_partner ?? 'Delivery Service';
            if (!isset($breakdown[$partnerName])) {
                // បើក្នុង Expense មិនទាន់មាន Type ហ្នឹង គឺបង្កើតថ្មី
                $breakdown[$partnerName] = [
                    'total_amount' => 0.0,
                    'expense_type' => [
                        'name' => $partnerName,
                        'image' => null,
                    ]
                ];
            }

            $breakdown[$partnerName]['total_amount'] += (float) $delivery->total_fee;
        }

        return array_values($breakdown);
    }

    // public function getIncomeBreakdown($startDate, $endDate)
    // {
    //     $query = DB::table('order_details')
    //         ->join('orders', 'order_details.order_id', '=', 'orders.id')
    //         ->join('products', 'order_details.product_id', '=', 'products.id')
    //         ->join('category', 'products.category_id', '=', 'category.id')
    //         ->where('orders.status', 'completed');

    //     if ($startDate && $endDate) {
    //         $query->whereBetween('orders.created_at', [$startDate, $endDate]);
    //     }

    //     return $query
    //         ->select(
    //             'category.name as category_name',
    //             'category.image as image',
    //             DB::raw('SUM(order_details.total_price) as total_amount')
    //         )
    //         ->groupBy('category.id', 'category.name', 'category.image')
    //         ->get();
    // }

    public function getIncomeBreakdown($startDate, $endDate)
    {

        $query = DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('category', 'products.category_id', '=', 'category.id')
            ->where('orders.status', 'completed')
            ->select('category.name as category_name', 'category.image', DB::raw('SUM(order_details.total_price) as total_amount'))
            ->groupBy('category.id', 'category.name', 'category.image');

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        }

        $breakdown = $query->get()->map(function ($item) {
            return [
                'category_name' => $item->category_name,
                'image' => $item->image,
                'total_amount' => (float) $item->total_amount,
            ];
        })->toArray();

        $deliveryQuery = DB::table('deliveries')
            ->whereIn('status', ['success', 'completed']);
        if ($startDate && $endDate) {

            $deliveryQuery->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        $totalDeliveryFee = (float) $deliveryQuery->sum('delivery_fee');
        if ($totalDeliveryFee > 0) {
            $breakdown[] = [
                'category_name' => 'សេវាដឹកជញ្ជូន (Delivery Fee)',
                'image' => null,
                'total_amount' => $totalDeliveryFee,
            ];
        }

        return $breakdown;
    }
    public function getExpenseByCategoryBreakdown($startDate, $endDate)
    {
        $query = DB::table('purchase_items')
            ->join('purchases', 'purchase_items.purchase_id', '=', 'purchases.id')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->join('category', 'products.category_id', '=', 'category.id')
            ->where('purchases.status', 'completed');

        if ($startDate && $endDate) {
            $query->whereBetween('purchases.created_at', [$startDate, $endDate]);
        }

        return $query
            ->select(
                'category.name as category_name',
                'category.image as image',
                DB::raw('SUM(purchase_items.total_price) as total_amount')
            )
            ->groupBy('category.id', 'category.name', 'category.image')
            ->get();
    }

    public function getMonthlyGrowthReport($year, $month)
    {
        // ១. ចំណូលខែបច្ចុប្បន្ន
        $currentRevenue = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // ២. គណនារកខែ និងឆ្នាំមុន (Previous Month)
        $prevDate = \Carbon\Carbon::create($year, $month, 1)->subMonth();
        $prevYear = $prevDate->year;
        $prevMonth = $prevDate->month;

        // ៣. ចំណូលខែមុន
        $previousRevenue = Order::whereYear('created_at', $prevYear)
            ->whereMonth('created_at', $prevMonth)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        // ៤. គណនាភាគរយការប្រៀបធៀប (Growth Rate Percentage)
        $growthPercentage = 0;
        if ($previousRevenue > 0) {
            $growthPercentage = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
        } else {
            $growthPercentage = $currentRevenue > 0 ? 100 : 0;
        }

        return [
            'current_revenue' => (float) $currentRevenue,
            'previous_revenue' => (float) $previousRevenue,
            'growth_percentage' => round($growthPercentage, 1), // ឧ. 12.5 ឬ -5.2
            'is_increase' => $growthPercentage >= 0, // true = ឡើង, false = ចុះ
        ];
    }

     public function getAllOrders()
    {
        return Order::with([
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

    public function showInvoice($orderId)
    {
        return Order::with([
            'invoice.items.product', 
            'customer', 
            'user', 
            'payment', 
            'details'
        ])->findOrFail($orderId);

    }
}
    

