<?php

namespace App\Services;

class ReportService implements \App\Services\Interface\ReportServiceInterface
{
    protected $reportRepository;

    public function __construct(\App\Repositories\ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function getReportData($filters)
    {
        return $this->reportRepository->getReportData($filters);
    }

    public function getReportSummary($filters)
    {
        return $this->reportRepository->getReportSummary($filters);
    }

    public function DailyReport($filters)
    {
        return $this->reportRepository->DailyReport($filters);
    }

    public function MonthlyReport($filters)
    {
        return $this->reportRepository->MonthlyReport($filters);
    }

    public function WeeklyReport($filters)
    {
        return $this->reportRepository->WeeklyReport($filters);
    }
    public function YearlyReport($filters)
    {
        return $this->reportRepository->YearlyReport($filters);
    }

    public function getReportById($id)
    {
        return $this->reportRepository->getReportById($id);
    }

    public function saleReport($data)
    {
        return $this->reportRepository->saleReport($data);
    }

    public function historyReport($data)
    {
        return $this->reportRepository->historyReport($data);
    }

    public function topSellingProducts($data)
    {
        return $this->reportRepository->topSellingProducts($data);
    }


    // 🟢 បន្ថែម $data ចូលក្នុង Function នេះ ដើម្បីឱ្យត្រូវគ្នាជាមួយ Interface
    public function lowStockReport($data = [])
    {
        return $this->reportRepository->lowStockReport($data);
    }


    public function purchaseHistoryReport($data)
    {
        return $this->reportRepository->purchaseHistoryReport($data);
    }

    public function purchaseHistoryReportbyid($purchaseId, $data)
    {
        return $this->reportRepository->purchaseHistoryReportbyid($purchaseId, $data);
    }
    public function customerHistoryReport($customerId, $data)
    {
        return $this->reportRepository->customerHistoryReport($customerId, $data);
    }

    public function getAllcustomers()
    {
        return $this->reportRepository->getAllcustomers();
    }

    public function totalSalesReport()
    {
        return $this->reportRepository->totalSalesReport();
    }

    public function getFinancialReportData($startDate, $endDate)
    {
        // 1. គណនាចំណូលសរុប
        $totalIncome = $this->reportRepository->getTotalIncome($startDate, $endDate);

        // 2. ក. គណនាចំណាយទូទៅ
        $generalExpenses = $this->reportRepository->getGeneralExpenses($startDate, $endDate);

        // 2. ខ. គណនាការចំណាយទិញទំនិញចូល
        $purchaseExpenses = $this->reportRepository->getPurchaseExpenses($startDate, $endDate);

        // 3. ចំណាយសរុប
        $totalExpense = $generalExpenses + $purchaseExpenses;

        // 4. ប្រាក់ចំណេញសុទ្ធ
        $netProfit = $totalIncome - $totalExpense;

        // 5. ចំណាយលម្អិតតាមប្រភេទ
        $expenseBreakdown = $this->reportRepository->getExpenseBreakdown($startDate, $endDate);

        $incomeBreakdown = $this->reportRepository->getIncomeBreakdown($startDate, $endDate);
        $expenseBreakdown = $this->reportRepository->getExpenseBreakdown($startDate, $endDate);
        $expenseByCategoryBreakdown = $this->reportRepository->getExpenseByCategoryBreakdown($startDate, $endDate);
        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'general_expenses' => $generalExpenses,
            'purchase_expenses' => $purchaseExpenses,
            'net_profit' => $netProfit,
            'income_breakdown' => $incomeBreakdown,
            'expense_breakdown' => $expenseBreakdown,
            'expense_by_category' => $expenseByCategoryBreakdown, // 🟢 ចំណាយតាម Product Category ថ្មី
        ];
    }
}
