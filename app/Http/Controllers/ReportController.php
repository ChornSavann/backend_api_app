<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportService;
use App\Services\Interface\ReportServiceInterface;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;
    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getReportData(Request $request):JsonResponse
    {
        $filters = $request->all();
        $reportData = $this->reportService->getReportData($filters);
        return response()->json([
            'success' => true,
            'data' => $reportData
        ]);
    }

    public function getReportSummary(Request $request):JsonResponse
    {
        $filters = $request->all();
        $reportSummary = $this->reportService->getReportSummary($filters);
        return response()->json([
            'success' => true,
            'data' => $reportSummary
        ]);
    }

    public function DailyReport(Request $request):JsonResponse
    {
        $filters = $request->all();
        $dailyReport = $this->reportService->DailyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $dailyReport
        ]);
    }

    public function MonthlyReport(Request $request):JsonResponse
    {
        $filters = $request->all();
        $monthlyReport = $this->reportService->MonthlyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $monthlyReport
        ]);
    }

    public function YearlyReport(Request $request):JsonResponse
    {
        $filters = $request->all();
        $yearlyReport = $this->reportService->YearlyReport($filters);
        return response()->json([
            'success' => true,
            'data' => $yearlyReport
        ]);
    }

    public function getReportById($id):JsonResponse
    {
        $report = $this->reportService->getReportById($id);
        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }
    
    public function saleReport(Request $request):JsonResponse
    {
        $data = $request->all();
        $saleReport = $this->reportService->saleReport($data);
        return response()->json([
            'success' => true,
            'data' => $saleReport
        ]);
    }
}
