<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $attendance = AttendanceRecord::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'check_in_time' => now(),
                'status' => 'present',
            ]
        );

        return response()->json($attendance);
    }

    public function checkOut(Request $request)
    {
        $attendance = AttendanceRecord::where('user_id', $request->user()->id)
            ->where('attendance_date', now()->toDateString())
            ->firstOrFail();

        $attendance->update(['check_out_time' => now()]);

        return response()->json($attendance);
    }

    public function myRecords(Request $request)
    {
        $records = AttendanceRecord::where('user_id', $request->user()->id)
            ->latest('attendance_date')
            ->paginate(30);

        return response()->json($records);
    }
}