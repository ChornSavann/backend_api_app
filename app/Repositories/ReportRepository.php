<?php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\RepositoryInterface\ReportInterface;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Customer;
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
        $query = Order::with(['customer', 'payment', 'details.product']);

        if (!empty($filters['date'])) {
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
                DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('month')
            ->orderBy('month', 'ASC')
            ->get();
    }


    public function YearlyReport($filters)
    {
        return Order::select(
                DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->get();
    }

    /**
     * ទាញយកព័ត៌មានលម្អិតរបស់របាយការណ៍តាម ID
     */
    public function getReportById($id)
    {
        return Order::with(['customer', 'user', 'details.product', 'payment', 'invoice'])
                    ->find($id);
    }

    /**
     * បង្កើតរបាយការណ៍ការលក់ (Sales Report)
     */

    public function saleReport($data)
    {
        $query = Order::with(['details.product', 'payment', 'customer']);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        if (!empty($data['payment_method'])) {
            $query->whereHas('payment', function($q) use ($data) 
            {
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



    public function topSellingProducts($data)
    {
        $limit = $data['limit'] ?? 6; 

        return OrderDetail::with('product') 
            ->select(
                'product_id', 
                'product_name', 
                DB::raw('SUM(quantity) as total_quantity_sold'), 
                DB::raw('SUM(total_price) as total_revenue')
            )
            ->groupBy('product_id', 'product_name')
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

    public function purchaseHistoryReportbyid($purchaseId, $data)
    {
        $query = Purchase::with(['supplier', 'user', 'purchaseItems.product'])
            ->where('id', $purchaseId);

        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
        }

        return $query->first();
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
            ->get();
    }
   
}