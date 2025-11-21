<x-app-layout>
    <x-slot name="header">Leave Request Review</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('atasan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('atasan.leaves.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Leave</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('atasan.leaves.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Leave</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Leave Request</h2>
                @if($leave->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending Approval</span>
                @elseif($leave->status === 'approved')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Approved</span>
                @else
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejected</span>
                @endif
            </div>

            <!-- Employee Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $leave->user->employee_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->user->position }}</p>
                </div>
            </div>

            <!-- Request Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Start Date</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->start_date->format('d F Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">End Date</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->end_date->format('d F Y') }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Duration</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->days }} days</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Leave Type</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ ucfirst($leave->type) }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Requested On</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $leave->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Reason</p>
                <p class="text-gray-900 dark:text-white">{{ $leave->reason }}</p>
            </div>

            @if($leave->attachment)
                <div class="mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Attachment</p>
                    <a href="{{ Storage::url($leave->attachment) }}" target="_blank" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path>
                        </svg>
                        Download Attachment
                    </a>
                </div>
            @endif

            <!-- Approval Actions -->
            @if($leave->status === 'pending')
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
                                    onclick="submitApproval('{{ route('atasan.leaves.approve', $leave) }}')"
                                    class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition">
                                Approve
                            </button>
                            <button type="button" 
                                    onclick="submitApproval('{{ route('atasan.leaves.reject', $leave) }}')"
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
                            <p class="text-gray-900 dark:text-white font-medium">{{ $leave->approver->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Reviewed At</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $leave->approved_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                    @if($leave->notes)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Notes</p>
                            <p class="text-gray-900 dark:text-white">{{ $leave->notes }}</p>
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
