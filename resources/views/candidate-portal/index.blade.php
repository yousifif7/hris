<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Candidate Portal — McCrory Center</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
  <style>
    :root {
      --bg: #f4f5f7;
      --surface: #ffffff;
      --surface2: #f8f9fb;
      --border: #dfe1e6;
      --text: #172b4d;
      --text2: #5e6c84;
      --text3: #97a0af;
      --accent: #5ac6cc;
      --accent2: #4fbfc7;
      --accent-glow: rgba(90,198,204,.1);
      --green: #00875a;
      --green-bg: rgba(0,135,90,.08);
      --red: #de350b;
      --red-bg: rgba(222,53,11,.07);
      --radius: 8px;
      --radius-lg: 14px;
      --shadow: 0 2px 8px rgba(0,0,0,.06);
      --font: 'DM Sans', sans-serif;
      --font-display: 'Playfair Display', serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; padding: 32px 16px 80px; }
    input, select, textarea, button { font-family: inherit; font-size: inherit; }
    button { cursor: pointer; border: none; background: none; color: inherit; }
    input, select, textarea {
      background: var(--surface); border: 1.5px solid var(--border); color: var(--text);
      padding: 10px 14px; border-radius: var(--radius); outline: none;
      transition: border .2s, box-shadow .2s; width: 100%; font-size: 14px;
    }
    input:focus, textarea:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
    input:disabled, textarea:disabled, input[readonly] { background: var(--surface2); color: var(--text2); cursor: not-allowed; }

    .page-wrap { max-width: 920px; margin: 0 auto; }
    .topbar { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; }
    .brand-icon {
      width: 44px; height: 44px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-radius: 12px; display: flex; align-items: center; justify-content: center;
      font-weight: 700; color: #fff; font-size: 19px;
    }
    .brand h1 { font-family: var(--font-display); font-size: 22px; }
    .brand p  { font-size: 13px; color: var(--text2); margin-top: 1px; }
    .topbar .right { margin-left: auto; display: flex; gap: 8px; align-items: center; }
    .btn { padding: 8px 14px; background: var(--accent); color: #fff; border-radius: var(--radius); font-weight: 600; font-size: 13px; transition: background .15s, transform .1s; }
    .btn:hover { background: var(--accent2); transform: translateY(-1px); }
    .btn.secondary { background: var(--surface); color: var(--text); border: 1.5px solid var(--border); }
    .btn.secondary:hover { background: var(--surface2); border-color: var(--text3); transform: none; }
    .btn.ghost { background: transparent; color: var(--text2); border: none; padding: 6px 10px; }
    .btn.ghost:hover { color: var(--text); }
    .btn.full { width: 100%; padding: 11px 14px; font-size: 14px; }
    .btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }

    .hero {
      background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg);
      padding: 24px 28px; margin-bottom: 24px; box-shadow: var(--shadow);
      display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center;
    }
    .hero h2 { font-family: var(--font-display); font-size: 22px; margin-bottom: 6px; }
    .hero p  { font-size: 13px; color: var(--text2); line-height: 1.55; max-width: 540px; }
    .hero .status-chip {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--accent-glow); color: var(--accent2);
      padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
      margin-top: 10px;
    }
    .hero .status-chip::before { content:''; width:8px; height:8px; border-radius:50%; background: var(--accent2); }

    .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px 28px; margin-bottom: 20px; box-shadow: var(--shadow); }
    .card h3 { font-family: var(--font-display); font-size: 17px; margin-bottom: 4px; }
    .card .card-sub { font-size: 13px; color: var(--text2); margin-bottom: 18px; line-height: 1.55; }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px 18px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px 18px; }
    @media (max-width: 720px) { .grid-2, .grid-3 { grid-template-columns: 1fr; } }
    .field { display: flex; flex-direction: column; gap: 5px; }
    .field label { font-size: 12px; font-weight: 600; color: var(--text2); }
    .field .hint { font-size: 11px; color: var(--text3); }
    .field .ro { padding: 9px 12px; background: var(--surface2); border: 1.5px solid var(--border); border-radius: var(--radius); color: var(--text2); min-height: 40px; display: flex; align-items: center; font-size: 13px; }
    .field .ro.empty { color: var(--text3); font-style: italic; }

    .doc-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 8px; }
    .doc-row .doc-label { font-size: 13px; font-weight: 600; color: var(--text); flex: 1; }
    .doc-row .doc-name { font-size: 12px; color: var(--text2); margin-right: 8px; }
    .doc-row .doc-name a { color: var(--accent); text-decoration: none; }

    .offer-block { background: linear-gradient(135deg, rgba(90,198,204,.06), rgba(90,198,204,.02)); border: 1px solid rgba(90,198,204,.25); border-radius: var(--radius); padding: 16px 18px; margin-top: 10px; }
    .offer-block .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; color: var(--text); }
    .offer-block .row .lbl { color: var(--text2); }

    .checkbox-row { display: flex; align-items: center; gap: 10px; padding: 12px 14px; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--radius); }
    .checkbox-row input[type=checkbox] { width: auto; }
    .checkbox-row label { font-size: 13px; color: var(--text); cursor: pointer; }

    .toast { position: fixed; top: 20px; right: 20px; padding: 12px 18px; border-radius: var(--radius); font-size: 13px; font-weight: 600; box-shadow: var(--shadow); z-index: 100; opacity: 0; transform: translateY(-8px); transition: opacity .2s, transform .2s; }
    .toast.show { opacity: 1; transform: translateY(0); }
    .toast.ok { background: var(--green-bg); color: var(--green); border: 1px solid rgba(0,135,90,.2); }
    .toast.err { background: var(--red-bg); color: var(--red); border: 1px solid rgba(222,53,11,.2); }

    .loading-state { text-align: center; padding: 80px 20px; color: var(--text3); }
  </style>
