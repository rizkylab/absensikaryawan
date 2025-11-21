<x-app-layout>
    <x-slot name="header">Salary Slip Details</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('karyawan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.payrolls.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Payroll</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('karyawan.payrolls.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Payroll</a>
            <a href="{{ route('karyawan.payrolls.download', $payroll) }}" 
               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                Download PDF
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Salary Slip</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($payroll->period . '-01')->format('F Y') }}</p>
            </div>

            <!-- Employee Info -->
            <div class="grid grid-cols-2 gap-6 mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee Name</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $payroll->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee ID</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $payroll->user->employee_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $payroll->user->position }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Period</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ \Carbon\Carbon::parse($payroll->period . '-01')->format('F Y') }}</p>
                </div>
            </div>

            <!-- Earnings -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Earnings</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Base Salary</span>
                        <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                    </div>
                    @if($payroll->overtime_bonus > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Overtime Bonus</span>
                            <span class="text-green-600 dark:text-green-400 font-medium">+ Rp {{ number_format($payroll->overtime_bonus, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Deductions -->
            @if($payroll->late_penalty > 0 || $payroll->leave_deduction > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Deductions</h3>
                    <div class="space-y-3">
                        @if($payroll->late_penalty > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Late Penalty</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">- Rp {{ number_format($payroll->late_penalty, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($payroll->leave_deduction > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Leave Deduction</span>
                                <span class="text-red-600 dark:text-red-400 font-medium">- Rp {{ number_format($payroll->leave_deduction, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Total -->
            <div class="pt-6 border-t-2 border-gray-300 dark:border-gray-600">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Total Salary</span>
                    <span class="text-3xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Attendance Summary -->
            <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Attendance Summary</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $payroll->total_days }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Working Days</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $payroll->late_count }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Late Days</p>
                    </div>
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $payroll->leave_count }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Leave Days</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
