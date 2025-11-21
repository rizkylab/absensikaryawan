<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Leave;
use App\Models\Payroll;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->format('Y-m');

        // Total counts
        $totalUsers = User::where('role_id', '!=', 1)->count(); // Exclude admin
        $totalKaryawan = User::whereHas('role', function($q) {
            $q->where('name', 'karyawan');
        })->count();

        // Today's attendance
        $todayAttendances = Attendance::whereDate('date', $today)->count();
        $todayLate = Attendance::whereDate('date', $today)
            ->where('late_duration', '>', 0)
            ->count();

        // Pending approvals
        $pendingOvertimes = Overtime::where('status', 'pending')->count();
        $pendingLeaves = Leave::where('status', 'pending')->count();

        // This month stats
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $monthlyAttendance = Attendance::whereBetween('date', [$monthStart, $monthEnd])
            ->whereNotNull('check_in')
            ->count();

        $monthlyPayroll = Payroll::where('period', $thisMonth)->sum('total_salary');

        // Recent attendances
        $recentAttendances = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Charts data - Last 7 days attendance
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = [
                'date' => $date->format('d M'),
                'count' => Attendance::whereDate('date', $date)->whereNotNull('check_in')->count(),
                'late' => Attendance::whereDate('date', $date)->where('late_duration', '>', 0)->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalKaryawan',
            'todayAttendances',
            'todayLate',
            'pendingOvertimes',
            'pendingLeaves',
            'monthlyAttendance',
            'monthlyPayroll',
            'recentAttendances',
            'last7Days'
        ));
    }
}