</head>
<body>

<div class="page-wrap">
  <div class="topbar">
    <div class="brand-icon">M</div>
    <div class="brand">
      <h1>Candidate Portal</h1>
      <p id="topGreeting">Welcome back</p>
    </div>
    <div class="right">
      <button class="btn ghost" onclick="showPasswordModal()">Change Password</button>
      <button class="btn secondary" onclick="logout()">Sign out</button>
    </div>
  </div>

  <div id="content">
    <div class="loading-state">Loading your portal…</div>
  </div>
</div>

<!-- Change password modal -->
<div id="pwModal" style="display:none;position:fixed;inset:0;background:rgba(23,43,77,.45);z-index:90;align-items:center;justify-content:center">
  <div style="background:var(--surface);border-radius:var(--radius-lg);padding:28px;max-width:420px;width:100%;margin:16px">
    <h3 style="font-family:var(--font-display);font-size:18px;margin-bottom:14px">Change Your Password</h3>
    <div class="field" style="margin-bottom:12px"><label>Current Password</label><input id="pwCur" type="password" autocomplete="current-password"></div>
    <div class="field" style="margin-bottom:12px"><label>New Password</label><input id="pwNew" type="password" autocomplete="new-password"></div>
    <div class="field" style="margin-bottom:18px"><label>Confirm New Password</label><input id="pwConf" type="password" autocomplete="new-password"></div>
    <div style="display:flex;gap:10px">
      <button class="btn secondary" style="flex:1" onclick="hidePasswordModal()">Cancel</button>
      <button class="btn" style="flex:1" onclick="submitPassword()">Update</button>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>

<script>
const TOKEN = localStorage.getItem('hris_token');
if (!TOKEN) { window.location.href = '/login'; }

let me = null;        // { candidate, editable_fields, document_fields, read_only }
let dirty = {};       // staged changes for the editable fields

async function apiFetch(url, opts = {}) {
  opts.headers = Object.assign({
    'Accept': 'application/json',
    'Authorization': 'Bearer ' + TOKEN,
  }, opts.headers || {});
  if (opts.body && !(opts.body instanceof FormData) && !opts.headers['Content-Type']) {
    opts.headers['Content-Type'] = 'application/json';
  }
  const r = await fetch(url, opts);
  if (r.status === 401) { localStorage.removeItem('hris_token'); window.location.href = '/login'; return null; }
  return r;
}

function esc(s) { return (s ?? '').toString().replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }
function fmtMoney(v) { if (v === null || v === undefined || v === '') return '—'; return '$' + Number(v).toLocaleString(undefined, {minimumFractionDigits: 2}); }
function fmtDate(v) { if (!v) return '—'; const d = new Date(v); if (isNaN(d)) return v; return d.toLocaleDateString(undefined, {month:'short', day:'numeric', year:'numeric'}); }
function toast(msg, kind = 'ok') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show ' + kind;
  setTimeout(() => t.classList.remove('show'), 2400);
}

async function logout() {
  await apiFetch('/api/logout', { method: 'POST' }).catch(() => {});
  localStorage.removeItem('hris_token');
  window.location.href = '/login';
}

