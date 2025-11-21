<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    public function __construct(
        protected GpsValidationService $gpsService,
        protected QrCodeService $qrService,
        protected FaceRecognitionService $faceService
    ) {}

    /**
     * Process check-in
     */
    public function checkIn(User $user, array $data): array
    {
        // Validate if already checked in today
        $existing = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if ($existing && $existing->check_in) {
            return [
                'success' => false,
                'message' => 'You have already checked in today',
            ];
        }

        // Validate GPS
        $gpsValidation = $this->gpsService->validateLocation(
            $data['latitude'],
            $data['longitude']
        );

        if (!$gpsValidation['valid']) {
            return [
                'success' => false,
                'message' => $gpsValidation['message'],
                'distance' => $gpsValidation['distance'],
            ];
        }

        // Validate QR Code
        if (!$this->qrService->validateToken($data['qr_token'])) {
            return [
                'success' => false,
                'message' => 'Invalid or expired QR code',
            ];
        }

        // Store photo from base64 data
        $photoPath = null;
        $faceScore = null;
        if (isset($data['photo_data'])) {
            $photoPath = $this->storePhotoFromBase64($data['photo_data'], 'check_in', $user->id);
            
            // Validate Face Recognition (if enabled)
            if ($this->faceService->isEnabled()) {
                $faceResult = $this->faceService->verify($photoPath, $user->id);
                
                if (!$faceResult['verified']) {
                    return [
                        'success' => false,
                        'message' => 'Face verification failed',
                        'score' => $faceResult['score'],
                    ];
                }
                
                $faceScore = $faceResult['score'];
            }
        }

        // Calculate late duration
        $workStartTime = Setting::get('work_start_time', '08:00');
        $checkInTime = Carbon::now();
        $lateDuration = $this->calculateLateDuration($checkInTime, $workStartTime);

        // Create or update attendance
        $attendance = Attendance::updateOrCreate(
            [
                'user_id' => $user->id,
                'date' => Carbon::today(),
            ],
            [
                'check_in' => $checkInTime->format('H:i:s'),
                'check_in_latitude' => $data['latitude'],
                'check_in_longitude' => $data['longitude'],
                'check_in_photo' => $photoPath ?? null,
                'check_in_face_score' => $faceScore,
                'check_in_address' => $data['address'] ?? null,
                'qr_token' => $data['qr_token'],
                'late_duration' => $lateDuration,
                'status' => 'valid',
            ]
        );

        // Log audit
        \App\Models\AuditLog::log('check_in', 'Attendance', $attendance->id, null, $attendance->toArray());

        return [
            'success' => true,
            'message' => 'Check-in successful',
            'attendance' => $attendance,
            'late_duration' => $lateDuration,
        ];
    }

    /**
     * Process check-out
     */
    public function checkOut(User $user, array $data): array
    {
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return [
                'success' => false,
                'message' => 'You have not checked in today',
            ];
        }

        if ($attendance->check_out) {
            return [
                'success' => false,
                'message' => 'You have already checked out today',
            ];
        }

        // Validate GPS
        $gpsValidation = $this->gpsService->validateLocation(
            $data['latitude'],
            $data['longitude']
        );

        // Store photo from base64 data
        $photoPath = null;
        $faceScore = null;
        if (isset($data['photo_data'])) {
            $photoPath = $this->storePhotoFromBase64($data['photo_data'], 'check_out', $user->id);
            
            // Validate Face Recognition (if enabled)
            if ($this->faceService->isEnabled()) {
                $faceResult = $this->faceService->verify($photoPath, $user->id);
                $faceScore = $faceResult['score'];
            }
        }

        // Calculate work duration and early leave
        $checkOutTime = Carbon::now();
        $workEndTime = Setting::get('work_end_time', '17:00');
        $workDuration = $this->calculateWorkDuration($attendance->check_in, $checkOutTime);
        $earlyLeaveDuration = $this->calculateEarlyLeaveDuration($checkOutTime, $workEndTime);

        // Update attendance
        $oldValues = $attendance->toArray();
        $attendance->update([
            'check_out' => $checkOutTime->format('H:i:s'),
            'check_out_latitude' => $data['latitude'],
            'check_out_longitude' => $data['longitude'],
            'check_out_photo' => $photoPath ?? null,
            'check_out_face_score' => $faceScore,
            'check_out_address' => $data['address'] ?? null,
            'work_duration' => $workDuration,
            'early_leave_duration' => $earlyLeaveDuration,
        ]);

        // Log audit
        \App\Models\AuditLog::log('check_out', 'Attendance', $attendance->id, $oldValues, $attendance->toArray());

        return [
            'success' => true,
            'message' => 'Check-out successful',
            'attendance' => $attendance,
            'work_duration' => $workDuration,
        ];
    }

    /**
     * Store photo from base64 data
     */
    protected function storePhotoFromBase64(string $base64Data, string $type, int $userId): string
    {
        // Remove data:image/jpeg;base64, prefix
        $image = str_replace('data:image/jpeg;base64,', '', $base64Data);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);
        
        // Generate unique filename
        $filename = $type . '_' . $userId . '_' . time() . '.jpg';
        $path = 'attendance/' . $type . '/' . $filename;
        
        // Store file
        Storage::disk('public')->put($path, $imageData);
        
        return $path;
    }

    /**
     * Calculate late duration in minutes
     */
    protected function calculateLateDuration(Carbon $checkIn, string $workStartTime): int
    {
        $startTime = Carbon::createFromFormat('H:i', $workStartTime);
        $tolerance = Setting::get('late_tolerance', 15); // minutes

        if ($checkIn->lessThanOrEqualTo($startTime->copy()->addMinutes($tolerance))) {
            return 0;
        }

        return $checkIn->diffInMinutes($startTime);
    }

    /**
     * Calculate work duration in minutes
     */
    protected function calculateWorkDuration(string $checkIn, Carbon $checkOut): int
    {
        // Parse check_in - it could be datetime or time only
        $start = Carbon::parse($checkIn);
        return $checkOut->diffInMinutes($start);
    }

    /**
     * Calculate early leave duration in minutes
     */
    protected function calculateEarlyLeaveDuration(Carbon $checkOut, string $workEndTime): int
    {
        $endTime = Carbon::createFromFormat('H:i', $workEndTime);

        if ($checkOut->greaterThanOrEqualTo($endTime)) {
            return 0;
        }

        return $endTime->diffInMinutes($checkOut);
    }

    /**
     * Get user attendance history
     */
    public function getUserAttendanceHistory(User $user, ?string $month = null)
    {
        $query = Attendance::where('user_id', $user->id);

        if ($month) {
            $date = Carbon::createFromFormat('Y-m', $month);
            $query->whereYear('date', $date->year)
                  ->whereMonth('date', $date->month);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
