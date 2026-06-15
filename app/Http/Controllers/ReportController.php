<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\ProductionRecord;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return response()->json(
            $this->reportService->financialReport($request->start_date, $request->end_date)
        );
    }

    public function production(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return response()->json(
            $this->reportService->productionReport($request->start_date, $request->end_date)
        );
    }

    public function inventory()
    {
        return response()->json($this->reportService->inventoryReport());
    }

    public function attendance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        return response()->json(
            $this->reportService->attendanceReport(
                $request->start_date,
                $request->end_date,
                $request->department_id
            )
        );
    }

    public function department($departmentId)
    {
        return response()->json(
            $this->reportService->departmentPerformance($departmentId)
        );
    }

    public function exportFinancial(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $summary = $this->reportService->financialReport($request->start_date, $request->end_date);

        $revenue = Transaction::revenue()->approved()
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date')
            ->get();

        $expenses = Transaction::expense()->approved()
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date')
            ->get();

        $filename = 'financial-report-' . $request->start_date . '-to-' . $request->end_date . '.pdf';
        $data = [
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'summary' => $summary,
            'revenue' => $revenue,
            'expenses' => $expenses,
        ];

        if ($request->query('format') === 'html') {
            return response()
                ->view('reports.financial-pdf', $data)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"');
        }

        return Pdf::loadView('reports.financial-pdf', $data)
            ->setPaper('a4')
            ->download($filename);
    }

    public function exportProduction(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $report = $this->reportService->productionReport($request->start_date, $request->end_date);

        $records = ProductionRecord::where('status', 'approved')
            ->whereBetween('production_date', [$request->start_date, $request->end_date])
            ->orderBy('production_date')
            ->get();

        $filename = 'production-report-' . $request->start_date . '-to-' . $request->end_date . '.pdf';
        $data = [
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'report' => $report,
            'records' => $records,
        ];

        if ($request->query('format') === 'html') {
            return response()
                ->view('reports.production-pdf', $data)
                ->header('Content-Type', 'text/html')
                ->header('Content-Disposition', 'attachment; filename="' . str_replace('.pdf', '.html', $filename) . '"');
        }

        return Pdf::loadView('reports.production-pdf', $data)
            ->setPaper('a4')
            ->download($filename);
    }
}