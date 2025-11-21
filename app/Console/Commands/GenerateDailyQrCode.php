<?php

namespace App\Console\Commands;

use App\Services\QrCodeService;
use Illuminate\Console\Command;

class GenerateDailyQrCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qr:generate-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate daily QR code for attendance';

    /**
     * Execute the console command.
     */
    public function handle(QrCodeService $qrCodeService)
    {
        $this->info('Generating daily QR code...');

        $qr = $qrCodeService->generateDaily();

        $this->info('QR Code generated successfully!');
        $this->table(
            ['ID', 'Token', 'Valid Date', 'Status'],
            [
                [
                    $qr->id,
                    substr($qr->token, 0, 20) . '...',
                    $qr->valid_date->format('Y-m-d'),
                    $qr->is_active ? 'Active' : 'Inactive',
                ]
            ]
        );

        return Command::SUCCESS;
    }
}
