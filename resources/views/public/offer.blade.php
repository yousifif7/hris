<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Offer Letter — {{ $company }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:14px}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#f0f4ff 0%,#f4f5f7 100%);min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:40px 16px}
input,select,textarea,button{font-family:inherit;font-size:inherit}
button{cursor:pointer;border:none}
:root{
  --accent:#5ac6cc;--accent2:#4fbfc7;
  --text:#172b4d;--text2:#5e6c84;--text3:#97a0af;
  --border:#dfe1e6;--surface:#ffffff;--surface2:#f4f5f7;
  --green:#00875a;--green-bg:rgba(0,135,90,.08);
  --red:#de350b;--red-bg:rgba(222,53,11,.07);
  --radius:8px;--radius-lg:14px;
  --shadow:0 4px 24px rgba(0,0,0,.1);
}
.card{background:var(--surface);border-radius:var(--radius-lg);box-shadow:var(--shadow);width:100%;max-width:680px;overflow:hidden}
.card-top{background:linear-gradient(135deg,var(--accent),var(--accent2));padding:32px 36px;color:#fff}
.card-top h1{font-family:'Playfair Display',serif;font-size:26px;margin-bottom:6px}
.card-top p{opacity:.88;font-size:14px}
.card-body{padding:36px}
.section{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:20px}
.section h3{font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:var(--text3);font-weight:600;margin-bottom:14px}
.row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)}
.row:last-child{border-bottom:none}
.row .lbl{font-size:13px;color:var(--text2)}
.row .val{font-size:14px;font-weight:600;color:var(--text)}
.status-badge{display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.status-sent{background:rgba(90,198,204,.12);color:var(--accent)}
.status-viewed{background:rgba(0,101,255,.08);color:#0065ff}
.status-accepted{background:var(--green-bg);color:var(--green)}
.status-declined{background:var(--red-bg);color:var(--red)}
.already-responded{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:24px;text-align:center}
.already-responded h3{font-size:16px;font-weight:700;margin-bottom:8px}
.already-responded p{font-size:13px;color:var(--text2)}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px}
.form-group input{width:100%;background:var(--surface);border:1px solid var(--border);color:var(--text);padding:10px 14px;border-radius:var(--radius);outline:none;font-size:14px;transition:border .2s}
.form-group input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(90,198,204,.1)}
.action-buttons{display:flex;gap:12px;margin-top:8px}
.btn-accept{flex:1;padding:14px;border-radius:var(--radius);font-weight:700;font-size:14px;background:var(--green);color:#fff;transition:all .15s;cursor:pointer;border:none}
.btn-accept:hover{background:#007a52;transform:translateY(-1px);box-shadow:0 4px 12px rgba(0,135,90,.3)}
.btn-decline{flex:1;padding:14px;border-radius:var(--radius);font-weight:700;font-size:14px;background:var(--red-bg);color:var(--red);border:1px solid rgba(222,53,11,.2);transition:all .15s;cursor:pointer}
.btn-decline:hover{background:rgba(222,53,11,.12)}
.required-docs ul{padding-left:20px;display:flex;flex-direction:column;gap:6px;margin-top:6px}
.required-docs ul li{font-size:13px;color:var(--text2)}
.sig-pad{border:1.5px solid var(--border);border-radius:var(--radius);padding:12px 14px;width:100%;font-size:14px;font-style:italic;color:var(--text)}
.disclaimer{font-size:11px;color:var(--text3);line-height:1.5;margin-top:20px;padding-top:14px;border-top:1px solid var(--border);text-align:center}
.expired-notice{background:rgba(222,53,11,.06);border:1px solid rgba(222,53,11,.2);border-radius:var(--radius);padding:16px 20px;color:var(--red);font-size:13px;font-weight:500}
</style>
</head>
<body>

<div class="card">
  <div class="card-top">
    <p style="font-size:12px;opacity:.7;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px">Official Offer Letter</p>
    <h1>{{ $company }}</h1>
    <p>Congratulations, {{ $candidate->first_name }}! We'd love to have you join our team.</p>
    <div style="margin-top:12px">
      @php
        $statusLabels = ['sent'=>'Pending Your Response','viewed'=>'Opened','accepted'=>'Accepted','declined'=>'Declined','expired'=>'Expired'];
        $statusCls    = ['sent'=>'status-sent','viewed'=>'status-viewed','accepted'=>'status-accepted','declined'=>'status-declined','expired'=>'status-declined'];
      @endphp
      <span class="status-badge {{ $statusCls[$offer->status] ?? 'status-sent' }}">
        {{ $statusLabels[$offer->status] ?? ucfirst($offer->status) }}
      </span>
    </div>
  </div>

  <div class="card-body">

    {{-- Offer details --}}
    <div class="section">
      <h3>Offer Details</h3>
      <div class="row">
        <span class="lbl">Position</span>
        <span class="val">{{ $candidate->category?->name ?? 'Staff' }}</span>
      </div>
      <div class="row">
        <span class="lbl">Employment Type</span>
        <span class="val">{{ $offer->employment_type }}</span>
      </div>
      @if($offer->location)
      <div class="row">
        <span class="lbl">Location</span>
        <span class="val">{{ $offer->location }}</span>
      </div>
      @endif
      <div class="row">
        <span class="lbl">Compensation</span>
        <span class="val">${{ number_format($offer->pay_rate, 2) }} / {{ $offer->pay_type }}</span>
      </div>
      @if($offer->start_date)
      <div class="row">
        <span class="lbl">Start Date</span>
        <span class="val">{{ $offer->start_date->format('F j, Y') }}</span>
      </div>
      @endif
      @if($offer->orientation_date)
      <div class="row">
        <span class="lbl">Orientation Date</span>
        <span class="val">{{ $offer->orientation_date->format('F j, Y') }}</span>
      </div>
      @endif
      @if($offer->deadline_days)
      <div class="row">
        <span class="lbl">Response Deadline</span>
        <span class="val">{{ $offer->sent_at?->addDays($offer->deadline_days)->format('F j, Y') ?? 'N/A' }}</span>
      </div>
      @endif
    </div>

    {{-- Required documents --}}
    @if($offer->required_documents)
    <div class="section required-docs">
      <h3>Required Documents</h3>
      <p style="font-size:13px;color:var(--text2);margin-bottom:8px">Please have the following documents ready before or on your first day:</p>
      <ul>
        @foreach(explode("\n", $offer->required_documents) as $doc)
          @if(trim($doc))
          <li>{{ trim($doc) }}</li>
          @endif
        @endforeach
      </ul>
    </div>
    @endif

    {{-- Already responded --}}
    @if(in_array($offer->status, ['accepted','declined','expired']))
    <div class="already-responded">
      @if($offer->status === 'accepted')
        <h3 style="color:var(--green)">✅ You accepted this offer</h3>
        <p>Our HR team will be in touch with onboarding details. We're excited to have you on board!</p>
        @if($offer->responded_at)
          <p style="margin-top:6px;font-size:12px;color:var(--text3)">Responded on {{ $offer->responded_at->format('F j, Y') }}</p>
        @endif
      @elseif($offer->status === 'declined')
        <h3 style="color:var(--red)">❌ You declined this offer</h3>
        <p>Thank you for your consideration. We wish you the best in your future endeavors.</p>
      @else
        <div class="expired-notice">⏰ This offer has expired. Please contact HR to discuss further options.</div>
      @endif
    </div>

    {{-- Pending response --}}
    @else
      @if($offer->isExpired())
        <div class="expired-notice">⏰ This offer has expired. Please contact HR to discuss further options.</div>
      @else
        <form method="POST" action="{{ route('public.offer.respond', $offer->token) }}" id="offerForm">
          @csrf
          <div id="responseSection">
            <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;color:var(--text)">Your Response</h3>

            <div class="form-group">
              <label>Digital Signature (type your full name to sign)</label>
              <input type="text" name="signature" id="sigInput" placeholder="{{ $candidate->full_name }}" class="sig-pad" autocomplete="off">
            </div>

            <div id="acceptFields" style="display:none">
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="form-group">
                  <label>Confirm Start Date (optional)</label>
                  <input type="date" name="start_date" id="startDateInput" value="{{ $offer->start_date?->format('Y-m-d') }}">
                </div>
                <div class="form-group">
                  <label>Confirm Orientation Date (optional)</label>
                  <input type="date" name="orientation_date" id="orientDateInput" value="{{ $offer->orientation_date?->format('Y-m-d') }}">
                </div>
              </div>
            </div>

            <input type="hidden" name="response" id="responseInput" value="">

            <div class="action-buttons">
              <button type="button" class="btn-accept" onclick="submitResponse('accepted')">
                ✅ Accept Offer
              </button>
              <button type="button" class="btn-decline" onclick="submitResponse('declined')">
                ✗ Decline
              </button>
            </div>
          </div>
        </form>
      @endif
    @endif

    <div class="disclaimer">
      This offer letter is confidential and intended solely for {{ $candidate->full_name }}.
      By accepting this offer, you agree to the terms stated above.
      Questions? Contact HR at <a href="mailto:hr@mccrorycenter.com" style="color:var(--accent)">hr@mccrorycenter.com</a>.
    </div>

  </div>
</div>

<script>
function submitResponse(response) {
    var sig = document.getElementById('sigInput').value.trim();
    if (!sig) {
        alert('Please type your full name as your digital signature before responding.');
        document.getElementById('sigInput').focus();
        return;
    }

    if (response === 'declined') {
        if (!confirm('Are you sure you want to decline this offer?')) return;
    }

    document.getElementById('responseInput').value = response;

    if (response === 'accepted') {
        document.getElementById('acceptFields').style.display = 'block';
    }

    document.getElementById('offerForm').submit();
}
</script>
</body>
</html>
