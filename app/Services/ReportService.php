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
}