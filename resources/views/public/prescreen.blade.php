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
        Hi {{ $candidate->first_name }}, please complete this form so our HR team can finalize your application review.
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

      <form method="POST" action="{{ route('public.prescreen.submit', ['token' => $token]) }}" enctype="multipart/form-data">
        @csrf

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
          <label for="uploaded_form">Upload Completed PDF Form</label>
          <input id="uploaded_form" name="uploaded_form" type="file" accept="application/pdf" required>
          <div class="hint" style="margin-top:8px;margin-bottom:0">
            Upload your completed PDF. This file is required before you can submit.
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
          <button type="submit" class="btn">Submit Form</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
