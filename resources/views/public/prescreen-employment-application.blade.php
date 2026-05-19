<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $company }} — Application for Employment</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --teal:       #2A7D6F;
    --teal-light: #3FA08E;
    --teal-pale:  #E8F5F3;
    --teal-mid:   #C2E8E2;
    --gold:       #D4A843;
    --dark:       #1A2E2A;
    --mid:        #4A6660;
    --soft:       #7A9E98;
    --bg:         #F7FAFA;
    --white:      #FFFFFF;
    --border:     #C8DEDA;
    --error:      #C0392B;
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

  .status {
    padding: 12px 14px;
    border-radius: 8px;
    margin: 16px 0;
    font-size: 13px;
  }
  .status.success { background: #d4edda; color: #1e6f38; border: 1px solid #98d4a7; }
  .status.error   { background: #fadbd8; color: #922b21; border: 1px solid #e8a7a0; }

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
  .col-span-3 { grid-column: span 3; }

  .field { display: flex; flex-direction: column; gap: 5px; }
  .field > label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--soft);
  }
  .field input[type="text"],
  .field input[type="email"],
  .field input[type="tel"],
  .field input[type="date"],
  .field textarea,
  .field select {
    border: 1.5px solid var(--border);
    border-radius: 6px;
    padding: 9px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    color: var(--dark);
    background: var(--bg);
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    width: 100%;
  }
  .field input:focus,
  .field textarea:focus,
  .field select:focus {
    border-color: var(--teal);
    box-shadow: 0 0 0 3px rgba(42,125,111,0.12);
    background: var(--white);
  }
  .field textarea { resize: vertical; min-height: 80px; }

  .employer-block {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: var(--shadow);
  }
  .employer-block .emp-header {
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
    display: flex; align-items: center; gap: 12px;
    font-size: 12px; color: var(--mid);
  }
  .contact-toggle label { display: flex; align-items: center; gap: 5px; cursor: pointer; }
  .contact-toggle input[type="radio"] { accent-color: var(--teal); }

  .yn-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--teal-pale);
    gap: 16px;
  }
  .yn-row:last-child { border-bottom: none; }
  .yn-label { flex: 1; font-size: 13.5px; line-height: 1.5; }
  .yn-label small { display: block; color: var(--soft); font-size: 11.5px; margin-top: 2px; }
  .yn-btns { display: flex; gap: 8px; flex-shrink: 0; }
  .yn-btn {
    padding: 5px 14px;
    border-radius: 20px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
    color: var(--mid);
  }
  .yn-btn.yes-btn:hover, .yn-btn.yes-btn.active { background: #D4EDDA; border-color: #27AE60; color: #1e8449; }
  .yn-btn.no-btn:hover, .yn-btn.no-btn.active  { background: #FADBD8; border-color: #C0392B; color: #922B21; }
  .yn-explain { margin-top: 8px; display: none; }
  .yn-explain.show { display: block; }
  .yn-explain textarea, .yn-explain input[type="text"] {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    padding: 8px 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--dark);
    background: var(--bg);
    resize: vertical;
    min-height: 60px;
    outline: none;
  }
  .yn-explain input[type="text"] { min-height: 0; }
  .yn-explain textarea:focus, .yn-explain input[type="text"]:focus { border-color: var(--teal); }

  .days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    margin-top: 8px;
  }
  .day-cell { display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .day-cell span { font-size: 10px; font-weight: 600; color: var(--soft); text-transform: uppercase; }
  .day-cell input[type="text"] {
    width: 100%; border: 1.5px solid var(--border); border-radius: 6px;
    padding: 6px 4px; font-size: 11px; text-align: center;
    background: var(--bg); font-family: 'DM Sans', sans-serif; outline: none;
    transition: border-color 0.2s;
  }
  .day-cell input[type="text"]:focus { border-color: var(--teal); }

  .avail-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px;
  }
  .avail-chip {
    display: flex; align-items: center; gap: 8px;
    border: 1.5px solid var(--border); border-radius: 6px;
    padding: 8px 12px; cursor: pointer; transition: all 0.15s;
    background: var(--bg); font-size: 13px; color: var(--mid);
  }
  .avail-chip input { display: none; }
  .avail-chip.checked { background: var(--teal-pale); border-color: var(--teal); color: var(--teal); font-weight: 500; }

  .edu-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  .edu-table th {
    background: var(--teal); color: #fff;
    font-size: 11px; letter-spacing: 0.8px; text-transform: uppercase;
    padding: 10px 12px; text-align: left; font-weight: 500;
  }
  .edu-table th:first-child { border-radius: 6px 0 0 0; }
  .edu-table th:last-child  { border-radius: 0 6px 0 0; }
  .edu-table td { padding: 4px; border-bottom: 1px solid var(--border); background: var(--white); }
  .edu-table td:first-child {
    padding: 8px 12px; font-size: 12px; font-weight: 600; color: var(--mid);
    text-transform: uppercase; letter-spacing: 0.5px; background: var(--teal-pale); white-space: nowrap;
  }
  .edu-table td input[type="text"] {
    width: 100%; border: none; border-radius: 4px;
    padding: 6px 8px; font-family: 'DM Sans', sans-serif;
    font-size: 13px; color: var(--dark); background: transparent; outline: none;
    transition: background 0.15s;
  }
  .edu-table td input[type="text"]:focus { background: var(--teal-pale); }

  .ref-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  .ref-table th {
    background: var(--teal); color: #fff;
    font-size: 11px; letter-spacing: 0.8px; text-transform: uppercase;
    padding: 10px 12px; text-align: left; font-weight: 500;
  }
  .ref-table td { padding: 4px; border-bottom: 1px solid var(--border); }
  .ref-table td input[type="text"] {
    width: 100%; border: 1.5px solid transparent; border-radius: 4px;
    padding: 7px 10px; font-family: 'DM Sans', sans-serif;
    font-size: 13px; color: var(--dark); background: var(--bg); outline: none;
    transition: border-color 0.2s;
  }
  .ref-table td input:focus { border-color: var(--teal); background: #fff; }

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
    transition: background 0.2s, border-color 0.2s;
    cursor: pointer;
    user-select: none;
  }
  .agreement-para:hover { background: #f5fdfc; }
  .agreement-para.checked-state {
    background: var(--teal-pale);
    border-color: var(--teal);
  }
  .agree-cb {
    flex-shrink: 0; width: 18px; height: 18px;
    margin-top: 3px; accent-color: var(--teal); cursor: pointer;
  }
  .agree-text { flex: 1; font-size: 13px; line-height: 1.65; color: #3a3a3a; }

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
  .sig-tabs { display: flex; gap: 8px; margin-bottom: 14px; }
  .sig-tab {
    padding: 7px 18px;
    border-radius: 20px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    color: var(--mid);
    transition: all 0.15s;
  }
  .sig-tab.active { background: var(--teal); border-color: var(--teal); color: #fff; }

  #sig-canvas-wrap { display: block; }
  #sig-type-wrap   { display: none; }

  #sig-canvas {
    display: block;
    width: 100%;
    height: 130px;
    border: 2px dashed var(--border);
    border-radius: 8px;
    cursor: crosshair;
    background: #FAFFFE;
    touch-action: none;
  }
  #sig-canvas.has-sig { border-color: var(--teal); border-style: solid; }

  #sig-type-input {
    width: 100%;
    border: 2px solid var(--border);
    border-radius: 8px;
    padding: 16px 20px;
    font-family: 'DM Serif Display', serif;
    font-size: 28px;
    color: var(--dark);
    background: #FAFFFE;
    outline: none;
    font-style: italic;
  }
  #sig-type-input:focus { border-color: var(--teal); }

  .sig-hint { font-size: 11px; color: var(--soft); margin-top: 8px; text-align: center; }
  .sig-controls { display: flex; gap: 10px; margin-top: 10px; }
  .btn-clear {
    padding: 7px 16px;
    border-radius: 6px;
    border: 1.5px solid var(--border);
    background: var(--bg);
    color: var(--mid);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s;
  }
  .btn-clear:hover { border-color: var(--error); color: var(--error); }

  .sig-bottom { display: flex; gap: 20px; margin-top: 20px; }
  .sig-bottom .field { flex: 2; }
  .sig-bottom .field-date { flex: 1; }

  .submit-wrap { text-align: center; margin-top: 32px; }
  .btn-submit {
    background: linear-gradient(135deg, var(--teal) 0%, #1d6358 100%);
    color: #fff;
    border: none;
    padding: 14px 48px;
    border-radius: 40px;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.5px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(42,125,111,0.3);
    transition: all 0.2s;
  }
  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(42,125,111,0.38);
  }
  .btn-print {
    margin-left: 12px;
    background: var(--white);
    color: var(--teal);
    border: 2px solid var(--teal);
    padding: 12px 32px;
    border-radius: 40px;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-print:hover { background: var(--teal-pale); }

  @media (max-width: 680px) {
    .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
    .col-span-2, .col-span-3 { grid-column: span 1; }
    .avail-grid { grid-template-columns: 1fr 1fr; }
    .days-grid  { grid-template-columns: repeat(4, 1fr); }
    .form-wrap  { padding: 0 14px 40px; }
    .disclaimer { margin: 16px 14px; }
    .sig-bottom { flex-direction: column; }
    .edu-table  { font-size: 11px; }
  }

  @media print {
    .btn-submit, .btn-print, .sig-controls, .sig-tabs { display: none !important; }
    body { background: white; }
    .card, .employer-block, .sig-section { box-shadow: none; border: 1px solid #ccc; }
  }
</style>
</head>
<body>
@php
  $formAction = $formAction ?? route('public.prescreen.application.submit', ['token' => $token]);
  $app        = $applicationData ?? [];
  $employmentHistory = old('employment_history', data_get($app, 'employment_history', [[], [], []]));
  $educationRows     = old('education_rows', data_get($app, 'education_rows', [
      ['level' => 'High School'],
      ['level' => 'College / University'],
      ['level' => 'Graduate / Professional'],
      ['level' => 'Trade School'],
      ['level' => 'Other'],
  ]));
  $references = old('references', data_get($app, 'references', [[], [], []]));
  $general    = old('general',    data_get($app, 'general', []));
  $agreements = data_get($app, 'agreements', []);
  $signature  = data_get($app, 'signature', []);
@endphp

<div class="app-header">
  <div class="header-logo">{{ $company }}</div>
  <div class="header-sub">Behavioral Health</div>
  <div class="header-title">Application for Employment</div>
</div>

<div class="disclaimer">
  All applicants are considered for all positions without regard to race, religion, color, sex, gender, sexual orientation, pregnancy, age, national origin, ancestry, physical/mental disability, medical condition, military/veteran status, genetic information, marital status, ethnicity, citizenship or immigration status or any other protected classification, in accordance with applicable federal, state, and local laws. Equal access to programs, services, and employment is available to all qualified persons. Those applicants requiring accommodation to complete the application and/or interview process should contact a management representative.
</div>

<div class="form-wrap">
  @if (session('submittedEmployment'))
    <div class="status success">Your employment application has been submitted successfully.</div>
  @endif
  @if ($errors->any())
    <div class="status error">{{ $errors->first() }}</div>
  @endif

  <form id="employment-form" method="POST" action="{{ $formAction }}">
    @csrf

    {{-- PERSONAL INFO --}}
    <div class="section-title">Personal Information</div>
    <div class="card">
      <div class="grid-2" style="margin-bottom:16px">
        <div class="field">
          <label>Position(s) Applied For</label>
          <input type="text" name="position_applied_for" placeholder="Enter position title" value="{{ old('position_applied_for', data_get($app, 'position_applied_for', $candidate->category?->name)) }}" required>
        </div>
        <div class="field">
          <label>Date of Application</label>
          <input type="date" id="application_date" name="application_date" value="{{ old('application_date', data_get($app, 'application_date', now()->format('Y-m-d'))) }}">
        </div>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label>Full Name (Last, First, Middle)</label>
        <input type="text" name="applicant_full_name" placeholder="Last name, First name, Middle name" value="{{ old('applicant_full_name', data_get($app, 'applicant_full_name', $candidate->full_name)) }}" required>
      </div>
      <div class="grid-4" style="margin-bottom:16px">
        <div class="field col-span-2">
          <label>Street Address</label>
          <input type="text" name="address_street" placeholder="Street address" value="{{ old('address_street', data_get($app, 'address_street')) }}">
        </div>
        <div class="field">
          <label>City</label>
          <input type="text" name="address_city" placeholder="City" value="{{ old('address_city', data_get($app, 'address_city')) }}">
        </div>
        <div class="field">
          <label>State</label>
          <input type="text" name="address_state" placeholder="State" value="{{ old('address_state', data_get($app, 'address_state')) }}">
        </div>
      </div>
      <div class="grid-3">
        <div class="field">
          <label>Zip Code</label>
          <input type="text" name="address_zip" placeholder="Zip" value="{{ old('address_zip', data_get($app, 'address_zip')) }}">
        </div>
        <div class="field">
          <label>Main Phone</label>
          <input type="tel" name="phone_main" placeholder="(000) 000-0000" value="{{ old('phone_main', data_get($app, 'phone_main', $candidate->phone)) }}">
        </div>
        <div class="field">
          <label>Alternate Phone</label>
          <input type="tel" name="phone_alternate" placeholder="(000) 000-0000" value="{{ old('phone_alternate', data_get($app, 'phone_alternate')) }}">
        </div>
      </div>
      <div class="field" style="margin-top:16px">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@example.com" value="{{ old('email', data_get($app, 'email', $candidate->email)) }}">
      </div>
    </div>

    {{-- EMPLOYMENT EXPERIENCE --}}
    <div class="section-title">Employment Experience</div>
    <p style="font-size:12.5px;color:var(--soft);margin-bottom:12px;">List employers in reverse chronological order. Account for all periods of time. Add additional page if necessary.</p>

    @for ($i = 0; $i < 3; $i++)
      @php
        $employer = $employmentHistory[$i] ?? [];
        $mayContact = old("employment_history.$i.may_contact", data_get($employer, 'may_contact'));
      @endphp
      <div class="employer-block">
        <div class="emp-header">
          <span class="emp-number">Employer {{ $i + 1 }}{{ $i === 0 ? ' — Most Recent' : '' }}</span>
          <div class="contact-toggle">
            May we contact?
            <label><input type="radio" name="employment_history[{{ $i }}][may_contact]" value="yes" @checked($mayContact === 'yes')> Yes</label>
            <label><input type="radio" name="employment_history[{{ $i }}][may_contact]" value="no"  @checked($mayContact === 'no')> No</label>
          </div>
        </div>
        <div class="grid-2" style="margin-bottom:12px">
          <div class="field">
            <label>Employer Name</label>
            <input type="text" name="employment_history[{{ $i }}][employer_name]" placeholder="Company name" value="{{ old("employment_history.$i.employer_name", data_get($employer, 'employer_name')) }}">
          </div>
          <div class="field">
            <label>Supervisor</label>
            <input type="text" name="employment_history[{{ $i }}][supervisor]" placeholder="Supervisor name" value="{{ old("employment_history.$i.supervisor", data_get($employer, 'supervisor')) }}">
          </div>
        </div>
        <div class="field" style="margin-bottom:12px">
          <label>Street Address</label>
          <input type="text" name="employment_history[{{ $i }}][street_address]" placeholder="Employer address" value="{{ old("employment_history.$i.street_address", data_get($employer, 'street_address')) }}">
        </div>
        <div class="grid-3" style="margin-bottom:12px">
          <div class="field">
            <label>Phone Number</label>
            <input type="tel" name="employment_history[{{ $i }}][phone]" placeholder="(000) 000-0000" value="{{ old("employment_history.$i.phone", data_get($employer, 'phone')) }}">
          </div>
          <div class="field">
            <label>From (Month/Year)</label>
            <input type="text" name="employment_history[{{ $i }}][from]" placeholder="MM/YYYY" value="{{ old("employment_history.$i.from", data_get($employer, 'from')) }}">
          </div>
          <div class="field">
            <label>To (Month/Year)</label>
            <input type="text" name="employment_history[{{ $i }}][to]" placeholder="MM/YYYY or Present" value="{{ old("employment_history.$i.to", data_get($employer, 'to')) }}">
          </div>
        </div>
        <div class="grid-2">
          <div class="field">
            <label>Job Title &amp; Duties</label>
            <textarea name="employment_history[{{ $i }}][job_title_duties]" placeholder="Describe your job title and primary duties...">{{ old("employment_history.$i.job_title_duties", data_get($employer, 'job_title_duties')) }}</textarea>
          </div>
          <div class="field">
            <label>Reason for Leaving</label>
            <textarea name="employment_history[{{ $i }}][reason_for_leaving]" placeholder="Reason for leaving this position...">{{ old("employment_history.$i.reason_for_leaving", data_get($employer, 'reason_for_leaving')) }}</textarea>
          </div>
        </div>
      </div>
    @endfor

    {{-- Additional questions --}}
    <div class="card">
      @php
        $terminationVal = old('termination_yn', data_get($app, 'termination_yn', data_get($app, 'termination_explanation') ? 'yes' : null));
        $terminationExplain = old('termination_explanation', data_get($app, 'termination_explanation'));
      @endphp
      <div class="yn-row" data-yn data-explain-target="term-explain">
        <div class="yn-label">Have you ever been involuntarily terminated or asked to resign from any job?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $terminationVal === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn {{ $terminationVal === 'no' ? 'active' : '' }}"  data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="termination_yn" value="{{ $terminationVal }}">
      </div>
      <div class="yn-explain {{ $terminationVal === 'yes' ? 'show' : '' }}" id="term-explain">
        <textarea name="termination_explanation" placeholder="Please explain...">{{ $terminationExplain }}</textarea>
      </div>
      <div class="field" style="margin-top:14px">
        <label>Please explain any gaps in your employment history</label>
        <textarea name="employment_gaps_explanation" placeholder="Describe any gaps and reasons...">{{ old('employment_gaps_explanation', data_get($app, 'employment_gaps_explanation')) }}</textarea>
      </div>
      <div class="field" style="margin-top:14px">
        <label>Additional experience, job-related skills, languages, or qualifications</label>
        <textarea name="additional_experience" placeholder="Relevant skills, certifications, languages, or other qualifications...">{{ old('additional_experience', data_get($app, 'additional_experience')) }}</textarea>
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
          @foreach ($educationRows as $i => $row)
            @php $levelLabel = old("education_rows.$i.level", data_get($row, 'level', 'Level '.($i + 1))); @endphp
            <tr>
              <td>
                {{ $levelLabel }}
                <input type="hidden" name="education_rows[{{ $i }}][level]" value="{{ $levelLabel }}">
              </td>
              <td><input type="text" name="education_rows[{{ $i }}][school_name]"     placeholder="School name"        value="{{ old("education_rows.$i.school_name", data_get($row, 'school_name')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][years_completed]" placeholder="e.g. 4"             value="{{ old("education_rows.$i.years_completed", data_get($row, 'years_completed')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][degree]"          placeholder="Yes / No"           value="{{ old("education_rows.$i.degree", data_get($row, 'degree')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][major]"           placeholder="Major or focus"     value="{{ old("education_rows.$i.major", data_get($row, 'major')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][training]"        placeholder="Activities or skills" value="{{ old("education_rows.$i.training", data_get($row, 'training')) }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- REFERENCES --}}
    <div class="section-title">Business &amp; Professional References</div>
    <div class="card" style="overflow-x:auto">
      <p style="font-size:12.5px;color:var(--soft);margin-bottom:12px;">List three professional references not related to you.</p>
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
              <td><input type="text" name="references[{{ $i }}][name_title]"   placeholder="Full name and title" value="{{ old("references.$i.name_title", data_get($ref, 'name_title')) }}"></td>
              <td><input type="text" name="references[{{ $i }}][relationship]" placeholder="e.g. Former Manager" value="{{ old("references.$i.relationship", data_get($ref, 'relationship')) }}"></td>
              <td><input type="text" name="references[{{ $i }}][contact]"      placeholder="Phone or email"      value="{{ old("references.$i.contact", data_get($ref, 'contact')) }}"></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>

    {{-- GENERAL INFORMATION --}}
    <div class="section-title">General Information</div>
    <div class="card">

      @php
        $q1 = old('general.q1_other_name',          data_get($general, 'q1_other_name'));
        $q2 = old('general.q2_name_change_info',    data_get($general, 'q2_name_change_info'));
        $q3 = old('general.q3_worked_here',         data_get($general, 'q3_worked_here'));
        $q4 = old('general.q4_relatives_here',      data_get($general, 'q4_relatives_here'));
      @endphp

      <div class="yn-row" data-yn data-explain-target="name-explain">
        <div class="yn-label">1. Have you ever used another name?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q1 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q1 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q1_other_name]" value="{{ $q1 }}">
      </div>

      <div class="yn-row" data-yn data-explain-target="name-explain">
        <div class="yn-label">2. Is additional info about name changes, assumed names, or nicknames necessary to check your work/educational record?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q2 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q2 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q2_name_change_info]" value="{{ $q2 }}">
      </div>
      <div class="yn-explain {{ ($q1 === 'yes' || $q2 === 'yes') ? 'show' : '' }}" id="name-explain">
        <textarea name="general[q2_explanation]" placeholder="Please explain name changes or aliases...">{{ old('general.q2_explanation', data_get($general, 'q2_explanation')) }}</textarea>
      </div>

      <div class="yn-row" data-yn data-explain-target="prev-company-explain">
        <div class="yn-label">3. Have you ever worked for this company before?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q3 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q3 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q3_worked_here]" value="{{ $q3 }}">
      </div>
      <div class="yn-explain {{ $q3 === 'yes' ? 'show' : '' }}" id="prev-company-explain">
        <div class="field"><label>Dates and Position</label><input type="text" name="general[q3_explanation]" placeholder="Dates and position held" value="{{ old('general.q3_explanation', data_get($general, 'q3_explanation')) }}"></div>
      </div>

      <div class="yn-row" data-yn data-explain-target="relatives-explain">
        <div class="yn-label">4. Do you have friends and/or relatives working for this company?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q4 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q4 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q4_relatives_here]" value="{{ $q4 }}">
      </div>
      <div class="yn-explain {{ $q4 === 'yes' ? 'show' : '' }}" id="relatives-explain">
        <div class="field"><label>Name(s) and Relationship(s)</label><input type="text" name="general[q4_explanation]" placeholder="Names and relationships" value="{{ old('general.q4_explanation', data_get($general, 'q4_explanation')) }}"></div>
      </div>

      <div class="yn-row" style="align-items:center">
        <div class="yn-label">5. On what date are you available to begin work?</div>
        <input type="date" name="general[available_begin_date]" value="{{ old('general.available_begin_date', data_get($general, 'available_begin_date')) }}" style="border:1.5px solid var(--border);border-radius:6px;padding:7px 10px;font-family:'DM Sans',sans-serif;font-size:13px;outline:none;color:var(--dark);background:var(--bg);max-width:220px">
      </div>

      <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
        <div style="font-size:13.5px;margin-bottom:8px;">6. Days/Hours available to work:</div>
        <div class="days-grid">
          @foreach (['mon' => 'Mon','tue' => 'Tue','wed' => 'Wed','thu' => 'Thu','fri' => 'Fri','sat' => 'Sat','sun' => 'Sun'] as $key => $label)
            <div class="day-cell">
              <span>{{ $label }}</span>
              <input type="text" name="general[work_hours_by_day][{{ $key }}]" placeholder="Hours" value="{{ old("general.work_hours_by_day.$key", data_get($general, "work_hours_by_day.$key")) }}">
            </div>
          @endforeach
        </div>
      </div>

      <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
        <div style="font-size:13.5px;margin-bottom:8px;">7. Are you available to work?</div>
        <div class="avail-grid">
          @php $availableTypes = old('general.available_types', data_get($general, 'available_types', [])); @endphp
          @foreach (['full' => 'Full-time','part' => 'Part-time','shift' => 'Shift Work','temp' => 'Temporary'] as $val => $label)
            <label class="avail-chip {{ in_array($val, $availableTypes, true) ? 'checked' : '' }}" data-avail-chip>
              <input type="checkbox" name="general[available_types][]" value="{{ $val }}" @checked(in_array($val, $availableTypes, true))> {{ $label }}
            </label>
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
        @php $v = old("general.{$q['key']}", data_get($general, $q['key'])); @endphp
        <div class="yn-row" data-yn>
          <div class="yn-label">
            {{ $q['label'] }}
            @if (!empty($q['small']))<small>{{ $q['small'] }}</small>@endif
          </div>
          <div class="yn-btns">
            <button type="button" class="yn-btn yes-btn {{ $v === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
            <button type="button" class="yn-btn no-btn  {{ $v === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
          </div>
          <input type="hidden" name="general[{{ $q['key'] }}]" value="{{ $v }}">
        </div>
      @endforeach

      @php
        $q15 = old('general.q15_felony_or_license_loss', data_get($general, 'q15_felony_or_license_loss'));
        $q16 = old('general.q16_disciplinary_history',   data_get($general, 'q16_disciplinary_history'));
      @endphp
      <div class="yn-row" data-yn data-explain-target="felony-explain">
        <div class="yn-label">15. Any history of loss of license and/or felony convictions?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q15 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q15 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q15_felony_or_license_loss]" value="{{ $q15 }}">
      </div>
      <div class="yn-explain {{ $q15 === 'yes' ? 'show' : '' }}" id="felony-explain">
        <textarea name="general[q15_explanation]" placeholder="Please explain...">{{ old('general.q15_explanation', data_get($general, 'q15_explanation')) }}</textarea>
      </div>

      <div class="yn-row" data-yn data-explain-target="discipline-explain">
        <div class="yn-label">16. Any history of loss or limitation of privileges or disciplinary action?</div>
        <div class="yn-btns">
          <button type="button" class="yn-btn yes-btn {{ $q16 === 'yes' ? 'active' : '' }}" data-yn-val="yes">Yes</button>
          <button type="button" class="yn-btn no-btn  {{ $q16 === 'no'  ? 'active' : '' }}" data-yn-val="no">No</button>
        </div>
        <input type="hidden" name="general[q16_disciplinary_history]" value="{{ $q16 }}">
      </div>
      <div class="yn-explain {{ $q16 === 'yes' ? 'show' : '' }}" id="discipline-explain">
        <textarea name="general[q16_explanation]" placeholder="Please explain...">{{ old('general.q16_explanation', data_get($general, 'q16_explanation')) }}</textarea>
      </div>
    </div>

    {{-- APPLICANT STATEMENT --}}
    <div class="section-title">Applicant Statement &amp; Agreement</div>
    <p style="font-size:12.5px;color:var(--soft);margin-bottom:14px;">Please read and check each paragraph below to acknowledge and agree.</p>

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
      @php $isChecked = old('agreement_'.$i, data_get($agreements, 'agreement_'.$i, false)); @endphp
      <label class="agreement-para {{ $isChecked ? 'checked-state' : '' }}" data-agreement>
        <input class="agree-cb" type="checkbox" name="agreement_{{ $i }}" value="1" @checked($isChecked)>
        <span class="agree-text">{{ $text }}</span>
      </label>
    @endforeach

    {{-- SIGNATURE --}}
    <div class="section-title">Electronic Signature</div>
    <p style="font-size:12.5px;color:var(--soft);margin-bottom:14px;">My signature below attests to the fact that I have read, understand, and agree to all of the above terms.</p>

    <div class="sig-section">
      <h3>Sign Below</h3>
      <div class="sig-tabs">
        <button type="button" class="sig-tab active" data-sig-tab="draw">Draw Signature</button>
        <button type="button" class="sig-tab"        data-sig-tab="type">Type Signature</button>
      </div>

      <input type="hidden" id="signature_mode"        name="signature_mode"        value="{{ old('signature_mode', data_get($signature, 'mode', 'draw')) }}">
      <input type="hidden" id="signature_drawn_data"  name="signature_drawn_data"  value="{{ old('signature_drawn_data', data_get($signature, 'drawn_data')) }}">

      <div id="sig-canvas-wrap">
        <canvas id="sig-canvas" width="820" height="130"></canvas>
        <p class="sig-hint">Draw your signature above using mouse or touch</p>
        <div class="sig-controls">
          <button type="button" class="btn-clear" id="sig-clear-btn">Clear</button>
        </div>
      </div>

      <div id="sig-type-wrap">
        <input id="sig-type-input" name="signature_typed" type="text" placeholder="Type your full name" value="{{ old('signature_typed', data_get($signature, 'typed')) }}">
        <p class="sig-hint">Your typed name serves as your electronic signature</p>
      </div>

      <div class="sig-bottom">
        <div class="field">
          <label>Printed Name</label>
          <input type="text" id="sig-print-name" name="signature_printed_name" placeholder="Print your full legal name" value="{{ old('signature_printed_name', data_get($signature, 'printed_name', $candidate->full_name)) }}" required>
        </div>
        <div class="field field-date">
          <label>Date</label>
          <input type="date" id="sig-date" name="signature_date" value="{{ old('signature_date', data_get($signature, 'signed_on', now()->format('Y-m-d'))) }}" required>
        </div>
      </div>
    </div>

    {{-- SUBMIT --}}
    <div class="submit-wrap">
      <button type="submit" class="btn-submit">Submit Application</button>
      <button type="button" class="btn-print" onclick="window.print()">Print</button>
    </div>
  </form>
