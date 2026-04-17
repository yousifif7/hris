<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Interview Confirmed — {{ $company }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#f0f4ff 0%,#f4f5f7 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px}
:root{--accent:#5ac6cc;--text:#172b4d;--text2:#5e6c84;--text3:#97a0af;--border:#dfe1e6;--surface:#ffffff;--green:#00875a;--green-bg:rgba(0,135,90,.08);--radius:8px;--radius-lg:14px;--shadow:0 4px 24px rgba(0,0,0,.1)}
.card{background:var(--surface);border-radius:var(--radius-lg);box-shadow:var(--shadow);width:100%;max-width:520px;overflow:hidden;text-align:center}
.card-top{background:linear-gradient(135deg,var(--accent),#4fbfc7);padding:36px;color:#fff}
.card-top .check{font-size:48px;margin-bottom:12px}
.card-top h1{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:6px}
.card-top p{opacity:.88;font-size:14px}
.card-body{padding:36px}
.detail-box{background:var(--green-bg);border:1px solid rgba(0,135,90,.2);border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;text-align:left}
.detail-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(0,135,90,.12)}
.detail-row:last-child{border-bottom:none}
.detail-row .lbl{font-size:12px;color:var(--text2);font-weight:600}
.detail-row .val{font-size:14px;font-weight:700;color:var(--text)}
.note{font-size:13px;color:var(--text2);line-height:1.6}
</style>
</head>
<body>
<div class="card">
  <div class="card-top">
    <div class="check">✓</div>
    <h1>You're all set!</h1>
    <p>Your interview has been scheduled, {{ $candidate->first_name }}.</p>
  </div>
  <div class="card-body">
    <div class="detail-box">
      <div class="detail-row">
        <span class="lbl">Date & Time</span>
        <span class="val">{{ $interview->scheduled_at->format('l, F j, Y \a\t g:i A') }}</span>
      </div>
      <div class="detail-row">
        <span class="lbl">Duration</span>
        <span class="val">15–20 minutes</span>
      </div>
      <div class="detail-row">
        <span class="lbl">Format</span>
        <span class="val">Zoom</span>
      </div>
      <div class="detail-row">
        <span class="lbl">Company</span>
        <span class="val">{{ $company }}</span>
      </div>
    </div>
    <p class="note">We'll send you the Zoom meeting link via email before your interview. If you have any questions, please reach out to your HR contact.</p>
    <p style="font-size:11px;color:var(--text3);margin-top:24px">{{ $company }} · Powered by HRPortal</p>
  </div>
</div>
</body>
</html>
