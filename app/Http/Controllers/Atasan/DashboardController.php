<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get subordinates
        $subordinates = User::where('supervisor_id', $user->id)->get();
        $subordinateIds = $subordinates->pluck('id');

        // Pending approvals
        $pendingOvertimes = Overtime::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->count();

        $pendingLeaves = Leave::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->count();

        // Today's team attendance
        $today = Carbon::today();
        $todayAttendances = Attendance::whereIn('user_id', $subordinateIds)
            ->whereDate('date', $today)
            ->with('user')
            ->get();

        $teamStats = [
            'total_team' => $subordinates->count(),
            'checked_in' => $todayAttendances->where('check_in', '!=', null)->count(),
            'late' => $todayAttendances->where('late_duration', '>', 0)->count(),
            'absent' => $subordinates->count() - $todayAttendances->count(),
        ];

        // Recent approval requests
        $recentOvertimes = Overtime::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentLeaves = Leave::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('atasan.dashboard', compact(
            'subordinates',
            'pendingOvertimes',
            'pendingLeaves',
            'teamStats',
            'todayAttendances',
            'recentOvertimes',
            'recentLeaves'
        ));
    }
}
