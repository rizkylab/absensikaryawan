<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Services\PayrollService;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index()
    {
        $payrolls = Payroll::where('user_id', auth()->id())
            ->orderBy('period', 'desc')
            ->paginate(12);

        return view('karyawan.payroll.index', compact('payrolls'));
    }

    public function show(Payroll $payroll)
    {
        // Ensure user can only view their own payroll
        if ($payroll->user_id !== auth()->id()) {
            abort(403);
        }

        return view('karyawan.payroll.show', compact('payroll'));
    }

    public function download(Payroll $payroll)
    {
        // Ensure user can only download their own payroll
        if ($payroll->user_id !== auth()->id()) {
            abort(403);
        }

        return $this->payrollService->generateSlipPdf($payroll);
    }
}
