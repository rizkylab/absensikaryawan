<x-app-layout>
    <x-slot name="header">User Details</x-slot>
    
    <x-slot name="sidebar">
        <x-nav-link href="{{ route('admin.dashboard') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span>Dashboard</span>
        </x-nav-link>

        <x-nav-link href="{{ route('admin.users.index') }}" :active="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>Users</span>
        </x-nav-link>
    </x-slot>

    <div class="max-w-4xl">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">← Back to Users</a>
            <div class="flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition">
                    Edit User
                </a>
                @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this user?')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            Delete User
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- User Info Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="w-24 h-24 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-400 text-3xl font-bold">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $user->position }}</p>
                    <span class="inline-block mt-2 px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $user->role->name === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                        {{ $user->role->name === 'atasan' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                        {{ $user->role->name === 'karyawan' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}">
                        {{ ucfirst($user->role->name) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee ID</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->employee_id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Base Salary</p>
                    <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($user->base_salary, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Supervisor</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->supervisor ? $user->supervisor->name : 'None' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Joined</p>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $user->created_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Subordinates -->
        @if($user->subordinates->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Team Members ({{ $user->subordinates->count() }})</h3>
                <div class="space-y-3">
                    @foreach($user->subordinates as $subordinate)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-400 font-semibold">
                                    {{ substr($subordinate->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $subordinate->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subordinate->position }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $subordinate) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
                                View
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
