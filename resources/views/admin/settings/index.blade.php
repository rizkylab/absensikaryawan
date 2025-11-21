<x-app-layout>
    <x-slot name="header">Settings</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('admin.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('admin.settings.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Settings</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-4xl">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">System Settings</h2>

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Work Hours Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Work Hours</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work Start Time</label>
                        <input type="time" name="work_start_time" value="{{ $settings['work_start_time']->value ?? '08:00' }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Work End Time</label>
                        <input type="time" name="work_end_time" value="{{ $settings['work_end_time']->value ?? '17:00' }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Late Tolerance (minutes)</label>
                        <input type="number" name="late_tolerance" value="{{ $settings['late_tolerance']->value ?? 15 }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>
                </div>
            </div>

            <!-- GPS & Location Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">GPS & Location Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">GPS Radius (meters)</label>
                        <input type="number" name="gps_radius" value="{{ $settings['gps_radius']->value ?? 100 }}" 
                               min="0"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maximum distance allowed from office location (currently: {{ $settings['gps_radius']->value ?? 100 }}m)</p>
                    </div>
                </div>
            </div>

            <!-- Face Recognition Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Face Recognition</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="flex items-center">
                            <input type="hidden" name="face_recognition_enabled" value="0">
                            <input type="checkbox" name="face_recognition_enabled" value="1" 
                                   {{ ($settings['face_recognition_enabled']->value ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Enable Face Recognition</span>
                        </label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recognition Threshold (%)</label>
                        <input type="number" name="face_recognition_threshold" value="{{ $settings['face_recognition_threshold']->value ?? 80 }}" 
                               min="0" max="100"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>
                </div>
            </div>

            <!-- Payroll Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Payroll Settings</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Late Penalty (Rp/minute)</label>
                        <input type="number" name="late_penalty_per_minute" value="{{ $settings['late_penalty_per_minute']->value ?? 1000 }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Overtime Rate (Rp/hour)</label>
                        <input type="number" name="overtime_rate_per_hour" value="{{ $settings['overtime_rate_per_hour']->value ?? 50000 }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Leave Deduction (Rp/day)</label>
                        <input type="number" name="leave_deduction_per_day" value="{{ $settings['leave_deduction_per_day']->value ?? 100000 }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>
                </div>
            </div>

            <!-- Company Info -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Company Information</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ $settings['company_name']->value ?? '' }}" 
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Address</label>
                        <textarea name="company_address" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white" required>{{ $settings['company_address']->value ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
