<?php

namespace App\Http\Controllers\Atasan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $subordinateIds = \App\Models\User::where('supervisor_id', $user->id)->pluck('id');

        $overtimes = Overtime::whereIn('user_id', $subordinateIds)
            ->with('user')
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('atasan.overtime.index', compact('overtimes'));
    }

    public function show(Overtime $overtime)
    {
        // Ensure overtime belongs to subordinate
        if ($overtime->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

        return view('atasan.overtime.show', compact('overtime'));
    }

    public function approve(Request $request, Overtime $overtime)
    {
        // Ensure overtime belongs to subordinate
        if ($overtime->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

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

        // Log audit
        \App\Models\AuditLog::log('overtime_approved', 'Overtime', $overtime->id, $oldValues, $overtime->toArray());

        return redirect()
            ->route('atasan.overtimes.index')
            ->with('success', 'Overtime request approved successfully');
    }

    public function reject(Request $request, Overtime $overtime)
    {
        // Ensure overtime belongs to subordinate
        if ($overtime->user->supervisor_id !== auth()->id()) {
            abort(403);
        }

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

        // Log audit
        \App\Models\AuditLog::log('overtime_rejected', 'Overtime', $overtime->id, $oldValues, $overtime->toArray());

        return redirect()
            ->route('atasan.overtimes.index')
            ->with('success', 'Overtime request rejected');
    }
}
