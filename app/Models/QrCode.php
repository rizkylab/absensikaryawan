<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class QrCode extends Model
{
    protected $fillable = [
        'token',
        'valid_date',
        'is_active',
    ];

    protected $casts = [
        'valid_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Check if QR code is valid for today
     */
    public function isValidToday(): bool
    {
        return $this->is_active && 
               $this->valid_date->isToday();
    }

    /**
     * Generate daily QR code
     */
    public static function generateDaily(): self
    {
        $today = Carbon::today();
        
        // Deactivate old QR codes
        static::where('valid_date', '<', $today)->update(['is_active' => false]);
        
        // Check if today's QR code already exists
        $existingQr = static::where('valid_date', $today)->first();
        if ($existingQr) {
            return $existingQr;
        }

        // Generate new QR code
        $token = bin2hex(random_bytes(32));
        
        return static::create([
            'token' => $token,
            'valid_date' => $today,
            'is_active' => true,
        ]);
    }

    /**
     * Validate QR token
     */
    public static function validateToken(string $token): bool
    {
        $qr = static::where('token', $token)
                    ->where('is_active', true)
                    ->first();

        return $qr && $qr->isValidToday();
    }
}
