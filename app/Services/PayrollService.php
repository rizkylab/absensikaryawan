<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Leave;
use App\Models\Setting;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollService
{
    /**
     * Calculate payroll for a user in a specific period
     */
    public function calculatePayroll(User $user, string $period): Payroll
    {
        $date = Carbon::createFromFormat('Y-m', $period);
        $startDate = $date->copy()->startOfMonth();
        $endDate = $date->copy()->endOfMonth();

        // Get base salary
        $baseSalary = $user->base_salary;

        // Calculate total work days in month (excluding weekends)
        $totalWorkDays = $this->calculateWorkDays($startDate, $endDate);

        // Get attendance days
        $attendanceDays = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'valid')
            ->whereNotNull('check_in')
            ->count();

        // Calculate late penalty
        $latePenalty = $this->calculateLatePenalty($user, $startDate, $endDate);

        // Calculate early leave penalty
        $earlyLeavePenalty = $this->calculateEarlyLeavePenalty($user, $startDate, $endDate);

        // Calculate overtime bonus
        $overtimeBonus = $this->calculateOvertimeBonus($user, $startDate, $endDate);

        // Calculate leave deduction
        $leaveDeduction = $this->calculateLeaveDeduction($user, $startDate, $endDate);

        // Calculate total salary
        $totalSalary = $baseSalary 
            - $latePenalty 
            - $earlyLeavePenalty 
            + $overtimeBonus 
            - $leaveDeduction;

        // Create or update payroll
        $payroll = Payroll::updateOrCreate(
            [
                'user_id' => $user->id,
                'period' => $period,
            ],
            [
                'base_salary' => $baseSalary,
                'attendance_days' => $attendanceDays,
                'total_work_days' => $totalWorkDays,
                'late_penalty' => $latePenalty,
                'early_leave_penalty' => $earlyLeavePenalty,
                'overtime_bonus' => $overtimeBonus,
                'leave_deduction' => $leaveDeduction,
                'total_salary' => $totalSalary,
                'generated_at' => now(),
                'generated_by' => auth()->id(),
            ]
        );

        // Log audit
        \App\Models\AuditLog::log('payroll_generated', 'Payroll', $payroll->id, null, $payroll->toArray());

        return $payroll;
    }

    /**
     * Calculate work days (excluding weekends)
     */
    protected function calculateWorkDays(Carbon $start, Carbon $end): int
    {
        $workDays = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $workDays++;
            }
            $current->addDay();
        }

        return $workDays;
    }

    /**
     * Calculate late penalty
     */
    protected function calculateLatePenalty(User $user, Carbon $start, Carbon $end): float
    {
        $penaltyPerMinute = Setting::get('late_penalty_per_minute', 5000);

        $totalLateMinutes = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->sum('late_duration');

        return $totalLateMinutes * $penaltyPerMinute;
    }

    /**
     * Calculate early leave penalty
     */
    protected function calculateEarlyLeavePenalty(User $user, Carbon $start, Carbon $end): float
    {
        $penaltyPerMinute = Setting::get('late_penalty_per_minute', 5000); // Same as late penalty

        $totalEarlyMinutes = Attendance::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->sum('early_leave_duration');

        return $totalEarlyMinutes * $penaltyPerMinute;
    }

    /**
     * Calculate overtime bonus
     */
    protected function calculateOvertimeBonus(User $user, Carbon $start, Carbon $end): float
    {
        $ratePerHour = Setting::get('overtime_rate_per_hour', 50000);

        $approvedOvertimes = Overtime::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->where('status', 'approved')
            ->get();

        $totalMinutes = $approvedOvertimes->sum('duration');
        $totalHours = $totalMinutes / 60;

        return $totalHours * $ratePerHour;
    }

    /**
     * Calculate leave deduction
     */
    protected function calculateLeaveDeduction(User $user, Carbon $start, Carbon $end): float
    {
        $deductionPerDay = Setting::get('leave_deduction_per_day', 100000);

        $approvedLeaves = Leave::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                      ->orWhereBetween('end_date', [$start, $end]);
            })
            ->get();

        $totalDays = $approvedLeaves->sum('days');

        return $totalDays * $deductionPerDay;
    }

    /**
     * Generate salary slip PDF
     */
    public function generateSlipPdf(Payroll $payroll): \Illuminate\Http\Response
    {
        $data = [
            'payroll' => $payroll,
            'user' => $payroll->user,
            'company_name' => Setting::get('company_name', 'PT. Absensi Modern Indonesia'),
            'company_address' => Setting::get('company_address', 'Jakarta, Indonesia'),
        ];

        $pdf = Pdf::loadView('pdf.salary-slip', $data);
        
        return $pdf->download('salary-slip-' . $payroll->user->employee_id . '-' . $payroll->period . '.pdf');
    }

    /**
     * Get payroll summary for a period
     */
    public function getPayrollSummary(string $period): array
    {
        $payrolls = Payroll::where('period', $period)->with('user')->get();

        return [
            'total_employees' => $payrolls->count(),
            'total_base_salary' => $payrolls->sum('base_salary'),
            'total_penalties' => $payrolls->sum('late_penalty') + $payrolls->sum('early_leave_penalty') + $payrolls->sum('leave_deduction'),
            'total_bonuses' => $payrolls->sum('overtime_bonus'),
            'total_payroll' => $payrolls->sum('total_salary'),
            'average_salary' => $payrolls->count() > 0 ? $payrolls->sum('total_salary') / $payrolls->count() : 0,
        ];
    }
}