</div>

<script>
  // ── Yes/No pill buttons ──
  document.querySelectorAll('[data-yn]').forEach(function(row){
    var hidden = row.querySelector('input[type="hidden"]');
    var explainId = row.dataset.explainTarget;
    row.querySelectorAll('.yn-btn').forEach(function(btn){
      btn.addEventListener('click', function(){
        row.querySelectorAll('.yn-btn').forEach(function(b){ b.classList.remove('active'); });
        btn.classList.add('active');
        var val = btn.dataset.ynVal;
        if (hidden) hidden.value = val;
        if (explainId) {
          var box = document.getElementById(explainId);
          if (box) box.classList.toggle('show', val === 'yes');
        }
      });
    });
  });

  // ── Availability chips ──
  document.querySelectorAll('[data-avail-chip]').forEach(function(chip){
    var cb = chip.querySelector('input[type="checkbox"]');
    chip.addEventListener('click', function(e){
      if (e.target !== cb) {
        cb.checked = !cb.checked;
      }
      chip.classList.toggle('checked', cb.checked);
    });
  });

  // ── Agreement cards ──
  document.querySelectorAll('[data-agreement]').forEach(function(label){
    var cb = label.querySelector('input[type="checkbox"]');
    label.addEventListener('click', function(e){
      // Native <label> already toggles the checkbox; just sync the visual state.
      setTimeout(function(){
        label.classList.toggle('checked-state', cb.checked);
      }, 0);
    });
  });

  // ── Signature canvas ──
  var canvas    = document.getElementById('sig-canvas');
  var ctx       = canvas.getContext('2d');
  var drawInput = document.getElementById('signature_drawn_data');
  var modeInput = document.getElementById('signature_mode');
  var form      = document.getElementById('employment-form');
  var drawing   = false;
  var hasSig    = false;

  function setupCanvas(){
    var ratio = window.devicePixelRatio || 1;
    var rect  = canvas.getBoundingClientRect();
    canvas.width  = rect.width  * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.scale(ratio, ratio);
    ctx.strokeStyle = '#1A2E2A';
    ctx.lineWidth   = 2.2;
    ctx.lineCap     = 'round';
    ctx.lineJoin    = 'round';

    if (drawInput.value) {
      var img = new Image();
      img.onload = function(){
        ctx.drawImage(img, 0, 0, rect.width, rect.height);
        hasSig = true;
        canvas.classList.add('has-sig');
      };
      img.src = drawInput.value;
    }
  }
  function getPos(e){
    var r = canvas.getBoundingClientRect();
    var src = e.touches ? e.touches[0] : e;
    return { x: src.clientX - r.left, y: src.clientY - r.top };
  }
  function beginDraw(e){ drawing = true; var p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
  function draw(e){ if (!drawing) return; var p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasSig = true; canvas.classList.add('has-sig'); }
  function endDraw(){ drawing = false; if (hasSig) drawInput.value = canvas.toDataURL('image/png'); }
  function clearCanvas(){
    var ratio = window.devicePixelRatio || 1;
    ctx.clearRect(0, 0, canvas.width / ratio, canvas.height / ratio);
    hasSig = false;
    drawInput.value = '';
    canvas.classList.remove('has-sig');
  }

  canvas.addEventListener('mousedown', beginDraw);
  canvas.addEventListener('mousemove', draw);
  canvas.addEventListener('mouseup', endDraw);
  canvas.addEventListener('mouseleave', endDraw);
  canvas.addEventListener('touchstart', function(e){ e.preventDefault(); beginDraw(e); }, { passive: false });
  canvas.addEventListener('touchmove',  function(e){ e.preventDefault(); draw(e); },      { passive: false });
  canvas.addEventListener('touchend',   endDraw);
  window.addEventListener('resize', setupCanvas);
  setupCanvas();

  document.getElementById('sig-clear-btn').addEventListener('click', clearCanvas);

  // ── Signature tab switch ──
  document.querySelectorAll('.sig-tab').forEach(function(tab){
    tab.addEventListener('click', function(){
      var mode = tab.dataset.sigTab;
      document.querySelectorAll('.sig-tab').forEach(function(t){ t.classList.remove('active'); });
      tab.classList.add('active');
      modeInput.value = mode;
      document.getElementById('sig-canvas-wrap').style.display = (mode === 'draw') ? 'block' : 'none';
      document.getElementById('sig-type-wrap').style.display   = (mode === 'type') ? 'block' : 'none';
    });
  });

  if (modeInput.value === 'type') {
    var typeTab = document.querySelector('.sig-tab[data-sig-tab="type"]');
    if (typeTab) typeTab.click();
  }

  // ── Submit guard ──
  form.addEventListener('submit', function(e){
    if (modeInput.value === 'draw' && hasSig) {
      drawInput.value = canvas.toDataURL('image/png');
    }
    var printName = document.getElementById('sig-print-name').value.trim();
    var typedSig  = document.getElementById('sig-type-input').value.trim();
    if (!printName) {
      e.preventDefault();
      alert('Please print your name in the signature section before submitting.');
      document.getElementById('sig-print-name').focus();
      return;
    }
    if (modeInput.value === 'draw' && !hasSig) {
      e.preventDefault();
      alert('Please draw your signature or switch to "Type Signature" before submitting.');
      return;
    }
    if (modeInput.value === 'type' && !typedSig) {
      e.preventDefault();
      alert('Please type your name as your electronic signature before submitting.');
      document.getElementById('sig-type-input').focus();
    }
  });
</script>
</body>
</html>
