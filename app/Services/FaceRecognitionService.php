<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FaceRecognitionService
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('FACE_RECOGNITION_API_URL', 'http://localhost:5000/api/verify');
        $this->apiKey = env('FACE_RECOGNITION_API_KEY', 'dummy_key');
    }

    /**
     * Verify face recognition
     * This is a dummy implementation. Replace with real API call.
     */
    public function verify(string $photoPath, int $userId): array
    {
        // TODO: Implement real face recognition API call
        // For now, return dummy data
        
        // Simulate API call delay
        usleep(500000); // 0.5 seconds

        // Generate random score between 70-95
        $score = rand(70, 95);

        return [
            'success' => true,
            'score' => $score,
            'threshold' => 70,
            'verified' => $score >= 70,
            'message' => $score >= 70 ? 'Face verified successfully' : 'Face verification failed',
        ];

        // Real implementation would be:
        /*
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->attach(
                'photo', file_get_contents($photoPath), 'photo.jpg'
            )->post($this->apiUrl, [
                'user_id' => $userId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => 'Face recognition API error',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
        */
    }

    /**
     * Check if face recognition is enabled
     */
    public function isEnabled(): bool
    {
        return \App\Models\Setting::get('face_recognition_enabled', true);
    }

    /**
     * Get face recognition threshold
     */
    public function getThreshold(): int
    {
        return \App\Models\Setting::get('face_recognition_threshold', 70);
    }
}
