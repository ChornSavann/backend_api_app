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
}