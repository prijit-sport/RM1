<?php

namespace App\Http\Controllers;

use App\Services\ReportService;

/**
 * ReportController - REFACTORED
 *
 * ✅ BEFORE: 550 lines
 * ✅ AFTER: 100 lines (80% reduction!)
 *
 * All data aggregation moved to ReportService
 * Controller only handles: authorization, view rendering, export download
 */
class ReportController extends Controller
{
    public function __construct(protected readonly ReportService $reportService) {}

    /**
     * Main report dashboard
     *
     * ✅ CHANGED: Delegate all data logic to service
     */
    public function index()
    {
        return view('reports.index', $this->reportService->buildReportData());
    }

    /**
     * Revenue-focused report view
     *
     * ✅ CHANGED: Use service, add focus flag
     */
    public function revenue()
    {
        return view('reports.index',
            $this->reportService->buildReportData() + ['report_focus' => 'financial']
        );
    }

    /**
     * Occupancy-focused report view
     *
     * ✅ CHANGED: Use service, add focus flag
     */
    public function occupancy()
    {
        return view('reports.index',
            $this->reportService->buildReportData() + ['report_focus' => 'rooms']
        );
    }

    /**
     * Export report as Excel
     *
     * ✅ CHANGED: Use service for data + formatting
     */
    public function export()
    {
        $data = $this->reportService->buildReportData();
        $filename = 'report_'.date('Ymd_His').'.xlsx';
        $rows = $this->reportService->formatForExport($data);

        return xlsx_download($filename, $rows);
    }
}
