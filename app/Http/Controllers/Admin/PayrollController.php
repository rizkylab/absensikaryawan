<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct(
        protected PayrollService $payrollService
    ) {}

    public function index(Request $request)
    {
        $period = $request->get('period', Carbon::now()->format('Y-m'));

        $payrolls = Payroll::where('period', $period)
            ->with('user')
            ->paginate(15);

        $summary = $this->payrollService->getPayrollSummary($period);

        return view('admin.payrolls.index', compact('payrolls', 'period', 'summary'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'period' => 'required|date_format:Y-m',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $period = $request->period;
        $userIds = $request->user_ids;

        // If no specific users, generate for all karyawan
        if (empty($userIds)) {
            $users = User::whereHas('role', function($q) {
                $q->where('name', 'karyawan');
            })->get();
        } else {
            $users = User::whereIn('id', $userIds)->get();
        }

        $generated = 0;
        foreach ($users as $user) {
            $this->payrollService->calculatePayroll($user, $period);
            $generated++;
        }

        return redirect()
            ->route('admin.payrolls.index', ['period' => $period])
            ->with('success', "Payroll generated for {$generated} employees");
    }

    public function show(Payroll $payroll)
    {
        $payroll->load('user', 'generator');
        return view('admin.payrolls.show', compact('payroll'));
    }

    public function download(Payroll $payroll)
    {
        return $this->payrollService->generateSlipPdf($payroll);
    }
}
