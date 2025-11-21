<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use App\Services\QrCodeService;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService,
        protected QrCodeService $qrService
    ) {}

    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $canCheckIn = !$todayAttendance || !$todayAttendance->check_in;
        $canCheckOut = $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out;

        return view('karyawan.attendance.index', compact(
            'todayAttendance',
            'canCheckIn',
            'canCheckOut'
        ));
    }

    public function checkInForm()
    {
        $qrCode = $this->qrService->getOrCreateTodayQrCode();
        
        return view('karyawan.attendance.check-in', compact('qrCode'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'qr_token' => 'required|string',
            'photo_data' => 'required|string', // Base64 encoded image from camera
            'address' => 'nullable|string',
        ]);

        $result = $this->attendanceService->checkIn(auth()->user(), $request->all());

        if ($result['success']) {
            return redirect()
                ->route('karyawan.attendance.index')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function checkOutForm()
    {
        $user = auth()->user();
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$todayAttendance || !$todayAttendance->check_in) {
            return redirect()
                ->route('karyawan.attendance.index')
                ->with('error', 'You have not checked in today');
        }

        if ($todayAttendance->check_out) {
            return redirect()
                ->route('karyawan.attendance.index')
                ->with('error', 'You have already checked out today');
        }

        return view('karyawan.attendance.check-out', compact('todayAttendance'));
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo_data' => 'required|string', // Base64 encoded image from camera
            'address' => 'nullable|string',
        ]);

        $result = $this->attendanceService->checkOut(auth()->user(), $request->all());

        if ($result['success']) {
            return redirect()
                ->route('karyawan.attendance.index')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('error', $result['message']);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', Carbon::now()->format('Y-m'));

        $attendances = $this->attendanceService->getUserAttendanceHistory($user, $month);

        return view('karyawan.attendance.history', compact('attendances', 'month'));
    }
}
