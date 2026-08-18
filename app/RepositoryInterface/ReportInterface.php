<?php
namespace App\RepositoryInterface;

interface ReportInterface
{
    public function getReportData($filters);
    public function getReportSummary($filters);
    public function DailyReport($filters);
    public function MonthlyReport($filters);
    public function YearlyReport($filters);
    public function getReportById($id);
    public function saleReport($data);
}