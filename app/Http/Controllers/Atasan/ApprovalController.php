<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use App\Models\Leave;

class ApprovalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subordinateIds = \App\Models\User::where('supervisor_id', $user->id)->pluck('id');

        // Pending overtimes
        $pendingOvertimes = Overtime::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Pending leaves
        $pendingLeaves = Leave::whereIn('user_id', $subordinateIds)
            ->where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('atasan.approvals.index', compact('pendingOvertimes', 'pendingLeaves'));
    }
}
