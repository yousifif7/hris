<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>McCrory Center — Sign In</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
    <link rel="shortcut icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
    <link rel="apple-touch-icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png">
    
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--accent:#5ac6cc;--accent2:#4fbfc7;--bg:#f4f5f7;--surface:#fff;--border:#dfe1e6;--text:#172b4d;--text2:#5e6c84;--text3:#97a0af;--red:#de350b;--radius:8px;--font:'DM Sans',sans-serif;--font-display:'Playfair Display',serif}
body{font-family:var(--font);background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:var(--surface);border:1px solid var(--border);border-radius:14px;padding:40px;width:100%;max-width:400px;box-shadow:0 4px 24px rgba(0,0,0,.08)}
.logo{display:flex;align-items:center;gap:10px;margin-bottom:28px;justify-content:center}
.logo .icon{width:42px;height:42px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:12px;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:18px}
.logo h1{font-family:var(--font-display);font-size:22px;letter-spacing:-.5px;color:var(--text)}
h2{font-size:18px;font-weight:700;margin-bottom:6px;color:var(--text)}
.sub{font-size:13px;color:var(--text3);margin-bottom:28px}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px}
.form-group input{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:var(--radius);font-size:14px;font-family:var(--font);outline:none;transition:border .2s;color:var(--text);background:var(--surface)}
.form-group input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(90,198,204,.12)}
.btn{width:100%;padding:11px;background:var(--accent);color:#fff;border:none;border-radius:var(--radius);font-weight:600;font-size:14px;font-family:var(--font);cursor:pointer;transition:all .15s;margin-top:4px}
.btn:hover{background:var(--accent2);transform:translateY(-1px);box-shadow:0 2px 8px rgba(90,198,204,.25)}
.btn:disabled{opacity:.6;transform:none;cursor:default}
.error{background:rgba(222,53,11,.07);border:1px solid rgba(222,53,11,.2);color:var(--red);padding:10px 14px;border-radius:var(--radius);font-size:13px;margin-bottom:16px;display:none}
.hint{font-size:11px;color:var(--text3);text-align:center;margin-top:16px}
</style>
</head>
<body>
<div class="card">
    <div class="logo">
        <a href="{{ url('/') }}">
            <img src="https://login.mccrorycenter.com/?entryPoint=LogoImage&id=68136eae0e1afd0b7" alt="McCrory Center logo" style="max-width:220px;height:auto;display:block">
        </a>
    </div>
    <h2>Sign in to your account</h2>
    <p class="sub">Enter your HR portal credentials below.</p>
    <div class="error" id="errMsg"></div>
    <div class="form-group"><label>Email address</label><input type="email" id="email" placeholder="admin@hris.com" autocomplete="email"></div>
    <div class="form-group"><label>Password</label><input type="password" id="password" placeholder="••••••••" autocomplete="current-password"></div>
    <button class="btn" id="loginBtn" onclick="doLogin()">Sign In</button>
</div>
<script>
document.getElementById('password').addEventListener('keydown', function(e){ if(e.key==='Enter') doLogin(); });
document.getElementById('email').addEventListener('keydown', function(e){ if(e.key==='Enter') doLogin(); });

async function doLogin() {
    var btn = document.getElementById('loginBtn');
    var err = document.getElementById('errMsg');
    var email = document.getElementById('email').value.trim();
    var pass  = document.getElementById('password').value;
    err.style.display = 'none';
    if (!email || !pass) { showErr('Please enter your email and password.'); return; }
    btn.disabled = true; btn.textContent = 'Signing in…';
    try {
        var r = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email: email, password: pass })
        });
        var data = await r.json();
        if (!r.ok) { showErr(data.message || 'Invalid credentials.'); btn.disabled=false; btn.textContent='Sign In'; return; }
        localStorage.setItem('hris_token', data.token);
        window.location.href = data.redirect || '/hris';
    } catch(e) { showErr('Network error. Please try again.'); btn.disabled=false; btn.textContent='Sign In'; }
}
function showErr(m){ var e=document.getElementById('errMsg'); e.textContent=m; e.style.display='block'; }
</script>
</body>
</html>
