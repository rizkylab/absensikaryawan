<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $payroll->period }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-address {
            font-size: 11px;
            color: #666;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 0;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-table th,
        .salary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .salary-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .salary-table td.amount {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
            border-top: 1px solid #333;
            width: 200px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $company_name }}</div>
        <div class="company-address">{{ $company_address }}</div>
    </div>

    <div class="title">SLIP GAJI</div>

    <table class="info-table">
        <tr>
            <td>Nama Karyawan</td>
            <td>: {{ $user->name }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: {{ $user->employee_id }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>: {{ $user->position }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: {{ $payroll->getFormattedPeriod() }}</td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th style="width: 150px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="amount">{{ $payroll->getFormattedBaseSalary() }}</td>
            </tr>
            <tr>
                <td>Hari Masuk ({{ $payroll->attendance_days }}/{{ $payroll->total_work_days }} hari)</td>
                <td class="amount">-</td>
            </tr>
            <tr>
                <td>Bonus Lembur</td>
                <td class="amount">Rp {{ number_format($payroll->overtime_bonus, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="background-color: #f5f5f5; font-weight: bold;">Potongan</td>
            </tr>
            <tr>
                <td>Potongan Terlambat</td>
                <td class="amount">Rp {{ number_format($payroll->late_penalty, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Pulang Cepat</td>
                <td class="amount">Rp {{ number_format($payroll->early_leave_penalty, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Cuti</td>
                <td class="amount">Rp {{ number_format($payroll->leave_deduction, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAL GAJI</td>
                <td class="amount">{{ $payroll->getFormattedTotalSalary() }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ now()->format('d F Y') }}</p>
        <p>Hormat kami,</p>
        <div class="signature">
            <p>HRD</p>
        </div>
    </div>
</body>
</html>
