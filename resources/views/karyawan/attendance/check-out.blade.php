<x-app-layout>
    <x-slot name="header">Check Out</x-slot>
    
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
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Check Out</h2>

            <!-- Check-in Info -->
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-blue-800 dark:text-blue-200">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    You checked in at {{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('H:i') }}
                </p>
            </div>

            <form action="{{ route('karyawan.attendance.check-out.submit') }}" method="POST" enctype="multipart/form-data" x-data="checkOutForm()" x-init="init()">
                @csrf

                <!-- GPS Location -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        GPS Location *
                    </label>
                    <button type="button" @click="getLocation()" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <span x-show="!locationObtained">Get My Location</span>
                        <span x-show="locationObtained">✓ Location Obtained</span>
                    </button>
                    <p x-show="address" x-text="address" class="mt-2 text-sm text-gray-600 dark:text-gray-400"></p>
                    <input type="hidden" name="latitude" x-model="latitude" required>
                    <input type="hidden" name="longitude" x-model="longitude" required>
                    <input type="hidden" name="address" x-model="address">
                    @error('latitude')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Camera Capture -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Selfie Photo *
                    </label>
                    
                    <div class="space-y-4">
                        <!-- Camera Preview -->
                        <div x-show="!photoCaptured" class="space-y-3">
                            <div class="relative bg-gray-900 rounded-lg overflow-hidden">
                                <video x-ref="video" autoplay playsinline class="w-full h-64 object-cover"></video>
                            </div>
                            <!-- Capture Button -->
                            <button type="button" 
                                    @click="capturePhoto()" 
                                    class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                📸 Capture Photo
                            </button>
                        </div>

                        <!-- Captured Photo Preview -->
                        <div x-show="photoCaptured" class="space-y-3">
                            <div class="relative">
                                <img x-ref="preview" class="w-full h-64 object-cover rounded-lg">
                            </div>
                            <button type="button" 
                                    @click="retakePhoto()" 
                                    class="w-full px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                🔄 Retake Photo
                            </button>
                        </div>

                        <!-- Hidden Canvas -->
                        <canvas x-ref="canvas" class="hidden"></canvas>
                        
                        <!-- Hidden Input for Photo Data -->
                        <input type="hidden" name="photo_data" x-model="photoData" required>
                    </div>

                    @error('photo_data')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        📸 Klik tombol "Capture Photo" untuk mengambil foto
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full px-4 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition"
                        :disabled="!locationObtained || !photoCaptured">
                    <span x-show="!locationObtained || !photoCaptured">Please complete GPS and Photo</span>
                    <span x-show="locationObtained && photoCaptured">Check Out Now</span>
                </button>
            </form>
        </div>
    </div>

    <script>
        function checkOutForm() {
            return {
                latitude: null,
                longitude: null,
                address: '',
                locationObtained: false,
                photoData: '',
                photoCaptured: false,
                stream: null,

                async init() {
                    await this.startCamera();
                },

                async startCamera() {
                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { facingMode: 'user' },
                            audio: false 
                        });
                        this.$refs.video.srcObject = this.stream;
                    } catch (error) {
                        alert('Error accessing camera: ' + error.message);
                    }
                },

                capturePhoto() {
                    const video = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const context = canvas.getContext('2d');

                    // Set canvas size to video size
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Draw video frame to canvas
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Convert to base64
                    this.photoData = canvas.toDataURL('image/jpeg', 0.8);

                    // Show preview
                    this.$refs.preview.src = this.photoData;
                    this.photoCaptured = true;

                    // Stop camera stream
                    this.stopCamera();
                },

                retakePhoto() {
                    this.photoData = '';
                    this.photoCaptured = false;
                    this.startCamera();
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                },

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