function showPasswordModal() { document.getElementById('pwModal').style.display = 'flex'; }
function hidePasswordModal() {
  document.getElementById('pwModal').style.display = 'none';
  ['pwCur','pwNew','pwConf'].forEach(id => document.getElementById(id).value = '');
}
async function submitPassword() {
  const cur = document.getElementById('pwCur').value;
  const np  = document.getElementById('pwNew').value;
  const nc  = document.getElementById('pwConf').value;
  if (!cur || !np) { toast('Fill both fields.', 'err'); return; }
  if (np !== nc) { toast('Passwords do not match.', 'err'); return; }
  const r = await apiFetch('/api/candidate-portal/change-password', {
    method: 'POST',
    body: JSON.stringify({ current_password: cur, new_password: np, new_password_confirmation: nc }),
  });
  const data = await r.json();
  if (!r.ok) { toast(data.message || 'Could not update password.', 'err'); return; }
  hidePasswordModal();
  toast('Password updated.');
}

async function loadMe() {
  const r = await apiFetch('/api/candidate-portal/me');
  if (!r || !r.ok) {
    document.getElementById('content').innerHTML = '<div class="card"><h3>Could not load your portal</h3><p>Please sign out and back in. If the problem persists, contact your HR team.</p></div>';
    return;
  }
  me = await r.json();
  document.getElementById('topGreeting').textContent = 'Welcome back, ' + (me.candidate.first_name || '');
  render();
}

function render() {
  const c = me.candidate;
  const ro = me.read_only;
  const html = `
    <div class="hero">
      <div>
        <h2>Hi ${esc(c.first_name)}!</h2>
        <p>This is your candidate portal. Use it to keep your personal details current and to upload the onboarding paperwork your HR team has asked for. Your status, supervisors, pay, and assigned HR contact are managed by the team — if anything looks wrong, reply to your HR contact.</p>
        <span class="status-chip">${esc(ro.status_label || 'In progress')}</span>
      </div>
    </div>

    ${renderOfferCard(c, ro)}

    <div class="card">
      <h3>Personal Information</h3>
      <p class="card-sub">These details are yours — keep them current.</p>
      <div class="grid-2">
        ${editField('first_name', 'First Name', c.first_name)}
        ${editField('last_name',  'Last Name',  c.last_name)}
        ${editField('phone', 'Phone', c.phone, 'tel')}
        ${readOnlyField('Email',  c.email || '—', 'Your email is your login — contact HR to change it.')}
      </div>
      <div style="margin-top:14px">${editField('street_address', 'Street Address', c.street_address)}</div>
      <div class="grid-3" style="margin-top:14px">
        ${editField('city',  'City',  c.city)}
        ${editField('state', 'State', c.state)}
        ${editField('postal_code', 'Postal Code', c.postal_code)}
      </div>
    </div>

    <div class="card">
      <h3>Emergency Contacts</h3>
      <p class="card-sub">Two people we can reach if there's an emergency at work.</p>
      <div class="grid-2">
        ${editField('emergency_contact_1_name',  'Contact #1 — Name',  c.emergency_contact_1_name)}
        ${editField('emergency_contact_1_phone', 'Contact #1 — Phone', c.emergency_contact_1_phone, 'tel')}
        ${editField('emergency_contact_2_name',  'Contact #2 — Name',  c.emergency_contact_2_name)}
        ${editField('emergency_contact_2_phone', 'Contact #2 — Phone', c.emergency_contact_2_phone, 'tel')}
      </div>
    </div>

    <div class="card">
      <h3>References</h3>
      <p class="card-sub">People who can speak to your experience.</p>
      <div class="grid-3">
        ${editField('reference_1_name',        'Reference #1 — Name',        c.reference_1_name)}
        ${editField('reference_1_phone',       'Reference #1 — Phone',       c.reference_1_phone, 'tel')}
        ${editField('reference_1_association', 'Reference #1 — Association', c.reference_1_association)}
        ${editField('reference_2_name',        'Reference #2 — Name',        c.reference_2_name)}
        ${editField('reference_2_phone',       'Reference #2 — Phone',       c.reference_2_phone, 'tel')}
        ${editField('reference_2_association', 'Reference #2 — Association', c.reference_2_association)}
      </div>
    </div>

    <div class="card">
      <h3>Documents</h3>
      <p class="card-sub">Upload the items HR has asked for. Existing files can be replaced by uploading again.</p>
      ${me.document_fields.map(f => renderDocRow(f, c)).join('')}
    </div>

    <div class="card">
      <h3>Acknowledgements</h3>
      <p class="card-sub">Read and confirm the items below.</p>
      <div class="checkbox-row">
        <input id="ack_handbook" type="checkbox" ${c.acknowledgement_handbook ? 'checked' : ''} onchange="stage('acknowledgement_handbook', this.checked)">
        <label for="ack_handbook">I acknowledge that I have reviewed the Employee Handbook and Policies.</label>
      </div>
    </div>

    <div class="card">
      <h3>Your HR Contact</h3>
      <p class="card-sub">Set by your HR team — read-only.</p>
      <div class="grid-2">
        ${readOnlyField('Assigned To',         ro.assigned_to || '—')}
        ${readOnlyField('Operations Manager',  ro.operations_manager || '—')}
        ${readOnlyField('Clinical Supervisor', ro.clinical_supervisor || '—')}
        ${readOnlyField('Company Rep',         ro.company_representative || '—')}
      </div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end;padding:0 4px 40px">
      <button class="btn secondary" onclick="loadMe()">Discard changes</button>
      <button class="btn" id="saveBtn" onclick="saveAll()" disabled>Save changes</button>
    </div>
  `;
  document.getElementById('content').innerHTML = html;
  dirty = {};
  updateSaveBtn();
}

