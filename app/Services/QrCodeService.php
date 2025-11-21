<?php

namespace App\Services;

use App\Models\QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Carbon\Carbon;

class QrCodeService
{
    /**
     * Generate daily QR code
     */
    public function generateDaily(): QrCode
    {
        return QrCode::generateDaily();
    }

    /**
     * Validate QR token
     */
    public function validateToken(string $token): bool
    {
        return QrCode::validateToken($token);
    }

    /**
     * Generate QR code image (SVG format - no imagick required)
     */
    public function generateQrCodeImage(string $token): string
    {
        // Use SVG format to avoid imagick dependency
        return QrCodeGenerator::size(300)
            ->format('svg')
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->margin(2)
            ->errorCorrection('M')
            ->generate($token);
    }

    /**
     * Get today's QR code
     */
    public function getTodayQrCode(): ?QrCode
    {
        return QrCode::where('valid_date', Carbon::today())
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get or create today's QR code
     */
    public function getOrCreateTodayQrCode(): QrCode
    {
        $qr = $this->getTodayQrCode();
        
        if (!$qr) {
            $qr = $this->generateDaily();
        }

        return $qr;
    }
}
