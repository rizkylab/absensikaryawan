<x-app-layout>
    <x-slot name="header">Overtime Details</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('karyawan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.overtimes.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Overtime</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('karyawan.overtimes.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Overtime</a>
            @if($overtime->status === 'pending')
                <div class="flex gap-2">
                    <a href="{{ route('karyawan.overtimes.edit', $overtime) }}" 
                       class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                        Edit
                    </a>
                    <form action="{{ route('karyawan.overtimes.destroy', $overtime) }}" method="POST">
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Overtime Request</h2>
                @if($overtime->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pending Approval</span>
                @elseif($overtime->status === 'approved')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Approved</span>
                @else
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">Rejected</span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                @if($overtime->approved_by)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved/Rejected By</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->approver->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Approved/Rejected At</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $overtime->approved_at->format('d M Y H:i') }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Reason</p>
                <p class="text-gray-900 dark:text-white">{{ $overtime->reason }}</p>
            </div>

            @if($overtime->notes)
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Notes from Supervisor</p>
                    <p class="text-gray-900 dark:text-white">{{ $overtime->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
