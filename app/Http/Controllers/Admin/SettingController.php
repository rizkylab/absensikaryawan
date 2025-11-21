<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\OfficeLocation;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        $officeLocations = OfficeLocation::all();

        return view('admin.settings.index', compact('settings', 'officeLocations'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i',
            'late_tolerance' => 'required|integer|min:0',
            'face_recognition_enabled' => 'required|boolean',
            'face_recognition_threshold' => 'required|integer|min:0|max:100',
            'late_penalty_per_minute' => 'required|numeric|min:0',
            'overtime_rate_per_hour' => 'required|numeric|min:0',
            'leave_deduction_per_day' => 'required|numeric|min:0',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            $type = match($key) {
                'face_recognition_enabled' => 'boolean',
                'late_tolerance', 'face_recognition_threshold' => 'integer',
                'late_penalty_per_minute', 'overtime_rate_per_hour', 'leave_deduction_per_day' => 'float',
                default => 'string',
            };

            Setting::set($key, $value, $type);
        }

        \App\Models\AuditLog::log('settings_updated', 'Setting', null);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Settings updated successfully');
    }
}
