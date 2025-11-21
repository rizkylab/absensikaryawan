<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::where('user_id', auth()->id())
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('karyawan.leave.index', compact('leaves'));
    }

    public function create()
    {
        return view('karyawan.leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sick,annual,unpaid,other',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ]);

        // Calculate days
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        Leave::create([
            'user_id' => auth()->id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'type' => $request->type,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        // Log audit
        \App\Models\AuditLog::log('leave_requested', 'Leave', null);

        return redirect()
            ->route('karyawan.leaves.index')
            ->with('success', 'Leave request submitted successfully');
    }

    public function show(Leave $leave)
    {
        // Ensure user can only view their own leave
        if ($leave->user_id !== auth()->id()) {
            abort(403);
        }

        return view('karyawan.leave.show', compact('leave'));
    }

    public function edit(Leave $leave)
    {
        // Ensure user can only edit their own pending leave
        if ($leave->user_id !== auth()->id() || $leave->status !== 'pending') {
            abort(403);
        }

        return view('karyawan.leave.edit', compact('leave'));
    }

    public function update(Request $request, Leave $leave)
    {
        // Ensure user can only update their own pending leave
        if ($leave->user_id !== auth()->id() || $leave->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:sick,annual,unpaid,other',
            'reason' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate days
        $start = \Carbon\Carbon::parse($request->start_date);
        $end = \Carbon\Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;

        $oldValues = $leave->toArray();

        // Handle file upload
        if ($request->hasFile('attachment')) {
            // Delete old attachment
            if ($leave->attachment) {
                Storage::disk('public')->delete($leave->attachment);
            }
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
            $leave->attachment = $attachmentPath;
        }

        $leave->update([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        // Log audit
        \App\Models\AuditLog::log('leave_updated', 'Leave', $leave->id, $oldValues, $leave->toArray());

        return redirect()
            ->route('karyawan.leaves.index')
            ->with('success', 'Leave request updated successfully');
    }

    public function destroy(Leave $leave)
    {
        // Ensure user can only delete their own pending leave
        if ($leave->user_id !== auth()->id() || $leave->status !== 'pending') {
            abort(403);
        }

        // Delete attachment
        if ($leave->attachment) {
            Storage::disk('public')->delete($leave->attachment);
        }

        $leave->delete();

        // Log audit
        \App\Models\AuditLog::log('leave_deleted', 'Leave', $leave->id);

        return redirect()
            ->route('karyawan.leaves.index')
            ->with('success', 'Leave request deleted successfully');
    }
}
