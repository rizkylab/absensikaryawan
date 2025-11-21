<x-app-layout>
    <x-slot name="header">Check In</x-slot>
    
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

    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Check In</h2>

            <form action="{{ route('karyawan.attendance.check-in.submit') }}" method="POST" enctype="multipart/form-data" x-data="checkInForm()">
                @csrf

                <!-- QR Code Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Scan QR Code
                    </label>
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg text-center">
                        <div class="mb-4 inline-block bg-white p-4 rounded-lg">
                            @if($qrCode)
                                <div class="w-64 h-64">
                                    {!! app(\App\Services\QrCodeService::class)->generateQrCodeImage($qrCode->token) !!}
                                </div>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Valid for: {{ $qrCode ? $qrCode->valid_date->format('d M Y') : 'N/A' }}
                        </p>
                    </div>
                    <input type="hidden" name="qr_token" value="{{ $qrCode->token ?? '' }}" required>
                </div>

                <!-- GPS Location -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        GPS Location
                    </label>
                    <button type="button" @click="getLocation()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <span x-show="!locationObtained">Get My Location</span>
                        <span x-show="locationObtained">✓ Location Obtained</span>
                    </button>
                    <p x-show="address" x-text="address" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
                    <input type="hidden" name="latitude" x-model="latitude" required>
                    <input type="hidden" name="longitude" x-model="longitude" required>
                    <input type="hidden" name="address" x-model="address">
                </div>

                <!-- Photo Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Selfie Photo
                    </label>
                    <input type="file" 
                           name="photo" 
                           accept="image/*" 
                           capture="user"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Max 5MB</p>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition"
                        :disabled="!locationObtained">
                    Check In Now
                </button>
            </form>
        </div>
    </div>

    <script>
        function checkInForm() {
            return {
                latitude: null,
                longitude: null,
                address: '',
                locationObtained: false,

                getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                this.locationObtained = true;
                                this.getAddress();
                            },
                            (error) => {
                                alert('Error getting location: ' + error.message);
                            }
                        );
                    } else {
                        alert('Geolocation is not supported by this browser.');
                    }
                },

                async getAddress() {
                    try {
                        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.latitude}&lon=${this.longitude}`);
                        const data = await response.json();
                        this.address = data.display_name;
                    } catch (error) {
                        this.address = `${this.latitude}, ${this.longitude}`;
                    }
                }
            }
        }
    </script>
</x-app-layout>
