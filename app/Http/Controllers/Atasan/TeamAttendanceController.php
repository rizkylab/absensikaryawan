<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeamAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $subordinates = User::where('supervisor_id', $user->id)->get();
        $subordinateIds = $subordinates->pluck('id');

        $attendances = Attendance::whereIn('user_id', $subordinateIds)
            ->whereDate('date', $date)
            ->with('user')
            ->get();

        return view('atasan.team-attendance.index', compact('subordinates', 'attendances', 'date'));
    }

    public function show(User $user, Request $request)
    {
        // Ensure user is subordinate
        if ($user->supervisor_id !== auth()->id()) {
            abort(403);
        }

        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $date = Carbon::createFromFormat('Y-m', $month);
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        return view('atasan.team-attendance.show', compact('user', 'attendances', 'month'));
    }
}
