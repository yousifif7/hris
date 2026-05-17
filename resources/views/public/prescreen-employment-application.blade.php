<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $company }} - Application for Employment</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --teal: #2a7d6f;
    --teal-light: #3fa08e;
    --teal-pale: #e8f5f3;
    --teal-mid: #c2e8e2;
    --dark: #1a2e2a;
    --mid: #4a6660;
    --soft: #7a9e98;
    --bg: #f7fafa;
    --white: #ffffff;
    --border: #c8deda;
    --error: #c0392b;
    --radius: 8px;
    --shadow: 0 2px 16px rgba(42, 125, 111, 0.1);
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

  .header-logo {
    font-family: 'DM Serif Display', serif;
    font-size: 28px;
    margin-bottom: 4px;
  }

  .header-sub {
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    opacity: 0.75;
  }

  .header-title {
    font-family: 'DM Serif Display', serif;
    font-size: 20px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.25);
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
    max-width: 980px;
    margin: 0 auto;
    padding: 0 24px 60px;
  }

  .status {
    padding: 12px 14px;
    border-radius: 8px;
    margin: 16px 0;
    font-size: 13px;
  }

  .status.success {
    background: #d4edda;
    color: #1e6f38;
    border: 1px solid #98d4a7;
  }

  .status.error {
    background: #fadbd8;
    color: #922b21;
    border: 1px solid #e8a7a0;
  }

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
    box-shadow: 0 0 0 3px rgba(42, 125, 111, 0.12);
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

  .emp-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
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

  .inline-choices {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: var(--mid);
    flex-wrap: wrap;
  }

  .inline-choices label { display: inline-flex; gap: 6px; align-items: center; }

  .yn-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--teal-pale);
    gap: 16px;
  }

  .yn-row:last-child { border-bottom: none; }
  .yn-label { flex: 1; font-size: 13.5px; line-height: 1.5; }
  .yn-controls { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
  .yn-controls label { font-size: 13px; color: var(--mid); display: inline-flex; gap: 6px; align-items: center; }

  .days-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin-top: 8px; }
  .day-cell { display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .day-cell span { font-size: 10px; font-weight: 600; color: var(--soft); text-transform: uppercase; }
  .day-cell input[type="text"] {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    padding: 6px 4px;
    font-size: 11px;
    text-align: center;
    background: var(--bg);
    font-family: 'DM Sans', sans-serif;
    outline: none;
  }

  .avail-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px; }
  .avail-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    padding: 8px 12px;
    background: var(--bg);
    font-size: 13px;
    color: var(--mid);
  }

  .edu-table, .ref-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
  .edu-table th, .ref-table th {
    background: var(--teal);
    color: #fff;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    padding: 10px 12px;
    text-align: left;
    font-weight: 500;
  }

  .edu-table td, .ref-table td { padding: 4px; border-bottom: 1px solid var(--border); background: var(--white); }
  .edu-table td:first-child {
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 600;
    color: var(--mid);
    text-transform: uppercase;
    background: var(--teal-pale);
    white-space: nowrap;
  }

  .edu-table td input[type="text"],
  .ref-table td input[type="text"] {
    width: 100%;
    border: 1px solid transparent;
    border-radius: 4px;
    padding: 7px 10px;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    color: var(--dark);
    background: var(--bg);
    outline: none;
  }

  .agreement-para {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    background: var(--white);
    border: 1.5px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    margin-bottom: 10px;
    font-size: 13px;
    line-height: 1.65;
    color: #3a3a3a;
  }

  .agreement-para.checked-state {
    background: var(--teal-pale);
    border-color: var(--teal);
  }

  .agree-cb { width: 18px; height: 18px; margin-top: 3px; accent-color: var(--teal); cursor: pointer; }

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
  }

  .sig-tab.active { background: var(--teal); border-color: var(--teal); color: #fff; }

  #sig-canvas-wrap { display: block; }
  #sig-type-wrap { display: none; }

  #sig-canvas {
    display: block;
    width: 100%;
    height: 130px;
    border: 2px dashed var(--border);
    border-radius: 8px;
    cursor: crosshair;
    background: #fafffe;
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
    background: #fafffe;
    outline: none;
    font-style: italic;
  }

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
  }

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
    box-shadow: 0 4px 20px rgba(42, 125, 111, 0.3);
  }

  .btn-back {
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
    text-decoration: none;
    display: inline-block;
  }

  @media (max-width: 680px) {
    .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
    .col-span-2 { grid-column: span 1; }
    .avail-grid { grid-template-columns: 1fr 1fr; }
    .days-grid { grid-template-columns: repeat(4, 1fr); }
    .form-wrap { padding: 0 14px 40px; }
    .disclaimer { margin: 16px 14px; }
    .sig-bottom { flex-direction: column; }
  }
