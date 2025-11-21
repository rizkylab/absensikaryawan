<?php

namespace App\Services;

use App\Models\OfficeLocation;

class GpsValidationService
{
    /**
     * Validate if coordinates are within office radius
     */
    public function validateLocation(float $latitude, float $longitude): array
    {
        $office = OfficeLocation::where('is_active', true)->first();

        if (!$office) {
            return [
                'valid' => false,
                'message' => 'No active office location found',
                'distance' => null,
            ];
        }

        $distance = $office->calculateDistance($latitude, $longitude);
        $isWithin = $office->isWithinRadius($latitude, $longitude);

        return [
            'valid' => $isWithin,
            'message' => $isWithin 
                ? 'Location valid' 
                : 'You are outside office radius',
            'distance' => round($distance, 2),
            'radius' => $office->radius,
            'office_name' => $office->name,
        ];
    }

    /**
     * Get active office location
     */
    public function getActiveOffice(): ?OfficeLocation
    {
        return OfficeLocation::where('is_active', true)->first();
    }
}
