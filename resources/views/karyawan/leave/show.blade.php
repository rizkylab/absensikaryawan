<x-app-layout>
    <x-slot name="header">Leave Details</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('karyawan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.leaves.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span>Leave</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('karyawan.leaves.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Leave</a>
            @if($leave->status === 'pending')
                <div class="flex gap-2">
                    <a href="{{ route('karyawan.leaves.edit', $leave) }}" 
                       class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                        Edit
                    </a>
                    <form action="{{ route('karyawan.leaves.destroy', $leave) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure?')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Delete
                        </button>
                    </form>
                </div>
            @endif
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                @if($leave->approved_by)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved/Rejected By</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $leave->approver->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved/Rejected At</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $leave->approved_at->format('d M Y H:i') }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Reason</p>
                <p class="text-gray-900 dark:text-white">{{ $leave->reason }}</p>
            </div>

            @if($leave->attachment)
                <div class="mt-6">
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

            @if($leave->notes)
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Notes from Supervisor</p>
                    <p class="text-gray-900 dark:text-white">{{ $leave->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