</style>
</head>
<body>
@php
  $formAction = $formAction ?? route('public.prescreen.application.submit', ['token' => $token]);
  $backUrl = $backUrl ?? route('public.prescreen', ['token' => $token]);
  $backLabel = $backLabel ?? 'Back to Pre-Screen Form';
  $app = $applicationData ?? [];
  $employmentHistory = old('employment_history', data_get($app, 'employment_history', [[], [], []]));
  $educationRows = old('education_rows', data_get($app, 'education_rows', [
    ['level' => 'High School'],
    ['level' => 'College / University'],
    ['level' => 'Graduate / Professional'],
    ['level' => 'Trade School'],
    ['level' => 'Other'],
  ]));
  $references = old('references', data_get($app, 'references', [[], [], []]));
  $general = old('general', data_get($app, 'general', []));
  $agreements = data_get($app, 'agreements', []);
  $signature = data_get($app, 'signature', []);
@endphp

<div class="app-header">
  <div class="header-logo">{{ $company }}</div>
  <div class="header-sub">Behavioral Health</div>
  <div class="header-title">Application for Employment</div>
</div>

<div class="disclaimer">
  All applicants are considered for all positions without regard to race, religion, color, sex, gender, sexual orientation, pregnancy, age, national origin, ancestry, physical/mental disability, medical condition, military/veteran status, genetic information, marital status, ethnicity, citizenship or immigration status or any other protected classification, in accordance with applicable federal, state, and local laws.
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

    <div class="section-title">Personal Information</div>
    <div class="card">
      <div class="grid-2" style="margin-bottom:16px">
        <div class="field">
          <label>Position(s) Applied For</label>
          <input type="text" name="position_applied_for" value="{{ old('position_applied_for', data_get($app, 'position_applied_for', $candidate->category?->name)) }}" required>
        </div>
        <div class="field">
          <label>Date of Application</label>
          <input type="date" id="application_date" name="application_date" value="{{ old('application_date', data_get($app, 'application_date', now()->format('Y-m-d'))) }}">
        </div>
      </div>

      <div class="field" style="margin-bottom:16px">
        <label>Full Name (Last, First, Middle)</label>
        <input type="text" name="applicant_full_name" value="{{ old('applicant_full_name', data_get($app, 'applicant_full_name', $candidate->full_name)) }}" required>
      </div>

      <div class="grid-4" style="margin-bottom:16px">
        <div class="field col-span-2">
          <label>Street Address</label>
          <input type="text" name="address_street" value="{{ old('address_street', data_get($app, 'address_street')) }}">
        </div>
        <div class="field">
          <label>City</label>
          <input type="text" name="address_city" value="{{ old('address_city', data_get($app, 'address_city')) }}">
        </div>
        <div class="field">
          <label>State</label>
          <input type="text" name="address_state" value="{{ old('address_state', data_get($app, 'address_state')) }}">
        </div>
      </div>

      <div class="grid-3">
        <div class="field">
          <label>Zip Code</label>
          <input type="text" name="address_zip" value="{{ old('address_zip', data_get($app, 'address_zip')) }}">
        </div>
        <div class="field">
          <label>Main Phone</label>
          <input type="tel" name="phone_main" value="{{ old('phone_main', data_get($app, 'phone_main', $candidate->phone)) }}">
        </div>
        <div class="field">
          <label>Alternate Phone</label>
          <input type="tel" name="phone_alternate" value="{{ old('phone_alternate', data_get($app, 'phone_alternate')) }}">
        </div>
      </div>

      <div class="field" style="margin-top:16px">
        <label>Email Address</label>
        <input type="email" name="email" value="{{ old('email', data_get($app, 'email', $candidate->email)) }}">
      </div>
    </div>

    <div class="section-title">Employment Experience</div>
    @for ($i = 0; $i < 3; $i++)
      @php $employer = $employmentHistory[$i] ?? []; @endphp
      <div class="employer-block">
        <div class="emp-header">
          <span class="emp-number">Employer {{ $i + 1 }}{{ $i === 0 ? ' - Most Recent' : '' }}</span>
          <div class="inline-choices">
            <span>May we contact?</span>
            <label><input type="radio" name="employment_history[{{ $i }}][may_contact]" value="yes" @checked(old("employment_history.$i.may_contact", data_get($employer, 'may_contact')) === 'yes')> Yes</label>
            <label><input type="radio" name="employment_history[{{ $i }}][may_contact]" value="no" @checked(old("employment_history.$i.may_contact", data_get($employer, 'may_contact')) === 'no')> No</label>
          </div>
        </div>

        <div class="grid-2" style="margin-bottom:12px">
          <div class="field">
            <label>Employer Name</label>
            <input type="text" name="employment_history[{{ $i }}][employer_name]" value="{{ old("employment_history.$i.employer_name", data_get($employer, 'employer_name')) }}">
          </div>
          <div class="field">
            <label>Supervisor</label>
            <input type="text" name="employment_history[{{ $i }}][supervisor]" value="{{ old("employment_history.$i.supervisor", data_get($employer, 'supervisor')) }}">
          </div>
        </div>

        <div class="field" style="margin-bottom:12px">
          <label>Street Address</label>
          <input type="text" name="employment_history[{{ $i }}][street_address]" value="{{ old("employment_history.$i.street_address", data_get($employer, 'street_address')) }}">
        </div>

        <div class="grid-3" style="margin-bottom:12px">
          <div class="field">
            <label>Phone Number</label>
            <input type="tel" name="employment_history[{{ $i }}][phone]" value="{{ old("employment_history.$i.phone", data_get($employer, 'phone')) }}">
          </div>
          <div class="field">
            <label>From (Month/Year)</label>
            <input type="text" name="employment_history[{{ $i }}][from]" value="{{ old("employment_history.$i.from", data_get($employer, 'from')) }}">
          </div>
          <div class="field">
            <label>To (Month/Year)</label>
            <input type="text" name="employment_history[{{ $i }}][to]" value="{{ old("employment_history.$i.to", data_get($employer, 'to')) }}">
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label>Job Title and Duties</label>
            <textarea name="employment_history[{{ $i }}][job_title_duties]">{{ old("employment_history.$i.job_title_duties", data_get($employer, 'job_title_duties')) }}</textarea>
          </div>
          <div class="field">
            <label>Reason for Leaving</label>
            <textarea name="employment_history[{{ $i }}][reason_for_leaving]">{{ old("employment_history.$i.reason_for_leaving", data_get($employer, 'reason_for_leaving')) }}</textarea>
          </div>
        </div>
      </div>
    @endfor

    <div class="card">
      <div class="field">
        <label>Have you ever been involuntarily terminated or asked to resign from any job? Please explain</label>
        <textarea name="termination_explanation">{{ old('termination_explanation', data_get($app, 'termination_explanation')) }}</textarea>
      </div>
      <div class="field" style="margin-top:14px">
        <label>Please explain any gaps in your employment history</label>
        <textarea name="employment_gaps_explanation">{{ old('employment_gaps_explanation', data_get($app, 'employment_gaps_explanation')) }}</textarea>
      </div>
      <div class="field" style="margin-top:14px">
        <label>Additional experience, job-related skills, languages, or qualifications</label>
        <textarea name="additional_experience">{{ old('additional_experience', data_get($app, 'additional_experience')) }}</textarea>
      </div>
    </div>

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
            <tr>
              <td>
                {{ old("education_rows.$i.level", data_get($row, 'level', 'Level '.($i + 1))) }}
                <input type="hidden" name="education_rows[{{ $i }}][level]" value="{{ old("education_rows.$i.level", data_get($row, 'level', 'Level '.($i + 1))) }}">
              </td>
              <td><input type="text" name="education_rows[{{ $i }}][school_name]" value="{{ old("education_rows.$i.school_name", data_get($row, 'school_name')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][years_completed]" value="{{ old("education_rows.$i.years_completed", data_get($row, 'years_completed')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][degree]" value="{{ old("education_rows.$i.degree", data_get($row, 'degree')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][major]" value="{{ old("education_rows.$i.major", data_get($row, 'major')) }}"></td>
              <td><input type="text" name="education_rows[{{ $i }}][training]" value="{{ old("education_rows.$i.training", data_get($row, 'training')) }}"></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="section-title">Business and Professional References</div>
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
              <td><input type="text" name="references[{{ $i }}][name_title]" value="{{ old("references.$i.name_title", data_get($ref, 'name_title')) }}"></td>
              <td><input type="text" name="references[{{ $i }}][relationship]" value="{{ old("references.$i.relationship", data_get($ref, 'relationship')) }}"></td>
              <td><input type="text" name="references[{{ $i }}][contact]" value="{{ old("references.$i.contact", data_get($ref, 'contact')) }}"></td>
            </tr>
          @endfor
        </tbody>
      </table>
    </div>

    <div class="section-title">General Information</div>
    <div class="card">
      <div class="yn-row">
        <div class="yn-label">1. Have you ever used another name?</div>
        <div class="yn-controls">
          <label><input type="radio" name="general[q1_other_name]" value="yes" @checked(old('general.q1_other_name', data_get($general, 'q1_other_name')) === 'yes')> Yes</label>
          <label><input type="radio" name="general[q1_other_name]" value="no" @checked(old('general.q1_other_name', data_get($general, 'q1_other_name')) === 'no')> No</label>
        </div>
      </div>

      <div class="yn-row">
        <div class="yn-label">2. Is additional info about name changes, assumed names, or nicknames necessary?</div>
        <div class="yn-controls">
          <label><input type="radio" name="general[q2_name_change_info]" value="yes" @checked(old('general.q2_name_change_info', data_get($general, 'q2_name_change_info')) === 'yes')> Yes</label>
          <label><input type="radio" name="general[q2_name_change_info]" value="no" @checked(old('general.q2_name_change_info', data_get($general, 'q2_name_change_info')) === 'no')> No</label>
        </div>
      </div>

      <div class="field" style="margin-top:10px">
        <label>Name Changes / Aliases Explanation</label>
        <textarea name="general[q2_explanation]">{{ old('general.q2_explanation', data_get($general, 'q2_explanation')) }}</textarea>
      </div>

      <div class="yn-row">
        <div class="yn-label">3. Have you ever worked for this company before?</div>
        <div class="yn-controls">
          <label><input type="radio" name="general[q3_worked_here]" value="yes" @checked(old('general.q3_worked_here', data_get($general, 'q3_worked_here')) === 'yes')> Yes</label>
          <label><input type="radio" name="general[q3_worked_here]" value="no" @checked(old('general.q3_worked_here', data_get($general, 'q3_worked_here')) === 'no')> No</label>
        </div>
      </div>

      <div class="field" style="margin-top:10px">
        <label>Dates and Position</label>
        <input type="text" name="general[q3_explanation]" value="{{ old('general.q3_explanation', data_get($general, 'q3_explanation')) }}">
      </div>

      <div class="yn-row">
        <div class="yn-label">4. Do you have friends and/or relatives working for this company?</div>
        <div class="yn-controls">
          <label><input type="radio" name="general[q4_relatives_here]" value="yes" @checked(old('general.q4_relatives_here', data_get($general, 'q4_relatives_here')) === 'yes')> Yes</label>
          <label><input type="radio" name="general[q4_relatives_here]" value="no" @checked(old('general.q4_relatives_here', data_get($general, 'q4_relatives_here')) === 'no')> No</label>
        </div>
      </div>

      <div class="field" style="margin-top:10px">
        <label>Name(s) and Relationship(s)</label>
        <input type="text" name="general[q4_explanation]" value="{{ old('general.q4_explanation', data_get($general, 'q4_explanation')) }}">
      </div>

      <div class="yn-row" style="align-items:center">
        <div class="yn-label">5. On what date are you available to begin work?</div>
        <input type="date" name="general[available_begin_date]" value="{{ old('general.available_begin_date', data_get($general, 'available_begin_date')) }}" style="max-width:220px">
      </div>

      <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
        <div style="font-size:13.5px;margin-bottom:8px;">6. Days/Hours available to work</div>
        <div class="days-grid">
          @foreach (['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'] as $day)
            <div class="day-cell">
              <span>{{ strtoupper($day) }}</span>
              <input type="text" name="general[work_hours_by_day][{{ $day }}]" value="{{ old("general.work_hours_by_day.$day", data_get($general, "work_hours_by_day.$day")) }}">
            </div>
          @endforeach
        </div>
      </div>

      <div style="padding:12px 0; border-bottom:1px solid var(--teal-pale)">
        <div style="font-size:13.5px;margin-bottom:8px;">7. Are you available to work?</div>
        <div class="avail-grid">
          @php $availableTypes = old('general.available_types', data_get($general, 'available_types', [])); @endphp
          <label class="avail-chip"><input type="checkbox" name="general[available_types][]" value="full" @checked(in_array('full', $availableTypes, true))> Full-time</label>
          <label class="avail-chip"><input type="checkbox" name="general[available_types][]" value="part" @checked(in_array('part', $availableTypes, true))> Part-time</label>
          <label class="avail-chip"><input type="checkbox" name="general[available_types][]" value="shift" @checked(in_array('shift', $availableTypes, true))> Shift Work</label>
          <label class="avail-chip"><input type="checkbox" name="general[available_types][]" value="temp" @checked(in_array('temp', $availableTypes, true))> Temporary</label>
        </div>
      </div>

      @php
        $questions = [
          ['key' => 'q8_transportation', 'label' => '8. If hired, would you have reliable transportation to and from work?'],
          ['key' => 'q9_can_travel', 'label' => '9. Can you travel if the position requires it?'],
          ['key' => 'q10_can_relocate', 'label' => '10. Can you relocate if the position requires it?'],
          ['key' => 'q11_over_18', 'label' => '11. Are you at least 18 years old?'],
          ['key' => 'q12_work_auth', 'label' => '12. If hired, can you present evidence of identity and legal right to work in this country?'],
          ['key' => 'q13_essential_functions', 'label' => '13. Are you able to perform the essential job functions with or without reasonable accommodation?'],
          ['key' => 'q14_illegal_drug_use', 'label' => '14. Are you currently engaging in the use of illegal drugs?'],
          ['key' => 'q15_felony_or_license_loss', 'label' => '15. Any history of loss of license and/or felony convictions?'],
          ['key' => 'q16_disciplinary_history', 'label' => '16. Any history of loss or limitation of privileges or disciplinary action?'],
        ];
      @endphp

      @foreach ($questions as $q)
        <div class="yn-row">
          <div class="yn-label">{{ $q['label'] }}</div>
          <div class="yn-controls">
            <label><input type="radio" name="general[{{ $q['key'] }}]" value="yes" @checked(old("general.{$q['key']}", data_get($general, $q['key'])) === 'yes')> Yes</label>
            <label><input type="radio" name="general[{{ $q['key'] }}]" value="no" @checked(old("general.{$q['key']}", data_get($general, $q['key'])) === 'no')> No</label>
          </div>
        </div>
      @endforeach

      <div class="field" style="margin-top:10px">
        <label>Q15 Explanation</label>
        <textarea name="general[q15_explanation]">{{ old('general.q15_explanation', data_get($general, 'q15_explanation')) }}</textarea>
      </div>

      <div class="field" style="margin-top:10px">
        <label>Q16 Explanation</label>
        <textarea name="general[q16_explanation]">{{ old('general.q16_explanation', data_get($general, 'q16_explanation')) }}</textarea>
      </div>
    </div>

    <div class="section-title">Applicant Statement and Agreement</div>
    <div class="card">
      @for ($i = 1; $i <= 7; $i++)
        @php
          $agreementKey = 'agreement_'.$i;
          $isChecked = old($agreementKey, data_get($agreements, $agreementKey, false));
        @endphp
        <label class="agreement-para {{ $isChecked ? 'checked-state' : '' }}" onclick="toggleAgreement(this)">
          <input class="agree-cb" type="checkbox" name="{{ $agreementKey }}" value="1" @checked($isChecked) onclick="event.stopPropagation()">
          <span>I acknowledge and agree to paragraph {{ $i }} of the applicant statement.</span>
        </label>
      @endfor
    </div>

    <div class="section-title">Electronic Signature</div>
    <div class="sig-section">
      <h3>Sign Below</h3>
      <div class="sig-tabs">
        <button type="button" class="sig-tab active" onclick="switchSigTab('draw', this)">Draw Signature</button>
        <button type="button" class="sig-tab" onclick="switchSigTab('type', this)">Type Signature</button>
      </div>

      <input type="hidden" id="signature_mode" name="signature_mode" value="{{ old('signature_mode', data_get($signature, 'mode', 'draw')) }}">
      <input type="hidden" id="signature_drawn_data" name="signature_drawn_data" value="{{ old('signature_drawn_data', data_get($signature, 'drawn_data')) }}">

      <div id="sig-canvas-wrap">
        <canvas id="sig-canvas" width="820" height="130"></canvas>
        <p class="sig-hint">Draw your signature above using mouse or touch</p>
        <div class="sig-controls">
          <button type="button" class="btn-clear" onclick="clearCanvas()">Clear</button>
        </div>
      </div>

      <div id="sig-type-wrap">
        <input id="sig-type-input" name="signature_typed" type="text" placeholder="Type your full name" value="{{ old('signature_typed', data_get($signature, 'typed')) }}">
        <p class="sig-hint">Your typed name serves as your electronic signature</p>
      </div>

      <div class="sig-bottom">
        <div class="field">
          <label>Printed Name</label>
          <input type="text" id="sig-print-name" name="signature_printed_name" value="{{ old('signature_printed_name', data_get($signature, 'printed_name', $candidate->full_name)) }}" required>
        </div>
        <div class="field field-date">
          <label>Date</label>
          <input type="date" id="sig-date" name="signature_date" value="{{ old('signature_date', data_get($signature, 'signed_on', now()->format('Y-m-d'))) }}" required>
        </div>
      </div>
    </div>

    <div class="submit-wrap">
      <button type="submit" class="btn-submit">Submit Employment Application</button>
      <a class="btn-back" href="{{ $backUrl }}">{{ $backLabel }}</a>
    </div>
  </form>
