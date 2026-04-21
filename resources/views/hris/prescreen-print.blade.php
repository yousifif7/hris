<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Form — {{ $candidate->full_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #111; padding: 32px; }
        .header { border-bottom: 2px solid #111; padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-end; }
        .header h1 { font-size: 20px; }
        .header p { font-size: 12px; color: #555; }
        .section { margin-bottom: 22px; }
        .section-title { font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #ccc; padding-bottom: 4px; margin-bottom: 12px; }
        .field { display: flex; gap: 12px; margin-bottom: 10px; }
        .field label { width: 160px; flex-shrink: 0; font-weight: 600; color: #444; }
        .field span { flex: 1; border-bottom: 1px solid #bbb; padding-bottom: 2px; min-height: 18px; }
        .notes { border: 1px solid #bbb; min-height: 80px; padding: 8px; border-radius: 4px; white-space: pre-wrap; }
        .footer { margin-top: 40px; font-size: 11px; color: #888; text-align: center; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>Post-Interview Application Form</h1>
        <p>{{ \App\Models\Setting::get('company_name', 'McCrory Center') }}</p>
    </div>
    <div style="text-align:right">
        <p style="font-size:11px;color:#888">Printed: {{ now()->format('M d, Y') }}</p>
        <button class="no-print" onclick="window.print()" style="margin-top:6px;padding:6px 14px;font-size:12px;cursor:pointer;background:#1d4ed8;color:#fff;border:none;border-radius:4px">🖨️ Print / Save PDF</button>
    </div>
</div>

<div class="section">
    <div class="section-title">Candidate Information</div>
    <div class="field"><label>Full Name</label><span>{{ $candidate->full_name }}</span></div>
    <div class="field"><label>Email</label><span>{{ $candidate->email }}</span></div>
    <div class="field"><label>Phone</label><span>{{ $candidate->phone ?? '—' }}</span></div>
    <div class="field"><label>Position Applied</label><span>{{ $candidate->category?->name ?? '—' }}</span></div>
    <div class="field"><label>Assigned HR</label><span>{{ $candidate->assignedTo?->full_name ?? '—' }}</span></div>
</div>

@php $ps = $candidate->preScreening; @endphp

<div class="section">
    <div class="section-title">Application Details</div>
    <div class="field"><label>Education Level</label><span>{{ $ps->education_level ?? '—' }}</span></div>
    <div class="field"><label>Years of Experience</label><span>{{ $ps->years_experience ?? '—' }}</span></div>
    <div class="field"><label>Licenses / Certifications</label><span>{{ $ps->licenses ?? '—' }}</span></div>
    <div class="field"><label>Availability</label><span>{{ $ps->availability ?? '—' }}</span></div>
    <div class="field"><label>Earliest Start Date</label><span>{{ $ps->earliest_start_date ? \Carbon\Carbon::parse($ps->earliest_start_date)->format('M d, Y') : '—' }}</span></div>
    <div class="field"><label>Uploaded PDF</label><span>
        @if($ps->uploaded_form_path)
            <a href="{{ asset($ps->uploaded_form_path) }}" target="_blank" rel="noopener">{{ $ps->uploaded_form_name ?? 'Open uploaded PDF' }}</a>
        @else
            —
        @endif
    </span></div>
</div>

@if($ps->additional_notes)
<div class="section">
    <div class="section-title">Additional Notes</div>
    <div class="notes">{{ $ps->additional_notes }}</div>
</div>
@endif

<div class="section" style="margin-top:40px">
    <div class="section-title">Signature</div>
    <div style="display:flex;gap:40px;margin-top:16px">
        <div style="flex:1">
            <div style="border-bottom:1px solid #111;height:40px"></div>
            <div style="font-size:11px;color:#666;margin-top:4px">Candidate Signature</div>
        </div>
        <div style="flex:1">
            <div style="border-bottom:1px solid #111;height:40px"></div>
            <div style="font-size:11px;color:#666;margin-top:4px">Date</div>
        </div>
        <div style="flex:1">
            <div style="border-bottom:1px solid #111;height:40px"></div>
            <div style="font-size:11px;color:#666;margin-top:4px">HR Representative</div>
        </div>
    </div>
</div>

<div class="footer">
    {{ \App\Models\Setting::get('company_name', 'McCrory Center') }} · Confidential · Generated {{ now()->format('M d, Y g:i A') }}
</div>

</body>
</html>
