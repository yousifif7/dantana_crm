<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
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
}