</div>

<script>
  function toggleAgreement(label) {
    const cb = label.querySelector('input[type="checkbox"]');
    cb.checked = !cb.checked;
    label.classList.toggle('checked-state', cb.checked);
  }

  const canvas = document.getElementById('sig-canvas');
  const ctx = canvas.getContext('2d');
  const drawInput = document.getElementById('signature_drawn_data');
  const modeInput = document.getElementById('signature_mode');
  const form = document.getElementById('employment-form');
  let drawing = false;
  let hasSig = false;

  function setupCanvas() {
    const ratio = window.devicePixelRatio || 1;
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.scale(ratio, ratio);
    ctx.strokeStyle = '#1a2e2a';
    ctx.lineWidth = 2.2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    if (drawInput.value) {
      const img = new Image();
      img.onload = function () {
        ctx.drawImage(img, 0, 0, rect.width, rect.height);
        hasSig = true;
        canvas.classList.add('has-sig');
      };
      img.src = drawInput.value;
    }
  }

  function getPos(e) {
    const r = canvas.getBoundingClientRect();
    const src = e.touches ? e.touches[0] : e;
    return { x: src.clientX - r.left, y: src.clientY - r.top };
  }

  function beginDraw(e) {
    drawing = true;
    const p = getPos(e);
    ctx.beginPath();
    ctx.moveTo(p.x, p.y);
  }

  function draw(e) {
    if (!drawing) return;
    const p = getPos(e);
    ctx.lineTo(p.x, p.y);
    ctx.stroke();
    hasSig = true;
    canvas.classList.add('has-sig');
  }

  function endDraw() {
    drawing = false;
    if (hasSig) {
      drawInput.value = canvas.toDataURL('image/png');
    }
  }

  function clearCanvas() {
    const ratio = window.devicePixelRatio || 1;
    ctx.clearRect(0, 0, canvas.width / ratio, canvas.height / ratio);
    hasSig = false;
    drawInput.value = '';
    canvas.classList.remove('has-sig');
  }

  function switchSigTab(mode, btn) {
    document.querySelectorAll('.sig-tab').forEach(function (t) { t.classList.remove('active'); });
    btn.classList.add('active');
    modeInput.value = mode;
    document.getElementById('sig-canvas-wrap').style.display = mode === 'draw' ? 'block' : 'none';
    document.getElementById('sig-type-wrap').style.display = mode === 'type' ? 'block' : 'none';
  }

  canvas.addEventListener('mousedown', beginDraw);
  canvas.addEventListener('mousemove', draw);
  canvas.addEventListener('mouseup', endDraw);
  canvas.addEventListener('mouseleave', endDraw);
  canvas.addEventListener('touchstart', function (e) { e.preventDefault(); beginDraw(e); }, { passive: false });
  canvas.addEventListener('touchmove', function (e) { e.preventDefault(); draw(e); }, { passive: false });
  canvas.addEventListener('touchend', endDraw);

  window.addEventListener('resize', setupCanvas);
  setupCanvas();

  if (modeInput.value === 'type') {
    const typeTab = document.querySelectorAll('.sig-tab')[1];
    switchSigTab('type', typeTab);
  }

  form.addEventListener('submit', function () {
    if (modeInput.value === 'draw' && hasSig) {
      drawInput.value = canvas.toDataURL('image/png');
    }
  });
</script>
</body>
</html>