function renderOfferCard(c, ro) {
  const o = ro.offer || {};
  const hasOffer = o.amount || o.start_date;
  if (!hasOffer) return '';
  return `
    <div class="card">
      <h3>Your Offer</h3>
      <p class="card-sub">Details from your offer letter — read-only.</p>
      <div class="offer-block">
        <div class="row"><span class="lbl">Amount</span><span>${fmtMoney(o.amount)}</span></div>
        <div class="row"><span class="lbl">Payment Frequency</span><span>${esc(o.frequency || '—')}</span></div>
        <div class="row"><span class="lbl">Anticipated Start Date</span><span>${fmtDate(o.start_date)}</span></div>
        <div class="row"><span class="lbl">Deadline For Acceptance</span><span>${fmtDate(o.deadline_date)}</span></div>
      </div>
    </div>
  `;
}

function editField(name, label, value, type = 'text') {
  return `
    <div class="field">
      <label for="f_${name}">${esc(label)}</label>
      <input id="f_${name}" type="${type}" value="${esc(value ?? '')}" oninput="stage('${name}', this.value)">
    </div>
  `;
}

function readOnlyField(label, value, hint = '') {
  const isEmpty = !value || value === '—';
  return `
    <div class="field">
      <label>${esc(label)}</label>
      <div class="ro ${isEmpty ? 'empty' : ''}">${esc(value)}</div>
      ${hint ? `<span class="hint">${esc(hint)}</span>` : ''}
    </div>
  `;
}

function renderDocRow(field, c) {
  const name = c[field + '_name'];
  const path = c[field + '_path'] || (typeof c[field] === 'string' && c[field].startsWith('candidate-documents/') ? c[field] : null);
  const url  = path ? ('/storage/' + path) : null;
  const label = humanize(field);
  return `
    <div class="doc-row">
      <div class="doc-label">${esc(label)}</div>
      ${name && url ? `<span class="doc-name">📎 <a href="${esc(url)}" target="_blank">${esc(name)}</a></span>` : '<span class="doc-name">No file yet</span>'}
      <input type="file" id="doc_${field}" style="display:none" onchange="uploadDoc('${field}', this)">
      <button class="btn secondary" onclick="document.getElementById('doc_${field}').click()">${name ? 'Replace' : 'Upload'}</button>
    </div>
  `;
}

function humanize(s) {
  return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
          .replace(/\bI9\b/i, 'I-9').replace(/\bDwihn\b/i, 'DWIHN').replace(/\bCpr\b/i, 'CPR').replace(/\bTb\b/i, 'TB').replace(/\bCeus\b/i, 'CEUs');
}

function stage(field, value) {
  dirty[field] = value;
  updateSaveBtn();
}
function updateSaveBtn() {
  const btn = document.getElementById('saveBtn');
  if (btn) btn.disabled = Object.keys(dirty).length === 0;
}

async function saveAll() {
  if (Object.keys(dirty).length === 0) return;
  const btn = document.getElementById('saveBtn');
  btn.disabled = true; btn.textContent = 'Saving…';
  const r = await apiFetch('/api/candidate-portal/me', { method: 'PUT', body: JSON.stringify(dirty) });
  const data = await r.json();
  btn.textContent = 'Save changes';
  if (!r.ok) {
    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Could not save.');
    toast(msg, 'err');
    btn.disabled = false;
    return;
  }
  toast('Saved.');
  me.candidate = data;
  dirty = {};
  updateSaveBtn();
}

async function uploadDoc(field, input) {
  const file = input.files[0];
  if (!file) return;
  const fd = new FormData();
  fd.append('field', field);
  fd.append('file', file);
  const r = await apiFetch('/api/candidate-portal/upload', { method: 'POST', body: fd });
  const data = await r.json();
  if (!r.ok) {
    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Upload failed.');
    toast(msg, 'err');
    input.value = '';
    return;
  }
  toast('Uploaded ' + file.name + '.');
  // Refresh to reflect the new file
  loadMe();
}

loadMe();
</script>

</body>
</html>
