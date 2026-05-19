<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Application — {{ $candidate->full_name }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --teal:       #2A7D6F;
    --teal-light: #3FA08E;
    --teal-pale:  #E8F5F3;
    --teal-mid:   #C2E8E2;
    --dark:       #1A2E2A;
    --mid:        #4A6660;
    --soft:       #7A9E98;
    --bg:         #F7FAFA;
    --white:      #FFFFFF;
    --border:     #C8DEDA;
    --radius:     8px;
    --shadow:     0 2px 16px rgba(42,125,111,0.10);
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--dark);
    line-height: 1.6;
    font-size: 14px;
  }

  .app-header {
    background: linear-gradient(135deg, var(--teal) 0%, #1d6358 100%);
    color: #fff;
    padding: 36px 40px 32px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .app-header::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
  }
  .header-logo {
    font-family: 'DM Serif Display', serif;
    font-size: 28px;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    position: relative;
  }
  .header-sub {
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    opacity: 0.75;
    position: relative;
  }
  .header-title {
    font-family: 'DM Serif Display', serif;
    font-size: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255,255,255,0.25);
    position: relative;
  }
  .submitted-badge {
    display: inline-block;
    margin-top: 10px;
    padding: 4px 14px;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 20px;
    font-size: 11px;
    letter-spacing: 1px;
    text-transform: uppercase;
    position: relative;
  }

  .disclaimer {
    background: var(--teal-pale);
    border-left: 4px solid var(--teal);
    padding: 16px 20px;
    font-size: 12.5px;
    color: var(--mid);
    line-height: 1.7;
    margin: 24px 40px;
    border-radius: 0 var(--radius) var(--radius) 0;
  }

  .form-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 24px 60px;
  }

  .print-toolbar {
    text-align: right;
    margin: 16px 0;
  }
  .btn-print {
    background: var(--white);
    color: var(--teal);
    border: 2px solid var(--teal);
    padding: 8px 22px;
    border-radius: 30px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-print:hover { background: var(--teal-pale); }

  .section-title {
    font-family: 'DM Serif Display', serif;
    font-size: 15px;
    color: var(--teal);
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-bottom: 2px solid var(--teal-mid);
    padding-bottom: 6px;
    margin: 32px 0 16px;
  }

  .card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
    margin-bottom: 20px;
  }

  .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
  .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
  .col-span-2 { grid-column: span 2; }

  .field { display: flex; flex-direction: column; gap: 5px; }
  .field > label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--soft);
  }
  .value-box {
    border: 1.5px solid var(--border);
    border-radius: 6px;
    padding: 9px 12px;
    background: var(--bg);
    font-size: 14px;
    color: var(--dark);
    min-height: 38px;
    white-space: pre-wrap;
    word-break: break-word;
  }
  .value-box.empty { color: var(--soft); font-style: italic; }
  .value-box.multi { min-height: 80px; }

  .employer-block {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: var(--shadow);
  }
  .emp-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
  }
  .emp-number {
    background: var(--teal);
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
  }
  .contact-toggle {
    font-size: 12px; color: var(--mid);
    display: flex; align-items: center; gap: 8px;
  }
  .pill {
    padding: 3px 12px;
    border-radius: 20px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    font-size: 12px;
    font-weight: 600;
    color: var(--mid);
  }
  .pill.yes  { background: #D4EDDA; border-color: #27AE60; color: #1e8449; }
  .pill.no   { background: #FADBD8; border-color: #C0392B; color: #922B21; }
  .pill.muted { color: var(--soft); }

  .yn-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--teal-pale);
    gap: 16px;
  }
  .yn-row:last-child { border-bottom: none; }
  .yn-label { flex: 1; font-size: 13.5px; line-height: 1.5; }
  .yn-label small { display: block; color: var(--soft); font-size: 11.5px; margin-top: 2px; }
  .yn-explain-block {
    margin-top: 8px;
    padding: 10px 12px;
    background: var(--teal-pale);
    border-left: 3px solid var(--teal);
    border-radius: 0 6px 6px 0;
    font-size: 13px;
    white-space: pre-wrap;
    color: var(--dark);
  }
  .yn-explain-block.empty { color: var(--soft); font-style: italic; }

  .days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    margin-top: 8px;
  }
  .day-cell {
    display: flex; flex-direction: column; align-items: center; gap: 4px;
  }
  .day-cell span { font-size: 10px; font-weight: 600; color: var(--soft); text-transform: uppercase; }
  .day-cell .value-box {
    width: 100%;
    padding: 6px 4px;
    font-size: 11px;
    text-align: center;
    min-height: 32px;
  }

  .avail-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px;
  }
  .avail-chip {
    display: flex; align-items: center; gap: 8px;
    border: 1.5px solid var(--border); border-radius: 6px;
    padding: 8px 12px;
    background: var(--bg); font-size: 13px; color: var(--mid);
  }
  .avail-chip.checked { background: var(--teal-pale); border-color: var(--teal); color: var(--teal); font-weight: 500; }
  .avail-chip .check-mark { width: 14px; height: 14px; border-radius: 3px; border: 1.5px solid var(--border); display: inline-flex; align-items: center; justify-content: center; background: #fff; }
  .avail-chip.checked .check-mark { background: var(--teal); border-color: var(--teal); color: #fff; font-size: 10px; }

  .edu-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  .edu-table th {
    background: var(--teal); color: #fff;
    font-size: 11px; letter-spacing: 0.8px; text-transform: uppercase;
    padding: 10px 12px; text-align: left; font-weight: 500;
  }
  .edu-table th:first-child { border-radius: 6px 0 0 0; }
  .edu-table th:last-child  { border-radius: 0 6px 0 0; }
  .edu-table td { padding: 8px 10px; border-bottom: 1px solid var(--border); background: var(--white); vertical-align: top; font-size: 13px; color: var(--dark); }
  .edu-table td:first-child {
    font-weight: 600; color: var(--mid); text-transform: uppercase;
    letter-spacing: 0.5px; background: var(--teal-pale); white-space: nowrap;
    font-size: 12px;
  }
  .edu-table td .empty-cell { color: var(--soft); font-style: italic; }

  .ref-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  .ref-table th {
    background: var(--teal); color: #fff;
    font-size: 11px; letter-spacing: 0.8px; text-transform: uppercase;
    padding: 10px 12px; text-align: left; font-weight: 500;
  }
  .ref-table td { padding: 8px 10px; border-bottom: 1px solid var(--border); font-size: 13px; color: var(--dark); }
  .ref-table td .empty-cell { color: var(--soft); font-style: italic; }

  .agreement-para {
    display: flex; align-items: flex-start; gap: 14px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 10px;
    font-size: 13px;
    line-height: 1.65;
    color: #3a3a3a;
    box-shadow: 0 1px 4px rgba(42,125,111,0.06);
  }
  .agreement-para.checked-state {
    background: var(--teal-pale);
    border-color: var(--teal);
  }
  .agree-mark {
    flex-shrink: 0; width: 22px; height: 22px;
    border-radius: 4px; border: 1.5px solid var(--border);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 14px; font-weight: 700;
    background: #fff; color: transparent;
    margin-top: 1px;
  }
  .agreement-para.checked-state .agree-mark {
    background: var(--teal); border-color: var(--teal); color: #fff;
  }

  .sig-section {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 24px;
    box-shadow: var(--shadow);
    margin-top: 8px;
  }
  .sig-section h3 {
    font-family: 'DM Serif Display', serif;
    font-size: 14px;
    color: var(--dark);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--teal-mid);
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .sig-canvas-wrap {
    border: 2px solid var(--teal);
    border-radius: 8px;
    background: #FAFFFE;
    height: 130px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    padding: 6px;
  }
  .sig-canvas-wrap img { max-width: 100%; max-height: 100%; object-fit: contain; }
  .sig-typed {
    border: 2px solid var(--teal);
    border-radius: 8px;
    padding: 16px 20px;
    font-family: 'DM Serif Display', serif;
    font-size: 28px;
    color: var(--dark);
    background: #FAFFFE;
    font-style: italic;
  }
  .sig-empty {
    border: 2px dashed var(--border);
    border-radius: 8px;
    padding: 30px 20px;
    text-align: center;
    color: var(--soft);
    font-style: italic;
    background: var(--bg);
  }
  .sig-bottom { display: flex; gap: 20px; margin-top: 20px; }
  .sig-bottom .field { flex: 2; }
  .sig-bottom .field-date { flex: 1; }

  .footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    font-size: 11px;
    color: var(--soft);
    text-align: center;
  }

  @media (max-width: 680px) {
    .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
    .col-span-2 { grid-column: span 1; }
    .avail-grid { grid-template-columns: 1fr 1fr; }
    .days-grid  { grid-template-columns: repeat(4, 1fr); }
    .form-wrap  { padding: 0 14px 40px; }
    .disclaimer { margin: 16px 14px; }
    .sig-bottom { flex-direction: column; }
    .edu-table  { font-size: 11px; }
  }

  @media print {
    .print-toolbar, .btn-print { display: none !important; }
    body { background: white; }
    .card, .employer-block, .sig-section { box-shadow: none; border: 1px solid #ccc; page-break-inside: avoid; }
    .agreement-para { page-break-inside: avoid; }
    .section-title { page-break-after: avoid; }
    .app-header { padding: 24px; }
  }
</style>
</head>
<body>

@php
  $company = \App\Models\Setting::get('company_name', 'McCrory Center');
  $ps      = $candidate->preScreening;
  $app     = $ps?->employment_application_data ?? [];

  $employmentHistory = data_get($app, 'employment_history', []);
  $educationRows     = data_get($app, 'education_rows', []);
  $references        = data_get($app, 'references', []);
  $general           = data_get($app, 'general', []);
  $agreements        = data_get($app, 'agreements', []);
  $signature         = data_get($app, 'signature', []);
  $submittedAt       = $ps?->employment_application_submitted_at;

  $valOrDash = fn ($v) => (is_string($v) && trim($v) !== '') || (is_numeric($v)) ? $v : null;

  $pill = function ($val) {
      if ($val === 'yes') return '<span class="pill yes">Yes</span>';
      if ($val === 'no')  return '<span class="pill no">No</span>';
      return '<span class="pill muted">— Not answered —</span>';
  };
@endphp

<div class="app-header">
  <div class="header-logo">{{ $company }}</div>
  <div class="header-sub">Behavioral Health</div>
  <div class="header-title">Application for Employment</div>
  @if ($submittedAt)
    <div class="submitted-badge">Submitted {{ $submittedAt->format('M j, Y · g:i A') }}</div>
  @endif
</div>

<div class="disclaimer">
  All applicants are considered for all positions without regard to race, religion, color, sex, gender, sexual orientation, pregnancy, age, national origin, ancestry, physical/mental disability, medical condition, military/veteran status, genetic information, marital status, ethnicity, citizenship or immigration status or any other protected classification, in accordance with applicable federal, state, and local laws.
</div>

<div class="form-wrap">

  <div class="print-toolbar">
    <button class="btn-print" onclick="window.print()">🖨 Print / Save as PDF</button>
  </div>

  {{-- PERSONAL INFO --}}
  <div class="section-title">Personal Information</div>
  <div class="card">
    <div class="grid-2" style="margin-bottom:16px">
      <div class="field">
        <label>Position(s) Applied For</label>
        @php $v = $valOrDash(data_get($app, 'position_applied_for')) ?? $candidate->category?->name; @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="field">
        <label>Date of Application</label>
        @php $v = data_get($app, 'application_date'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ? \Carbon\Carbon::parse($v)->format('M j, Y') : '—' }}</div>
      </div>
    </div>
    <div class="field" style="margin-bottom:16px">
      <label>Full Name (Last, First, Middle)</label>
      @php $v = $valOrDash(data_get($app, 'applicant_full_name')) ?? $candidate->full_name; @endphp
      <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
    </div>
    <div class="grid-4" style="margin-bottom:16px">
      <div class="field col-span-2">
        <label>Street Address</label>
        @php $v = data_get($app, 'address_street'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="field">
        <label>City</label>
        @php $v = data_get($app, 'address_city'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="field">
        <label>State</label>
        @php $v = data_get($app, 'address_state'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
    </div>
    <div class="grid-3">
      <div class="field">
        <label>Zip Code</label>
        @php $v = data_get($app, 'address_zip'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="field">
        <label>Main Phone</label>
        @php $v = $valOrDash(data_get($app, 'phone_main')) ?? $candidate->phone; @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="field">
        <label>Alternate Phone</label>
        @php $v = data_get($app, 'phone_alternate'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
    </div>
    <div class="field" style="margin-top:16px">
      <label>Email Address</label>
      @php $v = $valOrDash(data_get($app, 'email')) ?? $candidate->email; @endphp
      <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
    </div>
  </div>

  {{-- EMPLOYMENT EXPERIENCE --}}
  <div class="section-title">Employment Experience</div>
  @for ($i = 0; $i < 3; $i++)
    @php $employer = $employmentHistory[$i] ?? []; @endphp
    <div class="employer-block">
      <div class="emp-header">
        <span class="emp-number">Employer {{ $i + 1 }}{{ $i === 0 ? ' — Most Recent' : '' }}</span>
        <div class="contact-toggle">
          May we contact? {!! $pill(data_get($employer, 'may_contact')) !!}
        </div>
      </div>
      <div class="grid-2" style="margin-bottom:12px">
        <div class="field">
          <label>Employer Name</label>
          @php $v = data_get($employer, 'employer_name'); @endphp
          <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
        <div class="field">
          <label>Supervisor</label>
          @php $v = data_get($employer, 'supervisor'); @endphp
          <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
      </div>
      <div class="field" style="margin-bottom:12px">
        <label>Street Address</label>
        @php $v = data_get($employer, 'street_address'); @endphp
        <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
      </div>
      <div class="grid-3" style="margin-bottom:12px">
        <div class="field">
          <label>Phone Number</label>
          @php $v = data_get($employer, 'phone'); @endphp
          <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
        <div class="field">
          <label>From (Month/Year)</label>
          @php $v = data_get($employer, 'from'); @endphp
          <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
        <div class="field">
          <label>To (Month/Year)</label>
          @php $v = data_get($employer, 'to'); @endphp
          <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
      </div>
      <div class="grid-2">
        <div class="field">
          <label>Job Title &amp; Duties</label>
          @php $v = data_get($employer, 'job_title_duties'); @endphp
          <div class="value-box multi {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
        <div class="field">
          <label>Reason for Leaving</label>
          @php $v = data_get($employer, 'reason_for_leaving'); @endphp
          <div class="value-box multi {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
        </div>
      </div>
    </div>
  @endfor

  <div class="card">
    <div class="field">
      <label>Have you ever been involuntarily terminated or asked to resign from any job?</label>
      @php $v = data_get($app, 'termination_explanation'); @endphp
      <div class="value-box multi {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
    </div>
    <div class="field" style="margin-top:14px">
      <label>Please explain any gaps in your employment history</label>
      @php $v = data_get($app, 'employment_gaps_explanation'); @endphp
      <div class="value-box multi {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
    </div>
    <div class="field" style="margin-top:14px">
      <label>Additional experience, job-related skills, languages, or qualifications</label>
      @php $v = data_get($app, 'additional_experience'); @endphp
      <div class="value-box multi {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
    </div>
  </div>

  {{-- EDUCATION --}}
  <div class="section-title">Education</div>
  <div class="card" style="overflow-x:auto">
    <table class="edu-table">
      <thead>
        <tr>
          <th></th>
          <th>School Name</th>
          <th>Years Completed</th>
          <th>Diploma/Degree?</th>
          <th>Area of Study / Major</th>
          <th>Specialized Training / Activities</th>
        </tr>
      </thead>
      <tbody>
        @php
          $defaultEdu = [
            ['level' => 'High School'],
            ['level' => 'College / University'],
            ['level' => 'Graduate / Professional'],
            ['level' => 'Trade School'],
            ['level' => 'Other'],
          ];
          $rows = !empty($educationRows) ? $educationRows : $defaultEdu;
        @endphp
        @foreach ($rows as $i => $row)
          <tr>
            <td>{{ data_get($row, 'level', 'Level '.($i + 1)) }}</td>
            @foreach (['school_name','years_completed','degree','major','training'] as $col)
              @php $v = data_get($row, $col); @endphp
              <td>{{ $v ?: ''}}@if(!$v)<span class="empty-cell">—</span>@endif</td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- REFERENCES --}}
  <div class="section-title">Business &amp; Professional References</div>
  <div class="card" style="overflow-x:auto">
    <table class="ref-table">
      <thead>
        <tr>
          <th>Name and Title</th>
          <th>Relationship</th>
          <th>Phone Number or Email</th>
        </tr>
      </thead>
      <tbody>
        @for ($i = 0; $i < 3; $i++)
          @php $ref = $references[$i] ?? []; @endphp
          <tr>
            @foreach (['name_title','relationship','contact'] as $col)
              @php $v = data_get($ref, $col); @endphp
              <td>{{ $v ?: '' }}@if(!$v)<span class="empty-cell">—</span>@endif</td>
            @endforeach
          </tr>
        @endfor
      </tbody>
    </table>
  </div>

  {{-- GENERAL INFORMATION --}}
  <div class="section-title">General Information</div>
  <div class="card">

    <div class="yn-row">
      <div class="yn-label">1. Have you ever used another name?</div>
      <div>{!! $pill(data_get($general, 'q1_other_name')) !!}</div>
    </div>

    <div class="yn-row">
      <div class="yn-label">2. Is additional info about name changes, assumed names, or nicknames necessary to check your work/educational record?</div>
      <div>{!! $pill(data_get($general, 'q2_name_change_info')) !!}</div>
    </div>
    @php $q2Exp = data_get($general, 'q2_explanation'); @endphp
    @if ($q2Exp)
      <div class="yn-explain-block">{{ $q2Exp }}</div>
    @endif

    <div class="yn-row">
      <div class="yn-label">3. Have you ever worked for this company before?</div>
      <div>{!! $pill(data_get($general, 'q3_worked_here')) !!}</div>
    </div>
    @php $q3Exp = data_get($general, 'q3_explanation'); @endphp
    @if ($q3Exp)
      <div class="yn-explain-block"><strong>Dates and Position:</strong> {{ $q3Exp }}</div>
    @endif

    <div class="yn-row">
      <div class="yn-label">4. Do you have friends and/or relatives working for this company?</div>
      <div>{!! $pill(data_get($general, 'q4_relatives_here')) !!}</div>
    </div>
    @php $q4Exp = data_get($general, 'q4_explanation'); @endphp
    @if ($q4Exp)
      <div class="yn-explain-block"><strong>Names and Relationships:</strong> {{ $q4Exp }}</div>
    @endif

    <div class="yn-row" style="align-items:center">
      <div class="yn-label">5. On what date are you available to begin work?</div>
      @php $v = data_get($general, 'available_begin_date'); @endphp
      <div class="value-box {{ $v ? '' : 'empty' }}" style="max-width:220px">
        {{ $v ? \Carbon\Carbon::parse($v)->format('M j, Y') : '—' }}
      </div>
    </div>

    <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
      <div style="font-size:13.5px;margin-bottom:8px;">6. Days/Hours available to work:</div>
      <div class="days-grid">
        @foreach (['mon' => 'Mon','tue' => 'Tue','wed' => 'Wed','thu' => 'Thu','fri' => 'Fri','sat' => 'Sat','sun' => 'Sun'] as $key => $label)
          @php $v = data_get($general, "work_hours_by_day.$key"); @endphp
          <div class="day-cell">
            <span>{{ $label }}</span>
            <div class="value-box {{ $v ? '' : 'empty' }}">{{ $v ?: '—' }}</div>
          </div>
        @endforeach
      </div>
    </div>

    <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
      <div style="font-size:13.5px;margin-bottom:8px;">7. Are you available to work?</div>
      <div class="avail-grid">
        @php $availableTypes = data_get($general, 'available_types', []) ?: []; @endphp
        @foreach (['full' => 'Full-time','part' => 'Part-time','shift' => 'Shift Work','temp' => 'Temporary'] as $val => $label)
          @php $checked = in_array($val, $availableTypes, true); @endphp
          <div class="avail-chip {{ $checked ? 'checked' : '' }}">
            <span class="check-mark">@if($checked)✓@endif</span> {{ $label }}
          </div>
        @endforeach
      </div>
    </div>

    @php
      $simpleYnQs = [
        ['key' => 'q8_transportation',         'label' => '8. If hired, would you have reliable transportation to and from work?'],
        ['key' => 'q9_can_travel',             'label' => '9. Can you travel if the position requires it?'],
        ['key' => 'q10_can_relocate',          'label' => '10. Can you relocate if the position requires it?'],
        ['key' => 'q11_over_18',               'label' => '11. Are you at least 18 years old?', 'small' => 'If under 18, hire is subject to verification of minimum legal age.'],
        ['key' => 'q12_work_auth',             'label' => '12. If hired, can you present evidence of identity and legal right to work in this country?'],
        ['key' => 'q13_essential_functions',   'label' => '13. Are you able to perform the essential job functions with or without reasonable accommodation?', 'small' => 'We comply with the ADA and consider reasonable accommodations for qualified applicants.'],
        ['key' => 'q14_illegal_drug_use',      'label' => '14. Are you currently engaging in the use of illegal drugs?'],
      ];
    @endphp
    @foreach ($simpleYnQs as $q)
      <div class="yn-row">
        <div class="yn-label">
          {{ $q['label'] }}
          @if (!empty($q['small']))<small>{{ $q['small'] }}</small>@endif
        </div>
        <div>{!! $pill(data_get($general, $q['key'])) !!}</div>
      </div>
    @endforeach

    <div class="yn-row">
      <div class="yn-label">15. Any history of loss of license and/or felony convictions?</div>
      <div>{!! $pill(data_get($general, 'q15_felony_or_license_loss')) !!}</div>
    </div>
    @php $q15Exp = data_get($general, 'q15_explanation'); @endphp
    @if ($q15Exp)
      <div class="yn-explain-block">{{ $q15Exp }}</div>
    @endif

    <div class="yn-row">
      <div class="yn-label">16. Any history of loss or limitation of privileges or disciplinary action?</div>
      <div>{!! $pill(data_get($general, 'q16_disciplinary_history')) !!}</div>
    </div>
    @php $q16Exp = data_get($general, 'q16_explanation'); @endphp
    @if ($q16Exp)
      <div class="yn-explain-block">{{ $q16Exp }}</div>
    @endif
  </div>

  {{-- APPLICANT STATEMENT --}}
  <div class="section-title">Applicant Statement &amp; Agreement</div>

  @php
    $agreementParagraphs = [
      1 => 'I hereby authorize the Company to thoroughly investigate my references, work record, education and other matters related to my suitability for employment and further authorize the prior employers and references I have listed to disclose to the Company any and all letters, reports and other information related to my work records, without giving me prior notice of such disclosure. In addition, I hereby release the Company, my former employers and all other persons, corporations, partnerships and associations from any and all claims, demands or liabilities arising out of or in any way related to such investigation or disclosure.',
      2 => 'In the event of my employment with the Company, I understand that I am required to comply with all rules and regulations of the Company.',
      3 => 'If hired, I understand and agree that my employment with the Company is at-will, and that neither I, nor the Company is required to continue the employment relationship for any specific term. I further understand that the Company or I may terminate the employment relationship at any time, with or without cause, and with or without notice. I understand that the at-will status of my employment cannot be amended, modified, or altered in any way by any oral modifications.',
      4 => 'I understand that safety of employees is extremely important to the Company and that the Company is committed to ensuring a safe working environment. I understand that I, and every employee, have a responsibility to prevent accidents and injuries by observing all safety procedures and guidelines and following the directions of my site supervisor. I understand and agree to comply with federal, state, and local regulations related to on-the-job safety and health.',
      5 => 'I hereby certify that the answers given by me are true and correct to the best of my knowledge. I further certify that I, the undersigned applicant, have personally completed this application. I understand that any omission or misstatement of material fact on this application or on any document used to secure employment shall be grounds for rejection of this application or for immediate discharge if I am employed, regardless of the time elapsed before discovery.',
      6 => 'I understand that if I am selected for hire, it will be necessary for me to provide satisfactory evidence of my identity and legal authority to work in the United States, and that federal immigration laws require me to complete an I-9 Form in this regard.',
      7 => 'I understand that if any term, provision, or portion of this Agreement is declared void or unenforceable, it shall be severed and the remainder of this Agreement shall be enforceable.',
    ];
  @endphp

  @foreach ($agreementParagraphs as $i => $text)
    @php $isChecked = (bool) data_get($agreements, 'agreement_'.$i, false); @endphp
    <div class="agreement-para {{ $isChecked ? 'checked-state' : '' }}">
      <span class="agree-mark">@if($isChecked)✓@endif</span>
      <span class="agree-text">{{ $text }}</span>
    </div>
  @endforeach

  {{-- SIGNATURE --}}
  <div class="section-title">Electronic Signature</div>

  <div class="sig-section">
    <h3>Signature</h3>
    @php
      $sigMode    = data_get($signature, 'mode');
      $sigDrawn   = data_get($signature, 'drawn_data');
      $sigTyped   = data_get($signature, 'typed');
      $sigPrinted = data_get($signature, 'printed_name');
      $sigDate    = data_get($signature, 'signed_on');
    @endphp

    @if ($sigMode === 'draw' && $sigDrawn)
      <div class="sig-canvas-wrap">
        <img src="{{ $sigDrawn }}" alt="Candidate signature">
      </div>
    @elseif ($sigMode === 'type' && $sigTyped)
      <div class="sig-typed">{{ $sigTyped }}</div>
    @elseif ($sigDrawn)
      <div class="sig-canvas-wrap">
        <img src="{{ $sigDrawn }}" alt="Candidate signature">
      </div>
    @elseif ($sigTyped)
      <div class="sig-typed">{{ $sigTyped }}</div>
    @else
      <div class="sig-empty">No signature on file</div>
    @endif

    <div class="sig-bottom">
      <div class="field">
        <label>Printed Name</label>
        <div class="value-box {{ $sigPrinted ? '' : 'empty' }}">{{ $sigPrinted ?: '—' }}</div>
      </div>
      <div class="field field-date">
        <label>Date</label>
        <div class="value-box {{ $sigDate ? '' : 'empty' }}">{{ $sigDate ? \Carbon\Carbon::parse($sigDate)->format('M j, Y') : '—' }}</div>
      </div>
    </div>
  </div>

  <div class="footer">
    {{ $company }} · Confidential · Generated {{ now()->format('M j, Y g:i A') }}
  </div>

</div>
</body>
</html>
