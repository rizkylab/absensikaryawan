<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('user', 'approver')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('admin.leaves.index', compact('leaves'));
    }

    public function show(Leave $leave)
    {
        $leave->load('user', 'approver');
        return view('admin.leaves.show', compact('leave'));
    }

    public function approve(Request $request, Leave $leave)
    {
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

        \App\Models\AuditLog::log('leave_approved', 'Leave', $leave->id, $oldValues, $leave->toArray());

        return redirect()
            ->route('admin.leaves.index')
            ->with('success', 'Leave request approved successfully');
    }

    public function reject(Request $request, Leave $leave)
    {
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

        \App\Models\AuditLog::log('leave_rejected', 'Leave', $leave->id, $oldValues, $leave->toArray());

        return redirect()
            ->route('admin.leaves.index')
            ->with('success', 'Leave request rejected');
    }
}
