<?php
namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\RepositoryInterface\ReportInterface;

class ReportRepository implements ReportInterface
{
    /**
     * ទាញយកទិន្នន័យរបាយការណ៍ផ្អែកលើ Filter ផ្សេងៗ (កាលបរិច្ឆេទជាដើម)
     */
    public function getReportData($filters)
    {
        $query = Order::with(['customer', 'user', 'details', 'payment']);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        return $query->latest()->get();
    }

    /**
     * ទាញយកសង្ខេបរបាយការណ៍ (Total Orders, Revenue, Discount, Tax)
     */
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

    /**
     * បង្កើតរបាយការណ៍ប្រចាំថ្ងៃ (Daily Report)
     */
    public function DailyReport($filters)
    {
        $query = Order::query();

        if (!empty($filters['month']) && !empty($filters['year'])) {
            $query->whereYear('created_at', $filters['year'])
                  ->whereMonth('created_at', $filters['month']);
        }

        return $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
    }

    /**
     * បង្កើតរបាយការណ៍ប្រចាំខែ (Monthly Report)
     */
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

    /**
     * បង្កើតរបាយការណ៍ប្រចាំឆ្នាំ (Yearly Report)
     */
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
}