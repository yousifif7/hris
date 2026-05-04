<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; background: #fff; }
  .page { padding: 40px 48px; }
  .header { border-bottom: 2px solid #1a56db; padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; }
  .company-name { font-size: 18px; font-weight: 700; color: #1a56db; }
  .doc-title { font-size: 14px; font-weight: 600; color: #444; margin-top: 2px; }
  .meta-right { text-align: right; font-size: 11px; color: #555; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
  .badge-finalized { background: #d1fae5; color: #065f46; }
  .badge-draft { background: #fef3c7; color: #92400e; }
  h3 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #777; margin-bottom: 8px; }
  .info-grid { display: table; width: 100%; margin-bottom: 20px; }
  .info-row { display: table-row; }
  .info-cell { display: table-cell; padding: 4px 0; font-size: 12px; width: 25%; }
  .info-label { color: #777; font-size: 10px; display: block; margin-bottom: 1px; }
  .pay-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  .pay-table th { background: #f0f4ff; text-align: left; padding: 7px 10px; font-size: 11px; color: #444; border-bottom: 1px solid #ddd; }
  .pay-table th.right { text-align: right; }
  .pay-table td { padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 12px; }
  .pay-table td.right { text-align: right; }
  .pay-table tr.total-row td { font-weight: 700; background: #f8faff; }
  .pay-table tr.net-row td { font-weight: 700; font-size: 14px; background: #f0f4ff; }
  .pay-table tr.net-row td.amount { color: #1a56db; }
  .pay-table tr.deduct-row td { color: #c0392b; }
  .footer { margin-top: 32px; border-top: 1px solid #eee; padding-top: 14px; font-size: 10px; color: #999; }
  .sig-line { margin-top: 40px; display: flex; justify-content: space-between; }
  .sig-block { width: 45%; border-top: 1px solid #888; padding-top: 6px; font-size: 10px; color: #555; text-align: center; }
</style>
</head>
<body>
<div class="page">
  <!-- Header -->
  <div class="header">
    <div>
      <div class="company-name">McCrory Construction</div>
      <div class="doc-title">Employee Pay Stub</div>
    </div>
    <div class="meta-right">
      <span class="badge {{ $payroll->status === 'finalized' ? 'badge-finalized' : 'badge-draft' }}">{{ strtoupper($payroll->status) }}</span><br>
      Record #{{ $payroll->id }}<br>
      Generated: {{ now()->format('M j, Y') }}
    </div>
  </div>

  <!-- Employee Info -->
  <h3>Employee Information</h3>
  <table class="info-grid" style="width:100%;margin-bottom:20px">
    <tr class="info-row">
      <td class="info-cell"><span class="info-label">Name</span>{{ $payroll->employee?->first_name }} {{ $payroll->employee?->last_name }}</td>
      <td class="info-cell"><span class="info-label">Role</span>{{ $payroll->employee?->role ?? '—' }}</td>
      <td class="info-cell"><span class="info-label">Pay Type</span>{{ ucfirst($payroll->pay_type) }}</td>
      <td class="info-cell"><span class="info-label">Rate</span>${{ number_format($payroll->employee_rate, 2) }}{{ $payroll->pay_type === 'salary' ? '/yr' : '/hr' }}</td>
    </tr>
  </table>

  <!-- Period Info -->
  <h3>Pay Period</h3>
  <table class="info-grid" style="width:100%;margin-bottom:20px">
    <tr class="info-row">
      <td class="info-cell"><span class="info-label">Frequency</span>{{ ucwords(str_replace('_', '-', $payroll->frequency)) }}</td>
      <td class="info-cell"><span class="info-label">Period Start</span>{{ $payroll->period_start->format('M j, Y') }}</td>
      <td class="info-cell"><span class="info-label">Period End</span>{{ $payroll->period_end->format('M j, Y') }}</td>
      <td class="info-cell"><span class="info-label">Pay Date</span>{{ $payroll->pay_date ? $payroll->pay_date->format('M j, Y') : '—' }}</td>
    </tr>
  </table>

  <!-- Pay Breakdown -->
  <h3>Pay Breakdown</h3>
  <table class="pay-table">
    <thead>
      <tr>
        <th>Description</th>
        <th class="right">Hours</th>
        <th class="right">Rate</th>
        <th class="right">Amount</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Regular Pay</td>
        <td class="right">{{ $payroll->pay_type === 'hourly' ? number_format($payroll->regular_hours, 2) : '—' }}</td>
        <td class="right">${{ number_format($payroll->effective_hourly_rate, 4) }}/hr</td>
        <td class="right">${{ number_format($payroll->regular_pay, 2) }}</td>
      </tr>
      @if($payroll->overtime_hours > 0)
      <tr>
        <td>Overtime Pay (1.5×)</td>
        <td class="right">{{ number_format($payroll->overtime_hours, 2) }}</td>
        <td class="right">${{ number_format($payroll->effective_hourly_rate * 1.5, 4) }}/hr</td>
        <td class="right">${{ number_format($payroll->overtime_pay, 2) }}</td>
      </tr>
      @endif
      @if($payroll->bonus > 0)
      <tr>
        <td>Bonus / Additional Pay</td>
        <td class="right">—</td>
        <td class="right">—</td>
        <td class="right">${{ number_format($payroll->bonus, 2) }}</td>
      </tr>
      @endif
      <tr class="total-row">
        <td colspan="3">Gross Pay</td>
        <td class="right">${{ number_format($payroll->gross_pay, 2) }}</td>
      </tr>
      @if($payroll->deductions > 0)
      <tr class="deduct-row">
        <td colspan="3">Deductions</td>
        <td class="right">−${{ number_format($payroll->deductions, 2) }}</td>
      </tr>
      @endif
      <tr class="net-row">
        <td colspan="3">Net Pay</td>
        <td class="right amount">${{ number_format($payroll->net_pay, 2) }}</td>
      </tr>
    </tbody>
  </table>

  @if($payroll->notes)
  <div style="font-size:11px;color:#555;margin-bottom:20px"><strong>Notes:</strong> {{ $payroll->notes }}</div>
  @endif

  <!-- Signatures -->
  <div class="sig-line">
    <div class="sig-block">Employee Signature</div>
    <div class="sig-block">Authorized By</div>
  </div>

  <!-- Footer -->
  <div class="footer">
    This document is confidential and intended solely for the named employee. McCrory Construction — HR Department.
  </div>
</div>
</body>
</html>
