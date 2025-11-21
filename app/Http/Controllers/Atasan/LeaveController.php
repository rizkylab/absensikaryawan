<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subordinateIds = \App\Models\User::where('supervisor_id', $user->id)->pluck('id');

        $leaves = Leave::whereIn('user_id', $subordinateIds)
            ->with('user')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('atasan.leave.index', compact('leaves'));
    }

    public function show(Leave $leave)
    {
        // Ensure leave belongs to subordinate
        if ($leave->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

        return view('atasan.leave.show', compact('leave'));
    }

    public function approve(Request $request, Leave $leave)
    {
        // Ensure leave belongs to subordinate
        if ($leave->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'This leave request has already been processed');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $oldValues = $leave->toArray();

        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        // Log audit
        \App\Models\AuditLog::log('leave_approved', 'Leave', $leave->id, $oldValues, $leave->toArray());

        return redirect()
            ->route('atasan.leaves.index')
            ->with('success', 'Leave request approved successfully');
    }

    public function reject(Request $request, Leave $leave)
    {
        // Ensure leave belongs to subordinate
        if ($leave->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'This leave request has already been processed');
        }

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $oldValues = $leave->toArray();

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        // Log audit
        \App\Models\AuditLog::log('leave_rejected', 'Leave', $leave->id, $oldValues, $leave->toArray());

        return redirect()
            ->route('atasan.leaves.index')
            ->with('success', 'Leave request rejected');
    }
}
