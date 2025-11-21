<x-app-layout>
    <x-slot name="header">Overtime Request Review</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('atasan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('atasan.overtimes.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Overtime</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('atasan.overtimes.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Overtime</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Overtime Request</h2>
                @if($overtime->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending Approval</span>
                @elseif($overtime->status === 'approved')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Approved</span>
                @else
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejected</span>
                @endif
            </div>

            <!-- Employee Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $overtime->user->employee_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->user->position }}</p>
                </div>
            </div>

            <!-- Request Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->date->format('d F Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Time</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->start_time }} - {{ $overtime->end_time }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ floor($overtime->duration / 60) }} hours {{ $overtime->duration % 60 }} minutes</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Requested On</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Reason</p>
                <p class="text-gray-900 dark:text-white">{{ $overtime->reason }}</p>
            </div>

            <!-- Approval Actions -->
            @if($overtime->status === 'pending')
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review Request</h3>
                    
                    <form id="approvalForm" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="button" 
                                    onclick="submitApproval('{{ route('atasan.overtimes.approve', $overtime) }}')"
                                    class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                                Approve
                            </button>
                            <button type="button" 
                                    onclick="submitApproval('{{ route('atasan.overtimes.reject', $overtime) }}')"
                                    class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                                Reject
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed By</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed At</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->approved_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @if($overtime->notes)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Notes</p>
                            <p class="text-gray-900 dark:text-white">{{ $overtime->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function submitApproval(action) {
            const form = document.getElementById('approvalForm');
            form.action = action;
            form.submit();
        }
    </script>
</x-app-layout>
