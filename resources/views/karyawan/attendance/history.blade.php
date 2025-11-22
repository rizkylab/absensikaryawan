<x-app-layout>
    <x-slot name="header">Attendance History</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('karyawan.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.attendance.index') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Attendance</span>
        </x-nav-link>

        <x-nav-link href="{{ route('karyawan.attendance.history') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span>History</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-6xl mx-auto" x-data="{ showModal: false, selectedAttendance: null }">
        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <form action="{{ route('karyawan.attendance.history') }}" method="GET" class="flex items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Month</label>
                    <input type="month" name="month" value="{{ $month }}" 
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    Filter
                </button>
            </form>
        </div>

        <!-- History Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Work Hours</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($attendance->check_in)
                                        <div class="flex flex-col">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                            </span>
                                            @if($attendance->check_in_status === 'late')
                                                <span class="text-xs text-red-500">Late</span>
                                            @else
                                                <span class="text-xs text-green-500">On Time</span>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($attendance->check_out)
                                        {{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($attendance->work_duration)
                                        {{ floor($attendance->work_duration / 60) }}h {{ $attendance->work_duration % 60 }}m
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->status === 'present')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Present
                                        </span>
                                    @elseif($attendance->status === 'alpha')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Alpha
                                        </span>
                                    @elseif($attendance->status === 'leave')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            Leave
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">
                                    @if($attendance->late_duration > 0)
                                        {{ $attendance->late_duration }} mins
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="selectedAttendance = {{ json_encode($attendance) }}; showModal = true" 
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No attendance records found for this month.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" @click="showModal = false"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl"
                 @click.away="showModal = false">
                
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white mb-4" id="modal-title">
                                Attendance Details
                            </h3>
                            
                            <template x-if="selectedAttendance">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Check In Details -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">Check In</h4>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Time</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedAttendance.check_in ? selectedAttendance.check_in.substring(0, 5) : '-'"></p>
                                            </div>
                                            
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Location</p>
                                                <template x-if="selectedAttendance.check_in_latitude && selectedAttendance.check_in_longitude">
                                                    <div>
                                                        <p class="text-sm text-gray-900 dark:text-white" x-text="selectedAttendance.check_in_latitude + ', ' + selectedAttendance.check_in_longitude"></p>
                                                        <a :href="'https://www.google.com/maps/search/?api=1&query=' + selectedAttendance.check_in_latitude + ',' + selectedAttendance.check_in_longitude" 
                                                           target="_blank"
                                                           class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                                            View on Map
                                                        </a>
                                                    </div>
                                                </template>
                                                <template x-if="!selectedAttendance.check_in_latitude">
                                                    <p class="text-sm text-gray-500">-</p>
                                                </template>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Photo</p>
                                                <template x-if="selectedAttendance.check_in_photo">
                                                    <img :src="'/storage/' + selectedAttendance.check_in_photo" 
                                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600"
                                                         alt="Check In Photo">
                                                </template>
                                                <template x-if="!selectedAttendance.check_in_photo">
                                                    <div class="w-full h-32 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">No Photo</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Check Out Details -->
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">Check Out</h4>
                                        
                                        <div class="space-y-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Time</p>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="selectedAttendance.check_out ? selectedAttendance.check_out.substring(0, 5) : '-'"></p>
                                            </div>
                                            
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Location</p>
                                                <template x-if="selectedAttendance.check_out_latitude && selectedAttendance.check_out_longitude">
                                                    <div>
                                                        <p class="text-sm text-gray-900 dark:text-white" x-text="selectedAttendance.check_out_latitude + ', ' + selectedAttendance.check_out_longitude"></p>
                                                        <a :href="'https://www.google.com/maps/search/?api=1&query=' + selectedAttendance.check_out_latitude + ',' + selectedAttendance.check_out_longitude" 
                                                           target="_blank"
                                                           class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                                            View on Map
                                                        </a>
                                                    </div>
                                                </template>
                                                <template x-if="!selectedAttendance.check_out_latitude">
                                                    <p class="text-sm text-gray-500">-</p>
                                                </template>
                                            </div>

                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Photo</p>
                                                <template x-if="selectedAttendance.check_out_photo">
                                                    <img :src="'/storage/' + selectedAttendance.check_out_photo" 
                                                         class="w-full h-32 object-cover rounded-lg border border-gray-200 dark:border-gray-600"
                                                         alt="Check Out Photo">
                                                </template>
                                                <template x-if="!selectedAttendance.check_out_photo">
                                                    <div class="w-full h-32 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center">
                                                        <span class="text-xs text-gray-500 dark:text-gray-400">No Photo</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" 
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto"
                            @click="showModal = false">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
