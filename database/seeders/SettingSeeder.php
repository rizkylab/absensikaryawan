<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Work hours
            [
                'key' => 'work_start_time',
                'value' => '08:00',
                'type' => 'string',
                'description' => 'Work start time',
            ],
            [
                'key' => 'work_end_time',
                'value' => '17:00',
                'type' => 'string',
                'description' => 'Work end time',
            ],
            [
                'key' => 'late_tolerance',
                'value' => '15',
                'type' => 'integer',
                'description' => 'Late tolerance in minutes',
            ],
            
            // Face recognition
            [
                'key' => 'face_recognition_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable face recognition for attendance',
            ],
            [
                'key' => 'face_recognition_threshold',
                'value' => '70',
                'type' => 'integer',
                'description' => 'Minimum face recognition score (0-100)',
            ],
            
            // Payroll
            [
                'key' => 'late_penalty_per_minute',
                'value' => '5000',
                'type' => 'integer',
                'description' => 'Penalty amount per minute late (IDR)',
            ],
            [
                'key' => 'overtime_rate_per_hour',
                'value' => '50000',
                'type' => 'integer',
                'description' => 'Overtime rate per hour (IDR)',
            ],
            [
                'key' => 'leave_deduction_per_day',
                'value' => '100000',
                'type' => 'integer',
                'description' => 'Leave deduction per day (IDR)',
            ],
            
            // Company info
            [
                'key' => 'company_name',
                'value' => 'PT. Absensi Modern Indonesia',
                'type' => 'string',
                'description' => 'Company name',
            ],
            [
                'key' => 'company_address',
                'value' => 'Jl. Sudirman No. 123, Jakarta',
                'type' => 'string',
                'description' => 'Company address',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
