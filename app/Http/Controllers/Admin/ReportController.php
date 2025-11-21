<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Leave;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function attendance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $attendances = Attendance::with('user')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.attendance-pdf', compact('attendances', 'startDate', 'endDate'));
            return $pdf->download('attendance-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        } else {
            // Excel export would go here
            // return Excel::download(new AttendanceExport($attendances), 'attendance-report.xlsx');
            return back()->with('info', 'Excel export coming soon');
        }
    }

    public function overtime(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $overtimes = Overtime::with('user', 'approver')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.overtime-pdf', compact('overtimes', 'startDate', 'endDate'));
            return $pdf->download('overtime-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        } else {
            return back()->with('info', 'Excel export coming soon');
        }
    }

    public function leave(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $leaves = Leave::with('user', 'approver')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->orderBy('start_date', 'desc')
            ->get();

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.leave-pdf', compact('leaves', 'startDate', 'endDate'));
            return $pdf->download('leave-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
        } else {
            return back()->with('info', 'Excel export coming soon');
        }
    }
}
