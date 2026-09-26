<?php

namespace App\RepositoryInterface;

interface ReportInterface
{
    public function getReportData($filters);
    public function getReportSummary($filters);
    public function totalSalesReport();
    public function DailyReport($filters);
    public function MonthlyReport($filters);
    public function YearlyReport($filters);
    public function getReportById($id);
    public function saleReport($data);
    public function historyReport($data);
    public function topSellingProducts($data);
    public function lowStockReport($data);
    public function purchaseHistoryReport($data);
    public function purchaseHistoryReportbyid($purchaseId, $data);
    public function customerHistoryReport($customerId, $data);
    public function getAllcustomers();
    public function getTotalIncome($startDate, $endDate);
    public function getGeneralExpenses($startDate, $endDate);
    public function getPurchaseExpenses($startDate, $endDate);
    public function getExpenseBreakdown($startDate, $endDate);
    
}
