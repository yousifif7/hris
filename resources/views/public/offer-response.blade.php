<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $accepted ? 'Offer Accepted' : 'Offer Declined' }} — {{ $company }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#f0f4ff,#f4f5f7);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 16px}
.card{background:#fff;border-radius:16px;box-shadow:0 8px 40px rgba(0,0,0,.1);max-width:520px;width:100%;padding:48px 40px;text-align:center}
.icon{font-size:64px;margin-bottom:20px}
h1{font-family:'Playfair Display',serif;font-size:26px;margin-bottom:12px;color:#172b4d}
p{font-size:14px;color:#5e6c84;line-height:1.6;margin-bottom:10px}
.company{font-size:13px;color:#97a0af;margin-top:20px}
</style>
</head>
<body>
<div class="card">
  @if($accepted)
    <div class="icon">🎉</div>
    <h1>Welcome to {{ $company }}!</h1>
    <p>Thank you for accepting the offer, <strong>{{ $candidate->first_name }}</strong>!</p>
    <p>Our HR team has been notified and will reach out shortly with onboarding information and next steps.</p>
    <p style="margin-top:16px;font-size:13px;color:#97a0af">We're excited to have you join us. See you soon!</p>
  @else
    <div class="icon">👋</div>
    <h1>Thank You, {{ $candidate->first_name }}</h1>
    <p>We appreciate your consideration and wish you the very best in your career journey.</p>
    <p>If you have any questions or your circumstances change, please don't hesitate to reach out.</p>
  @endif
  <div class="company">— {{ $company }} HR Team</div>
</div>
</body>
</html>
