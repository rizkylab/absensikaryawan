<x-app-layout>
    <x-slot name="header">Payroll Management</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('admin.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('admin.payrolls.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Payrolls</span>
        </x-nav-link>
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payroll Management</h2>
        <div class="flex items-center gap-4">
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="period" value="{{ $period }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    Filter
                </button>
            </form>
            <button onclick="document.getElementById('generateModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                Generate Payroll
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    @if(isset($summary))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Employees</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $summary['total_employees'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Payroll</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($summary['total_payroll'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <p class="text-sm text-gray-500 dark:text-gray-400">Average Salary</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($summary['average_salary'], 0, ',', '.') }}</p>
            </div>
        </div>
    @endif

    <!-- Payroll Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Base Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($payrolls as $payroll)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payroll->user->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payroll->user->employee_id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::parse($payroll->period . '-01')->format('F Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            Rp {{ number_format($payroll->total_salary, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('admin.payrolls.show', $payroll) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View</a>
                            <a href="{{ route('admin.payrolls.download', $payroll) }}" class="text-green-600 dark:text-green-400 hover:underline">Download</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No payroll records found for this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $payrolls->links() }}
    </div>

    <!-- Generate Modal -->
    <div id="generateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Generate Payroll</h3>
            <form action="{{ route('admin.payrolls.generate') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Period</label>
                        <input type="month" name="period" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-700 text-gray-800 dark:text-white rounded-lg transition">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            Generate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
