<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\User;
use App\Events\AttendanceCheckedIn;
use App\Services\AuditService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AuditService $auditService) {}

    public function index(Request $request)
    {
        $query = AttendanceRecord::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date')) {
            $query->where('attendance_date', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('attendance_date', '<=', $request->end_date);
        }

        return response()->json($query->latest('attendance_date')->paginate(30));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:present,absent,late,leave,half_day',
            'remarks' => 'nullable|string',
        ]);

        $record = AttendanceRecord::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'attendance_date' => $validated['attendance_date'],
            ],
            $validated
        );

        $this->auditService->log($request->user(), 'created', 'attendance', $record);

        return response()->json($record->load('user'), 201);
    }

    public function show(AttendanceRecord $attendance)
    {
        return response()->json($attendance->load('user'));
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $validated = $request->validate([
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'status' => 'sometimes|in:present,absent,late,leave,half_day',
            'remarks' => 'nullable|string',
        ]);

        $attendance->update($validated);

        $this->auditService->log($request->user(), 'updated', 'attendance', $attendance);

        return response()->json($attendance->fresh()->load('user'));
    }

    public function destroy(Request $request, AttendanceRecord $attendance)
    {
        $attendance->delete();

        $this->auditService->log($request->user(), 'deleted', 'attendance', $attendance);

        return response()->json(['message' => 'Attendance record deleted']);
    }

    public function checkIn(Request $request)
    {
        $attendance = AttendanceRecord::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'attendance_date' => now()->toDateString(),
            ],
            [
                'check_in_time' => now()->format('H:i:s'),
                'status' => 'present',
            ]
        );

        if (!$attendance->wasRecentlyCreated && !$attendance->check_in_time) {
            $attendance->update(['check_in_time' => now()->format('H:i:s'), 'status' => 'present']);
        }

        event(new AttendanceCheckedIn($attendance->fresh()));

        return response()->json($attendance);
    }

    public function checkOut(Request $request)
    {
        $attendance = AttendanceRecord::where('user_id', $request->user()->id)
            ->where('attendance_date', now()->toDateString())
            ->firstOrFail();

        $attendance->update(['check_out_time' => now()->format('H:i:s')]);

        return response()->json($attendance);
    }

    public function myRecords(Request $request)
    {
        $records = AttendanceRecord::where('user_id', $request->user()->id)
            ->latest('attendance_date')
            ->paginate(30);

        return response()->json($records);
    }

    public function departmentAttendance(Department $department)
    {
        $userIds = User::where('department_id', $department->id)->pluck('id');

        $records = AttendanceRecord::with('user')
            ->whereIn('user_id', $userIds)
            ->latest('attendance_date')
            ->paginate(30);

        return response()->json($records);
    }
}
