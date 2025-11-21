<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'user_id',
        'period',
        'base_salary',
        'attendance_days',
        'total_work_days',
        'late_penalty',
        'early_leave_penalty',
        'overtime_bonus',
        'leave_deduction',
        'total_salary',
        'generated_at',
        'generated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function getFormattedPeriod(): string
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->period);
        return $date->format('F Y');
    }

    public function getFormattedBaseSalary(): string
    {
        return 'Rp ' . number_format($this->base_salary, 0, ',', '.');
    }

    public function getFormattedTotalSalary(): string
    {
        return 'Rp ' . number_format($this->total_salary, 0, ',', '.');
    }
}
