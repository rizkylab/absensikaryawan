<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Overtime;
use Illuminate\Http\Request;

class OvertimeController extends Controller
{
    public function index()
    {
        $overtimes = Overtime::where('user_id', auth()->id())
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('karyawan.overtime.index', compact('overtimes'));
    }

    public function create()
    {
        return view('karyawan.overtime.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        // Calculate duration in minutes
        $start = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);

        Overtime::create([
            'user_id' => auth()->id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $duration,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        // Log audit
        \App\Models\AuditLog::log('overtime_requested', 'Overtime', null);

        return redirect()
            ->route('karyawan.overtimes.index')
            ->with('success', 'Overtime request submitted successfully');
    }

    public function show(Overtime $overtime)
    {
        // Ensure user can only view their own overtime
        if ($overtime->user_id !== auth()->id()) {
            abort(403);
        }

        return view('karyawan.overtime.show', compact('overtime'));
    }

    public function edit(Overtime $overtime)
    {
        // Ensure user can only edit their own pending overtime
        if ($overtime->user_id !== auth()->id() || $overtime->status !== 'pending') {
            abort(403);
        }

        return view('karyawan.overtime.edit', compact('overtime'));
    }

    public function update(Request $request, Overtime $overtime)
    {
        // Ensure user can only update their own pending overtime
        if ($overtime->user_id !== auth()->id() || $overtime->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:500',
        ]);

        // Calculate duration
        $start = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
        $end = \Carbon\Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);

        $oldValues = $overtime->toArray();

        $overtime->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $duration,
            'reason' => $request->reason,
        ]);

        // Log audit
        \App\Models\AuditLog::log('overtime_updated', 'Overtime', $overtime->id, $oldValues, $overtime->toArray());

        return redirect()
            ->route('karyawan.overtimes.index')
            ->with('success', 'Overtime request updated successfully');
    }

    public function destroy(Overtime $overtime)
    {
        // Ensure user can only delete their own pending overtime
        if ($overtime->user_id !== auth()->id() || $overtime->status !== 'pending') {
            abort(403);
        }

        $overtime->delete();

        // Log audit
        \App\Models\AuditLog::log('overtime_deleted', 'Overtime', $overtime->id);

        return redirect()
            ->route('karyawan.overtimes.index')
            ->with('success', 'Overtime request deleted successfully');
    }
}
