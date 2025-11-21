<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = Overtime::with('user', 'approver')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('admin.overtimes.index', compact('overtimes'));
    }

    public function show(Overtime $overtime)
    {
        $overtime->load('user', 'approver');
        return view('admin.overtimes.show', compact('overtime'));
    }

    public function approve(Request $request, Overtime $overtime)
    {
        if ($overtime->status !== 'pending') {
            return back()->with('error', 'This overtime request has already been processed');
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $oldValues = $overtime->toArray();

        $overtime->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        \App\Models\AuditLog::log('overtime_approved', 'Overtime', $overtime->id, $oldValues, $overtime->toArray());

        return redirect()
            ->route('admin.overtimes.index')
            ->with('success', 'Overtime request approved successfully');
    }

    public function reject(Request $request, Overtime $overtime)
    {
        if ($overtime->status !== 'pending') {
            return back()->with('error', 'This overtime request has already been processed');
        }

        $request->validate([
            'notes' => 'required|string|max:500',
        ]);

        $oldValues = $overtime->toArray();

        $overtime->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => $request->notes,
        ]);

        \App\Models\AuditLog::log('overtime_rejected', 'Overtime', $overtime->id, $oldValues, $overtime->toArray());

        return redirect()
            ->route('admin.overtimes.index')
            ->with('success', 'Overtime request rejected');
    }
}
