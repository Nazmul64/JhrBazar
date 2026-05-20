<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a 30-day grid of employee attendance history.
     */
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));

        $employees = User::where('role', 'employee')
            ->with(['department', 'designation'])
            ->orderBy('name')
            ->get();

        // Get total days in the selected month
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Fetch all attendances for this month and group by employee and day
        $startDate = "$year-$month-01";
        $endDate = "$year-$month-$daysInMonth";

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get()
            ->groupBy(['employee_id', function ($item) {
                return Carbon::parse($item->date)->day;
            }]);

        return view('admin.attendance.index', compact('employees', 'month', 'year', 'daysInMonth', 'attendances'));
    }

    /**
     * Toggle or save a single attendance status via AJAX.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'date'        => 'required|date',
            'status'      => 'required|in:Present,Absent,Late,Leave'
        ]);

        $status = $request->status;
        $clockIn = null;
        $clockOut = null;
        $workingHours = 0;
        $lateMinutes = 0;

        if ($status === 'Present') {
            $clockIn = '09:00:00';
            $clockOut = '17:00:00';
            $workingHours = 8.0;
        } elseif ($status === 'Late') {
            $clockIn = '09:45:00';
            $clockOut = '17:00:00';
            $workingHours = 7.25;
            $lateMinutes = 45;
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date'        => $request->date,
            ],
            [
                'status'        => $status,
                'clock_in'      => $clockIn,
                'clock_out'     => $clockOut,
                'working_hours' => $workingHours,
                'late_minutes'  => $lateMinutes,
                'device_ip'     => $request->ip(),
                'shift_name'    => 'Standard Day Shift'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully.',
            'status'  => $attendance->status
        ]);
    }

    /**
     * Fetch complete punch and shift details for a single day.
     */
    public function getPunchDetails(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'date'        => 'required|date'
        ]);

        $attendance = Attendance::where('employee_id', $request->employee_id)
            ->where('date', $request->date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'exists' => false,
                'date'   => $request->date,
                'employee_id' => $request->employee_id
            ]);
        }

        return response()->json([
            'exists'               => true,
            'id'                   => $attendance->id,
            'status'               => $attendance->status,
            'clock_in'             => $attendance->clock_in ? substr($attendance->clock_in, 0, 5) : '',
            'clock_out'            => $attendance->clock_out ? substr($attendance->clock_out, 0, 5) : '',
            'working_hours'        => $attendance->working_hours ?? 0,
            'late_minutes'         => $attendance->late_minutes ?? 0,
            'device_ip'            => $attendance->device_ip ?? 'N/A',
            'location_coordinates' => $attendance->location_coordinates ?? 'N/A',
            'shift_name'           => $attendance->shift_name ?? 'Standard Day Shift',
            'note'                 => $attendance->note ?? ''
        ]);
    }

    /**
     * Update/Save precision time punch details.
     */
    public function updatePunch(Request $request)
    {
        $request->validate([
            'employee_id'   => 'required|exists:users,id',
            'date'          => 'required|date',
            'status'        => 'required|in:Present,Absent,Late,Leave',
            'clock_in'      => 'nullable|string',
            'clock_out'     => 'nullable|string',
            'note'          => 'nullable|string',
            'shift_name'    => 'nullable|string'
        ]);

        $clockIn = $request->clock_in;
        $clockOut = $request->clock_out;
        $workingHours = 0;
        $lateMinutes = 0;

        if ($clockIn && $clockOut) {
            $inTime = Carbon::parse($clockIn);
            $outTime = Carbon::parse($clockOut);
            $workingHours = round($outTime->diffInMinutes($inTime) / 60, 2);

            // Calculate Lateness (standard shifts start at 09:00:00)
            $shiftStart = Carbon::parse('09:00:00');
            if ($inTime->greaterThan($shiftStart)) {
                $lateMinutes = $inTime->diffInMinutes($shiftStart);
            }
        }

        $attendance = Attendance::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'date'        => $request->date,
            ],
            [
                'status'               => $request->status,
                'clock_in'             => $clockIn,
                'clock_out'            => $clockOut,
                'working_hours'        => $workingHours,
                'late_minutes'         => $lateMinutes,
                'note'                 => $request->note,
                'shift_name'           => $request->shift_name ?? 'Standard Day Shift',
                'device_ip'            => $request->ip()
            ]
        );

        return redirect()->back()->with('success', 'Punch log updated successfully!');
    }
}
