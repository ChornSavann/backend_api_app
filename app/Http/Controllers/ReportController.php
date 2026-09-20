<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Services\Interface\ReportServiceInterface;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SupplierOrder;
use App\Models\SupplierOrderDetail;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use App\Models\ExpenseType;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;
    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getReportData(Request $request): JsonResponse
    {
        $filters = $request->all();
        $reportData = $this->reportService->getReportData($filters);
        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    }

    public function getReportSummary(Request $request): JsonResponse
    {
        $filters = $request->all();
        $reportSummary = $this->reportService->getReportSummary($filters);
        return response()->json([
            'success' => true,
            'data' => $reportSummary
        ]);
    }

    public function DailyReport(Request $request): JsonResponse
    {
        $filters = $request->all();
        $dailyReport = $this->reportService->DailyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $dailyReport
        ]);
    }

    public function WeeklyReport(Request $request): JsonResponse
    {
        $filters = $request->all();
        $weeklyReport = $this->reportService->WeeklyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $weeklyReport
        ]);
    }

    public function MonthlyReport(Request $request): JsonResponse
    {
        $filters = $request->all();
        $monthlyReport = $this->reportService->MonthlyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $monthlyReport
        ]);
    }

    public function YearlyReport(Request $request): JsonResponse
    {
        $filters = $request->all();
        $yearlyReport = $this->reportService->YearlyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $yearlyReport
        ]);
    }

    public function getReportById($id): JsonResponse
    {
        $report = $this->reportService->getReportById($id);
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function saleReport(Request $request): JsonResponse
    {
        $data = $request->all();
        $saleReport = $this->reportService->saleReport($data);
        return response()->json([
            'success' => true,
            'data' => $saleReport
        ]);
    }

    public function historyReport(Request $request): JsonResponse
    {

        $data = $request->all();
        $historyReport = $this->reportService->historyReport($data);
        return response()->json([
            'success' => true,
            'data' => $historyReport
        ]);
    }


    public function topSellingProducts(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->reportService->topSellingProducts($data);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    // public function lowStockReport($data): JsonResponse
    // {
    //     $result = $this->reportService->lowStockReport([$data = []]);
    //     return response()->json([
    //         'success' => true,
    //         'data' => $result
    //     ]);
    // }
    public function lowStockReport(Request $request): JsonResponse
    {
        $filters = $request->all();
        $result = $this->reportService->lowStockReport($filters); // ហៅ Service
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function purchaseHistoryReport(Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->reportService->purchaseHistoryReport($data);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function purchaseHistoryReportbyid($purchaseId, Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->reportService->purchaseHistoryReportbyid($purchaseId, $data);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function customerHistoryReport($customerId, Request $request): JsonResponse
    {
        $data = $request->all();
        $result = $this->reportService->customerHistoryReport($customerId, $data);

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function getAllcustomers(): JsonResponse
    {
        $result = $this->reportService->getAllcustomers();

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function totalSalesReport(): JsonResponse
    {
        $result = $this->reportService->totalSalesReport();

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    
    public function getFinancialReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->reportService->getFinancialReportData($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
