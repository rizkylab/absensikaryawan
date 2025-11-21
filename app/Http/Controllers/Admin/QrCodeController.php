<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;

class QrCodeController extends Controller
{
    public function __construct(
        protected QrCodeService $qrService
    ) {}

    public function index()
    {
        $qrCode = $this->qrService->getTodayQrCode();
        
        if ($qrCode) {
            $qrImage = $this->qrService->generateQrCodeImage($qrCode->token);
        } else {
            $qrImage = null;
        }

        return view('admin.qr-code.index', compact('qrCode', 'qrImage'));
    }

    public function generate()
    {
        $qrCode = $this->qrService->generateDaily();

        \App\Models\AuditLog::log('qr_code_generated', 'QrCode', $qrCode->id);

        return redirect()
            ->route('admin.qr-code.index')
            ->with('success', 'QR Code generated successfully');
    }
}
