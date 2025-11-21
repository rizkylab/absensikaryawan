<x-app-layout>
    <x-slot name="header">Attendance Details</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('admin.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('admin.attendances.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>Attendances</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.attendances.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Attendances</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Attendance Details</h2>

            <!-- Employee Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $attendance->user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $attendance->user->employee_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $attendance->user->position }}</p>
                </div>
            </div>

            <!-- Attendance Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $attendance->date->format('d F Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    @if($attendance->late_duration > 0)
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                            Late {{ $attendance->late_duration }} minutes
                        </span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            On Time
                        </span>
                    @endif
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Check In</p>
                    <p class="text-gray-900 dark:text-white font-medium">
                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i:s') : '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Check Out</p>
                    <p class="text-gray-900 dark:text-white font-medium">
                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i:s') : '-' }}
                    </p>
                </div>

                @if($attendance->check_in_location)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Check In Location</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $attendance->check_in_location }}</p>
                    </div>
                @endif

                @if($attendance->check_out_location)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Check Out Location</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $attendance->check_out_location }}</p>
                    </div>
                @endif
            </div>

            <!-- GPS Coordinates -->
            @if($attendance->check_in_lat && $attendance->check_in_lng)
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">GPS Coordinates</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Check In</p>
                            <p class="text-gray-900 dark:text-white">{{ $attendance->check_in_lat }}, {{ $attendance->check_in_lng }}</p>
                        </div>
                        @if($attendance->check_out_lat && $attendance->check_out_lng)
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Check Out</p>
                                <p class="text-gray-900 dark:text-white">{{ $attendance->check_out_lat }}, {{ $attendance->check_out_lng }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Selfie Photos -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($attendance->selfie_check_in)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Check In Selfie</p>
                        <img src="{{ Storage::url($attendance->selfie_check_in) }}" alt="Check In Selfie" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                @if($attendance->selfie_check_out)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Check Out Selfie</p>
                        <img src="{{ Storage::url($attendance->selfie_check_out) }}" alt="Check Out Selfie" 
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
