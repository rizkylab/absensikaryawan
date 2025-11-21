<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Leave;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->format('Y-m');

        // Today's attendance
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        // This month statistics
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyStats = [
            'total_days' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->whereNotNull('check_in')
                ->count(),
            'late_count' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('late_duration', '>', 0)
                ->count(),
            'early_leave_count' => Attendance::where('user_id', $user->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->where('early_leave_duration', '>', 0)
                ->count(),
        ];

        // Pending requests
        $pendingOvertimes = Overtime::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $pendingLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        // Recent attendance (last 7 days)
        $recentAttendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        return view('karyawan.dashboard', compact(
            'todayAttendance',
            'monthlyStats',
            'pendingOvertimes',
            'pendingLeaves',
            'recentAttendances'
        ));
    }
}
