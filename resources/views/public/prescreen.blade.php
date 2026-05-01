<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post-Interview Application — {{ $company }}</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #f4f5f7;
      --surface: #ffffff;
      --border: #dfe1e6;
      --text: #172b4d;
      --text2: #5e6c84;
      --accent: #5ac6cc;
      --accent2: #4fbfc7;
      --accent-glow: rgba(90, 198, 204, 0.12);
      --green: #00875a;
      --green-bg: rgba(0, 135, 90, 0.08);
      --red: #de350b;
      --red-bg: rgba(222, 53, 11, 0.08);
      --radius: 10px;
      --radius-lg: 14px;
      --shadow: 0 2px 8px rgba(0, 0, 0, 0.08), 0 8px 32px rgba(0, 0, 0, 0.06);
      --font: 'DM Sans', sans-serif;
      --display: 'Playfair Display', serif;
    }

    *, *::before, *::after { box-sizing: border-box; }
    body {
      margin: 0;
      min-height: 100vh;
      font-family: var(--font);
      background: radial-gradient(circle at top right, rgba(90, 198, 204, 0.12), transparent 42%), var(--bg);
      color: var(--text);
      padding: 24px 14px 44px;
    }

    .wrap { width: 100%; max-width: 760px; margin: 0 auto; }
    .brand { display: flex; gap: 12px; align-items: center; margin-bottom: 20px; }
    .brand-icon {
      width: 46px;
      height: 46px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
    }
    .brand h1 { margin: 0; font-family: var(--display); font-size: 24px; }
    .brand p { margin: 1px 0 0; color: var(--text2); font-size: 13px; }

    .card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow);
      padding: 26px;
    }

    .title { margin: 0 0 6px; font-size: 22px; }
    .sub { margin: 0 0 20px; color: var(--text2); line-height: 1.6; font-size: 14px; }
    .hint {
      border: 1px solid var(--border);
      background: #fafbfc;
      border-radius: var(--radius);
      padding: 12px 14px;
      font-size: 13px;
      margin-bottom: 18px;
      color: var(--text2);
    }

    .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .group { margin-bottom: 12px; }
    label { display: block; font-size: 12px; font-weight: 600; color: var(--text2); margin-bottom: 6px; }
    input, select, textarea {
      width: 100%;
      padding: 10px 12px;
      border-radius: var(--radius);
      border: 1px solid var(--border);
      outline: none;
      font: inherit;
      color: var(--text);
      background: var(--surface);
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px var(--accent-glow);
    }
    textarea { min-height: 110px; resize: vertical; }
    .file-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 12px;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background: #fafbfc;
      color: var(--text);
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
    }

    .actions { margin-top: 18px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .btn {
      border: none;
      cursor: pointer;
      border-radius: var(--radius);
      font-weight: 700;
      font-size: 14px;
      padding: 12px 20px;
      color: #fff;
      background: var(--accent);
      transition: transform 0.15s, box-shadow 0.15s, background 0.15s;
    }
    .btn:hover { background: var(--accent2); transform: translateY(-1px); box-shadow: 0 6px 16px rgba(79, 191, 199, 0.35); }

    .btn.secondary {
      background: #6b778c;
    }

    .btn.secondary:hover {
      background: #5e6c84;
      box-shadow: 0 6px 16px rgba(94, 108, 132, 0.35);
    }

    .wizard {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 18px;
      flex-wrap: wrap;
    }

    .wizard-step {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid var(--border);
      border-radius: 999px;
      padding: 6px 12px;
      font-size: 12px;
      color: var(--text2);
      background: #fafbfc;
      font-weight: 600;
    }

    .wizard-step.active {
      border-color: var(--accent);
      color: var(--text);
      background: var(--accent-glow);
    }

    .wizard-step .dot {
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      background: #dfe1e6;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
    }

    .wizard-step.active .dot {
      background: var(--accent);
    }

    .step-panel {
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 14px;
      margin-bottom: 14px;
      background: #fcfdff;
    }

    .step-panel.hidden {
      display: none;
    }

    .section-h {
      font-weight: 700;
      margin: 0 0 8px;
      font-size: 15px;
    }

    .history-card {
      border: 1px solid var(--border);
      border-radius: var(--radius);
      padding: 12px;
      margin-bottom: 10px;
      background: #fff;
    }

    .history-title {
      margin: 0 0 10px;
      font-size: 12px;
      font-weight: 700;
      color: var(--text2);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .check-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 8px;
      margin-top: 8px;
    }

    .check-item {
      display: flex;
      align-items: flex-start;
      gap: 8px;
      font-size: 13px;
      color: var(--text2);
    }

    .check-item input {
      width: auto;
      margin-top: 2px;
    }

    .ok, .err {
      border-radius: var(--radius);
      padding: 12px 14px;
      font-size: 13px;
      margin-bottom: 14px;
    }
    .ok { background: var(--green-bg); color: var(--green); border: 1px solid rgba(0, 135, 90, 0.2); }
    .err { background: var(--red-bg); color: var(--red); border: 1px solid rgba(222, 53, 11, 0.2); }

    @media (max-width: 640px) {
      .card { padding: 20px 14px; }
      .row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  @php
    $appData = $existing?->employment_application_data ?? [];
    $history = old('employment_history', $appData['employment_history'] ?? [[], [], []]);
    if (count($history) < 3) {
      $history = array_pad($history, 3, []);
    }
    $agreements = $appData['agreements'] ?? [];
  @endphp

  <div class="wrap">
    <div class="brand">
      <div class="brand-icon">M</div>
      <div>
        <h1>{{ $company }}</h1>
        <p>Candidate Application Portal</p>
      </div>
    </div>

    <div class="card">
      <h2 class="title">Post-Interview Application</h2>
      <p class="sub">
        Hi {{ $candidate->first_name }}, complete Step 1 first, then click Advance to continue to the Employment Application step.
      </p>

      @if (!empty($submitted))
        <div class="ok">Your form has been submitted successfully. Our team has been notified.</div>
      @endif

      @if ($errors->any())
        <div class="err">
          {{ $errors->first() }}
        </div>
      @endif

      <div class="hint">
        Candidate: <strong>{{ $candidate->full_name }}</strong>
      </div>

      <div class="wizard">
        <div id="stepPill1" class="wizard-step active"><span class="dot">1</span>General Form</div>
        <div id="stepPill2" class="wizard-step"><span class="dot">2</span>Employment Application</div>
      </div>

      <form method="POST" action="{{ route('public.prescreen.submit', ['token' => $token]) }}" enctype="multipart/form-data">
        @csrf

        <div id="step1Panel" class="step-panel">
          <h3 class="section-h">Step 1: General Pre-Screening</h3>

          <div class="row">
            <div class="group">
              <label for="education_level">Highest Education</label>
              <select id="education_level" name="education_level" required>
                @php $edu = old('education_level', $existing?->education_level); @endphp
                <option value="">Select...</option>
                <option value="high_school" @selected($edu === 'high_school')>High School</option>
                <option value="associates" @selected($edu === 'associates')>Associates</option>
                <option value="bachelors" @selected($edu === 'bachelors')>Bachelors</option>
                <option value="masters" @selected($edu === 'masters')>Masters</option>
                <option value="doctorate" @selected($edu === 'doctorate')>Doctorate</option>
              </select>
            </div>

            <div class="group">
              <label for="years_experience">Years of Experience</label>
              <input id="years_experience" name="years_experience" type="number" min="0" max="60" value="{{ old('years_experience', $existing?->years_experience ?? 0) }}" required>
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="licenses">Active Licenses</label>
              <input id="licenses" name="licenses" placeholder="LMSW, LPC, etc." value="{{ old('licenses', $existing?->licenses) }}">
            </div>

            <div class="group">
              <label for="availability">Availability</label>
              <select id="availability" name="availability" required>
                @php $av = old('availability', $existing?->availability); @endphp
                <option value="">Select...</option>
                <option value="full_time" @selected($av === 'full_time')>Full-Time</option>
                <option value="part_time" @selected($av === 'part_time')>Part-Time</option>
                <option value="either" @selected($av === 'either')>Either</option>
                <option value="contractor" @selected($av === 'contractor')>1099 / Contractor</option>
              </select>
            </div>
          </div>

          <div class="group">
            <label for="earliest_start_date">Earliest Start Date</label>
            <input id="earliest_start_date" name="earliest_start_date" type="date" value="{{ old('earliest_start_date', optional($existing?->earliest_start_date)->format('Y-m-d')) }}">
          </div>

          <div class="group">
            <label for="additional_notes">Additional Notes</label>
            <textarea id="additional_notes" name="additional_notes" placeholder="Anything else you'd like the team to know...">{{ old('additional_notes', $existing?->additional_notes) }}</textarea>
          </div>
          <div class="group">
            <label for="uploaded_form">Upload Completed PDF Form (Optional)</label>
            <input id="uploaded_form" name="uploaded_form" type="file" accept="application/pdf">
            <div class="hint" style="margin-top:8px;margin-bottom:0">
              Optional: upload a PDF only if needed. Preferred is the online Step 2 employment section below.
            </div>
            @if ($existing?->uploaded_form_path)
              <div style="margin-top:10px">
                <a class="file-link" href="{{ asset($existing->uploaded_form_path) }}" target="_blank" rel="noopener">
                  View current uploaded PDF
                </a>
              </div>
            @endif
          </div>

          <div class="actions">
            <button type="button" id="advanceBtn" class="btn">Advance to Employment Form</button>
          </div>
        </div>

        <div id="step2Panel" class="step-panel hidden">
          <h3 class="section-h">Step 2: Employment Application</h3>

          <div class="row">
            <div class="group">
              <label for="position_applied_for">Position Applied For</label>
              <input id="position_applied_for" name="position_applied_for" value="{{ old('position_applied_for', $appData['position_applied_for'] ?? '') }}" required>
            </div>

            <div class="group">
              <label for="application_date">Application Date</label>
              <input id="application_date" name="application_date" type="date" value="{{ old('application_date', $appData['application_date'] ?? now()->format('Y-m-d')) }}">
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="applicant_full_name">Applicant Full Name</label>
              <input id="applicant_full_name" name="applicant_full_name" value="{{ old('applicant_full_name', $appData['applicant_full_name'] ?? $candidate->full_name) }}" required>
            </div>

            <div class="group">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" value="{{ old('email', $appData['email'] ?? $candidate->email) }}">
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="phone_main">Primary Phone</label>
              <input id="phone_main" name="phone_main" value="{{ old('phone_main', $appData['phone_main'] ?? '') }}">
            </div>

            <div class="group">
              <label for="phone_alternate">Alternate Phone</label>
              <input id="phone_alternate" name="phone_alternate" value="{{ old('phone_alternate', $appData['phone_alternate'] ?? '') }}">
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="address_street">Street Address</label>
              <input id="address_street" name="address_street" value="{{ old('address_street', $appData['address_street'] ?? '') }}">
            </div>

            <div class="group">
              <label for="address_city">City</label>
              <input id="address_city" name="address_city" value="{{ old('address_city', $appData['address_city'] ?? '') }}">
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="address_state">State</label>
              <input id="address_state" name="address_state" value="{{ old('address_state', $appData['address_state'] ?? '') }}">
            </div>

            <div class="group">
              <label for="address_zip">Zip</label>
              <input id="address_zip" name="address_zip" value="{{ old('address_zip', $appData['address_zip'] ?? '') }}">
            </div>
          </div>

          @for ($i = 0; $i < 3; $i++)
            <div class="history-card">
              <p class="history-title">Employment History {{ $i + 1 }}</p>

              <div class="row">
                <div class="group">
                  <label for="employment_history_{{ $i }}_employer_name">Employer Name</label>
                  <input id="employment_history_{{ $i }}_employer_name" name="employment_history[{{ $i }}][employer_name]" value="{{ old("employment_history.$i.employer_name", $history[$i]['employer_name'] ?? '') }}">
                </div>

                <div class="group">
                  <label for="employment_history_{{ $i }}_supervisor">Supervisor</label>
                  <input id="employment_history_{{ $i }}_supervisor" name="employment_history[{{ $i }}][supervisor]" value="{{ old("employment_history.$i.supervisor", $history[$i]['supervisor'] ?? '') }}">
                </div>
              </div>

              <div class="row">
                <div class="group">
                  <label for="employment_history_{{ $i }}_phone">Phone</label>
                  <input id="employment_history_{{ $i }}_phone" name="employment_history[{{ $i }}][phone]" value="{{ old("employment_history.$i.phone", $history[$i]['phone'] ?? '') }}">
                </div>

                <div class="group">
                  <label for="employment_history_{{ $i }}_street_address">Street Address</label>
                  <input id="employment_history_{{ $i }}_street_address" name="employment_history[{{ $i }}][street_address]" value="{{ old("employment_history.$i.street_address", $history[$i]['street_address'] ?? '') }}">
                </div>
              </div>

              <div class="row">
                <div class="group">
                  <label for="employment_history_{{ $i }}_from">From</label>
                  <input id="employment_history_{{ $i }}_from" name="employment_history[{{ $i }}][from]" value="{{ old("employment_history.$i.from", $history[$i]['from'] ?? '') }}">
                </div>

                <div class="group">
                  <label for="employment_history_{{ $i }}_to">To</label>
                  <input id="employment_history_{{ $i }}_to" name="employment_history[{{ $i }}][to]" value="{{ old("employment_history.$i.to", $history[$i]['to'] ?? '') }}">
                </div>
              </div>

              <div class="group">
                <label for="employment_history_{{ $i }}_job_title_duties">Job Title / Duties</label>
                <textarea id="employment_history_{{ $i }}_job_title_duties" name="employment_history[{{ $i }}][job_title_duties]">{{ old("employment_history.$i.job_title_duties", $history[$i]['job_title_duties'] ?? '') }}</textarea>
              </div>

              <div class="group">
                <label for="employment_history_{{ $i }}_reason_for_leaving">Reason for Leaving</label>
                <textarea id="employment_history_{{ $i }}_reason_for_leaving" name="employment_history[{{ $i }}][reason_for_leaving]">{{ old("employment_history.$i.reason_for_leaving", $history[$i]['reason_for_leaving'] ?? '') }}</textarea>
              </div>

              <div class="group">
                <label for="employment_history_{{ $i }}_may_contact">May we contact this employer?</label>
                <select id="employment_history_{{ $i }}_may_contact" name="employment_history[{{ $i }}][may_contact]">
                  @php $mayContact = old("employment_history.$i.may_contact", $history[$i]['may_contact'] ?? ''); @endphp
                  <option value="">Select...</option>
                  <option value="yes" @selected($mayContact === 'yes')>Yes</option>
                  <option value="no" @selected($mayContact === 'no')>No</option>
                </select>
              </div>
            </div>
          @endfor

          <div class="group">
            <label for="termination_explanation">Termination Explanation (if applicable)</label>
            <textarea id="termination_explanation" name="termination_explanation">{{ old('termination_explanation', $appData['termination_explanation'] ?? '') }}</textarea>
          </div>

          <div class="group">
            <label for="employment_gaps_explanation">Employment Gaps Explanation</label>
            <textarea id="employment_gaps_explanation" name="employment_gaps_explanation">{{ old('employment_gaps_explanation', $appData['employment_gaps_explanation'] ?? '') }}</textarea>
          </div>

          <div class="group">
            <label for="additional_experience">Additional Experience / Skills</label>
            <textarea id="additional_experience" name="additional_experience">{{ old('additional_experience', $appData['additional_experience'] ?? '') }}</textarea>
          </div>

          <div class="group">
            <label>Agreements</label>
            <div class="check-grid">
              <label class="check-item"><input type="checkbox" name="agreement_1" value="1" {{ old('agreement_1', $agreements['agreement_1'] ?? false) ? 'checked' : '' }} required>I certify that all information provided is true and complete.</label>
              <label class="check-item"><input type="checkbox" name="agreement_2" value="1" {{ old('agreement_2', $agreements['agreement_2'] ?? false) ? 'checked' : '' }} required>I authorize verification of all information in this application.</label>
              <label class="check-item"><input type="checkbox" name="agreement_3" value="1" {{ old('agreement_3', $agreements['agreement_3'] ?? false) ? 'checked' : '' }} required>I understand this application does not guarantee employment.</label>
              <label class="check-item"><input type="checkbox" name="agreement_4" value="1" {{ old('agreement_4', $agreements['agreement_4'] ?? false) ? 'checked' : '' }} required>I understand any false statement may result in disqualification or termination.</label>
              <label class="check-item"><input type="checkbox" name="agreement_5" value="1" {{ old('agreement_5', $agreements['agreement_5'] ?? false) ? 'checked' : '' }} required>I acknowledge company policies may change and I will follow updated policies.</label>
              <label class="check-item"><input type="checkbox" name="agreement_6" value="1" {{ old('agreement_6', $agreements['agreement_6'] ?? false) ? 'checked' : '' }} required>I consent to employment-related background checks where legally permitted.</label>
              <label class="check-item"><input type="checkbox" name="agreement_7" value="1" {{ old('agreement_7', $agreements['agreement_7'] ?? false) ? 'checked' : '' }} required>I agree to electronic processing and storage of this application.</label>
            </div>
          </div>

          <div class="row">
            <div class="group">
              <label for="signature_typed">Typed Signature</label>
              <input id="signature_typed" name="signature_typed" value="{{ old('signature_typed', $appData['signature']['typed'] ?? '') }}" required>
            </div>

            <div class="group">
              <label for="signature_printed_name">Printed Name</label>
              <input id="signature_printed_name" name="signature_printed_name" value="{{ old('signature_printed_name', $appData['signature']['printed_name'] ?? $candidate->full_name) }}" required>
            </div>
          </div>

          <div class="group">
            <label for="signature_date">Signature Date</label>
            <input id="signature_date" name="signature_date" type="date" value="{{ old('signature_date', $appData['signature']['signed_on'] ?? now()->format('Y-m-d')) }}" required>
          </div>

          <div class="actions">
            <button type="button" id="backBtn" class="btn secondary">Back to Step 1</button>
            <button type="submit" class="btn">Submit Both Forms</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      var step1 = document.getElementById('step1Panel');
      var step2 = document.getElementById('step2Panel');
      var pill1 = document.getElementById('stepPill1');
      var pill2 = document.getElementById('stepPill2');
      var advanceBtn = document.getElementById('advanceBtn');
      var backBtn = document.getElementById('backBtn');

      function setStep(stepNumber) {
        var stepOneActive = stepNumber === 1;
        step1.classList.toggle('hidden', !stepOneActive);
        step2.classList.toggle('hidden', stepOneActive);
        pill1.classList.toggle('active', stepOneActive);
        pill2.classList.toggle('active', !stepOneActive);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      function validateStep1() {
        var fields = [
          document.getElementById('education_level'),
          document.getElementById('years_experience'),
          document.getElementById('availability')
        ];

        for (var i = 0; i < fields.length; i++) {
          if (!fields[i].reportValidity()) {
            return false;
          }
        }

        return true;
      }

      advanceBtn.addEventListener('click', function () {
        if (!validateStep1()) return;
        setStep(2);
      });

      backBtn.addEventListener('click', function () {
        setStep(1);
      });

      var forceStepTwoFromQuery = {{ request('step') == '2' ? 'true' : 'false' }};
      var hasEmploymentOldInput = {{ old('position_applied_for') ? 'true' : 'false' }};
      var hasEmploymentErrors = {{ ($errors->has('position_applied_for') || $errors->has('applicant_full_name') || $errors->has('signature_typed') || $errors->has('agreement_1')) ? 'true' : 'false' }};
      if (forceStepTwoFromQuery || hasEmploymentOldInput || hasEmploymentErrors) {
        setStep(2);
      }
    })();
  </script>
</body>
</html>
