<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard redirect based on role
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAtasan()) {
            return redirect()->route('atasan.dashboard');
        } else {
            return redirect()->route('karyawan.dashboard');
        }
    })->name('dashboard');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // User management
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    
    // Office locations
    Route::resource('office-locations', App\Http\Controllers\Admin\OfficeLocationController::class);
    
    // Attendance management
    Route::get('/attendances', [App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('/attendances/{attendance}', [App\Http\Controllers\Admin\AttendanceController::class, 'show'])->name('attendances.show');
    
    // Overtime management
    Route::get('/overtimes', [App\Http\Controllers\Admin\OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('/overtimes/{overtime}', [App\Http\Controllers\Admin\OvertimeController::class, 'show'])->name('overtimes.show');
    Route::post('/overtimes/{overtime}/approve', [App\Http\Controllers\Admin\OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::post('/overtimes/{overtime}/reject', [App\Http\Controllers\Admin\OvertimeController::class, 'reject'])->name('overtimes.reject');
    
    // Leave management
    Route::get('/leaves', [App\Http\Controllers\Admin\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/{leave}', [App\Http\Controllers\Admin\LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [App\Http\Controllers\Admin\LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [App\Http\Controllers\Admin\LeaveController::class, 'reject'])->name('leaves.reject');
    
    // Payroll
    Route::get('/payrolls', [App\Http\Controllers\Admin\PayrollController::class, 'index'])->name('payrolls.index');
    Route::post('/payrolls/generate', [App\Http\Controllers\Admin\PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::get('/payrolls/{payroll}', [App\Http\Controllers\Admin\PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/download', [App\Http\Controllers\Admin\PayrollController::class, 'download'])->name('payrolls.download');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/attendance', [App\Http\Controllers\Admin\ReportController::class, 'attendance'])->name('reports.attendance');
    Route::post('/reports/overtime', [App\Http\Controllers\Admin\ReportController::class, 'overtime'])->name('reports.overtime');
    Route::post('/reports/leave', [App\Http\Controllers\Admin\ReportController::class, 'leave'])->name('reports.leave');
    
    // Settings
    Route::get('/settings', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    // Audit logs
    Route::get('/audit-logs', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    
    // QR Code
    Route::get('/qr-code', [App\Http\Controllers\Admin\QrCodeController::class, 'index'])->name('qr-code.index');
    Route::post('/qr-code/generate', [App\Http\Controllers\Admin\QrCodeController::class, 'generate'])->name('qr-code.generate');
});

// Atasan routes
Route::middleware(['auth', 'role:atasan'])->prefix('atasan')->name('atasan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Atasan\DashboardController::class, 'index'])->name('dashboard');
    
    // Team attendance
    Route::get('/team-attendance', [App\Http\Controllers\Atasan\TeamAttendanceController::class, 'index'])->name('team-attendance.index');
    Route::get('/team-attendance/{user}', [App\Http\Controllers\Atasan\TeamAttendanceController::class, 'show'])->name('team-attendance.show');
    
    // Approvals
    Route::get('/approvals', [App\Http\Controllers\Atasan\ApprovalController::class, 'index'])->name('approvals.index');
    
    // Overtime approvals
    Route::get('/overtimes', [App\Http\Controllers\Atasan\OvertimeController::class, 'index'])->name('overtimes.index');
    Route::get('/overtimes/{overtime}', [App\Http\Controllers\Atasan\OvertimeController::class, 'show'])->name('overtimes.show');
    Route::post('/overtimes/{overtime}/approve', [App\Http\Controllers\Atasan\OvertimeController::class, 'approve'])->name('overtimes.approve');
    Route::post('/overtimes/{overtime}/reject', [App\Http\Controllers\Atasan\OvertimeController::class, 'reject'])->name('overtimes.reject');
    
    // Leave approvals
    Route::get('/leaves', [App\Http\Controllers\Atasan\LeaveController::class, 'index'])->name('leaves.index');
    Route::get('/leaves/{leave}', [App\Http\Controllers\Atasan\LeaveController::class, 'show'])->name('leaves.show');
    Route::post('/leaves/{leave}/approve', [App\Http\Controllers\Atasan\LeaveController::class, 'approve'])->name('leaves.approve');
    Route::post('/leaves/{leave}/reject', [App\Http\Controllers\Atasan\LeaveController::class, 'reject'])->name('leaves.reject');
    
    // Reports
    Route::get('/reports', [App\Http\Controllers\Atasan\ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/team-attendance', [App\Http\Controllers\Atasan\ReportController::class, 'teamAttendance'])->name('reports.team-attendance');
});

// Karyawan routes
Route::middleware(['auth', 'role:karyawan'])->prefix('karyawan')->name('karyawan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Karyawan\DashboardController::class, 'index'])->name('dashboard');
    
    // Attendance
    Route::get('/attendance', [App\Http\Controllers\Karyawan\AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/check-in', [App\Http\Controllers\Karyawan\AttendanceController::class, 'checkInForm'])->name('attendance.check-in');
    Route::post('/attendance/check-in', [App\Http\Controllers\Karyawan\AttendanceController::class, 'checkIn'])->name('attendance.check-in.submit');
    Route::get('/attendance/check-out', [App\Http\Controllers\Karyawan\AttendanceController::class, 'checkOutForm'])->name('attendance.check-out');
    Route::post('/attendance/check-out', [App\Http\Controllers\Karyawan\AttendanceController::class, 'checkOut'])->name('attendance.check-out.submit');
    Route::get('/attendance/history', [App\Http\Controllers\Karyawan\AttendanceController::class, 'history'])->name('attendance.history');
    
    // Overtime
    Route::resource('overtimes', App\Http\Controllers\Karyawan\OvertimeController::class);
    
    // Leave
    Route::resource('leaves', App\Http\Controllers\Karyawan\LeaveController::class);
    
    // Payroll
    Route::get('/payrolls', [App\Http\Controllers\Karyawan\PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('/payrolls/{payroll}', [App\Http\Controllers\Karyawan\PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('/payrolls/{payroll}/download', [App\Http\Controllers\Karyawan\PayrollController::class, 'download'])->name('payrolls.download');
});

require __DIR__.'/auth.php';
