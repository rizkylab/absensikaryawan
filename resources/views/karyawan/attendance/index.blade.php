<x-app-layout>
    <x-slot name="header">Attendance</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('karyawan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.attendance.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <span>Attendance</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Today's Status Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Today's Attendance</h2>
            
            @if($todayAttendance)
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Check In</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $todayAttendance->check_in ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') : '-' }}
                        </p>
                        @if($todayAttendance->late_duration > 0)
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                Late {{ $todayAttendance->late_duration }} min
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Check Out</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $todayAttendance->check_out ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('H:i') : '-' }}
                        </p>
                        @if($todayAttendance->work_duration)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                Work duration: {{ floor($todayAttendance->work_duration / 60) }}h {{ $todayAttendance->work_duration % 60 }}m
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">No attendance record for today</p>
            @endif

            <!-- Action Buttons -->
            <div class="mt-6 flex gap-4">
                @if($canCheckIn)
                    <a href="{{ route('karyawan.attendance.check-in') }}" 
                       class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-center font-semibold rounded-lg transition">
                        Check In
                    </a>
                @endif

                @if($canCheckOut)
                    <a href="{{ route('karyawan.attendance.check-out') }}" 
                       class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold rounded-lg transition">
                        Check Out
                    </a>
                @endif

                <a href="{{ route('karyawan.attendance.history') }}" 
                   class="flex-1 px-4 py-3 bg-gray-600 hover:bg-gray-700 text-white text-center font-semibold rounded-lg transition">
                    View History
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
