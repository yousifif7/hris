<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply — McCrory Center</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
  <style>
    :root {
      --bg: #f4f5f7;
      --surface: #ffffff;
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
      --shadow: 0 2px 8px rgba(0,0,0,.08), 0 8px 32px rgba(0,0,0,.06);
      --font: 'DM Sans', sans-serif;
      --font-display: 'Playfair Display', serif;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { font-size: 15px; }
    body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 40px 16px 80px; }
    input, select, textarea, button { font-family: inherit; font-size: inherit; }
    button { cursor: pointer; border: none; background: none; color: inherit; }
    input, select, textarea {
      background: var(--surface); border: 1.5px solid var(--border); color: var(--text);
      padding: 10px 14px; border-radius: var(--radius); outline: none;
      transition: border .2s, box-shadow .2s; width: 100%;
    }
    input:focus, select:focus, textarea:focus {
      border-color: var(--accent);
      box-shadow: 0 0 0 3px var(--accent-glow);
    }
    textarea { resize: vertical; min-height: 120px; }
    select {
      -webkit-appearance: none; appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%235e6c84' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center; padding-right: 34px;
    }

    /* Layout */
    .page-wrap { width: 100%; max-width: 680px; }
    .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
    .brand-icon {
      width: 46px; height: 46px;
      background: linear-gradient(135deg, var(--accent), var(--accent2));
      border-radius: 13px; display: flex; align-items: center; justify-content: center;
      font-weight: 700; color: #fff; font-size: 20px; flex-shrink: 0;
    }
    .brand h1 { font-family: var(--font-display); font-size: 22px; color: var(--text); }
    .brand p { font-size: 13px; color: var(--text2); margin-top: 1px; }

    .card {
      background: var(--surface); border: 1px solid var(--border);
      border-radius: var(--radius-lg); padding: 36px 40px;
      box-shadow: var(--shadow);
    }
    .card-title { font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
    .card-sub { font-size: 13px; color: var(--text2); margin-bottom: 28px; line-height: 1.6; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .form-group:last-child { margin-bottom: 0; }
    label { font-size: 13px; font-weight: 600; color: var(--text2); }
    label .req { color: var(--accent); margin-left: 2px; }

    /* Upload zone */
    .upload-zone {
      border: 2px dashed var(--border); border-radius: var(--radius);
      padding: 28px 20px; text-align: center; cursor: pointer;
      transition: border-color .2s, background .2s; margin-bottom: 16px;
    }
    .upload-zone:hover, .upload-zone.drag-over { border-color: var(--accent); background: var(--accent-glow); }
    .upload-zone .uz-icon { font-size: 28px; margin-bottom: 8px; }
    .upload-zone h4 { font-size: 14px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
    .upload-zone p { font-size: 12px; color: var(--text3); }
    .file-list { margin-top: 8px; }
    .file-item {
      display: flex; align-items: center; gap: 8px; padding: 6px 10px;
      background: var(--bg); border-radius: var(--radius); font-size: 12px;
      color: var(--text2); margin-bottom: 4px;
    }
    .file-item .file-name { flex: 1; }
    .file-item .file-remove { cursor: pointer; color: var(--text3); font-size: 14px; line-height: 1; }
    .file-item .file-remove:hover { color: var(--red); }

    /* Submit */
    .btn-submit {
      width: 100%; padding: 13px 20px; background: var(--accent); color: #fff;
      border-radius: var(--radius); font-weight: 700; font-size: 15px;
      transition: background .2s, transform .15s, box-shadow .15s; margin-top: 24px;
    }
    .btn-submit:hover:not(:disabled) { background: var(--accent2); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(90,198,204,.35); }
    .btn-submit:disabled { opacity: .6; cursor: not-allowed; }
    .btn-submit .spinner { display: none; }
    .btn-submit.loading .btn-text { display: none; }
    .btn-submit.loading .spinner { display: inline; }

    /* Divider */
    .divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
    .divider-line { flex: 1; height: 1px; background: var(--border); }
    .divider-text { font-size: 12px; color: var(--text3); font-weight: 500; }

    /* Alert */
    .alert {
      padding: 12px 16px; border-radius: var(--radius);
      font-size: 13px; margin-bottom: 16px; display: none;
    }
    .alert.show { display: flex; align-items: flex-start; gap: 10px; }
    .alert-error { background: var(--red-bg); color: var(--red); border: 1px solid rgba(222,53,11,.15); }
    .alert-error strong { font-weight: 700; }

    /* Success screen */
    .success-screen {
      display: none; text-align: center; padding: 48px 20px;
    }
    .success-screen .check { font-size: 52px; margin-bottom: 20px; }
    .success-screen h2 { font-family: var(--font-display); font-size: 26px; color: var(--text); margin-bottom: 12px; }
    .success-screen p { font-size: 14px; color: var(--text2); line-height: 1.7; max-width: 420px; margin: 0 auto; }

    /* Footer */
    .footer { margin-top: 32px; text-align: center; font-size: 12px; color: var(--text3); }
    .footer a { color: var(--accent); text-decoration: none; }

    @media (max-width: 560px) {
      .card { padding: 24px 20px; }
      .form-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
<div class="page-wrap">

  <!-- Brand header -->
  <div class="brand">
    <div class="brand-icon">M</div>
    <div>
      <h1>McCrory Center</h1>
      <p>Career Application Portal</p>
    </div>
  </div>

  <!-- Main card -->
  <div class="card" id="formCard">
    <div class="card-title">Submit Your Application</div>
    <p class="card-sub">Fill in your details below and attach your resume. Our HR team will review your application and reach out within a few business days.</p>

    <div class="alert alert-error" id="errorAlert">
      <span>⚠️</span>
      <div><strong>Please fix the following:</strong> <span id="errorMsg"></span></div>
    </div>

    <!-- Personal info -->
    <div class="form-row">
      <div class="form-group">
        <label>First Name <span class="req">*</span></label>
        <input id="aF" placeholder="Jane" required>
      </div>
      <div class="form-group">
        <label>Last Name <span class="req">*</span></label>
        <input id="aL" placeholder="Doe" required>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Email Address</label>
        <input id="aE" type="email" placeholder="jane@example.com">
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input id="aP" type="tel" placeholder="(313) 555-0100">
      </div>
    </div>

    <div class="form-group">
      <label>Street Address</label>
      <input id="aAddr" placeholder="123 Main St">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>City</label>
        <input id="aCity" placeholder="Detroit">
      </div>
      <div class="form-group">
        <label>State / Province</label>
        <input id="aState" placeholder="MI">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Postal Code</label>
        <input id="aPostal" placeholder="48201">
      </div>
      <div class="form-group">
        <label>LinkedIn Profile</label>
        <input id="aLinkedIn" type="url" placeholder="https://linkedin.com/in/jane-doe">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Years of Experience</label>
        <input id="aYears" type="number" min="0" max="60" placeholder="3">
      </div>
      <div class="form-group">
        <label>Desired Pay (optional)</label>
        <input id="aPay" type="number" min="0" step="0.01" placeholder="24.50">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Earliest Start Date</label>
        <input id="aStart" type="date">
      </div>
      <div class="form-group">
        <label>Authorized to work in the U.S.?</label>
        <select id="aAuth">
          <option value="">Select...</option>
          <option value="1">Yes</option>
          <option value="0">No</option>
        </select>
      </div>
    </div>

    <!-- Position -->
    <div class="form-group">
      <label>Position / Category of Interest</label>
      <select id="aC">
        <option value="">Select a category...</option>
      </select>
    </div>

    <!-- Availability -->
    <div class="form-group">
      <label>Availability</label>
      <select id="aAvailability">
        <option value="">Select availability...</option>
        <option value="full_time">Full-Time</option>
        <option value="part_time">Part-Time</option>
        {{-- <option value="contract">Contract</option>
        <option value="temporary">Temporary</option>
        <option value="internship">Internship</option> --}}
        <option value="remote">Remote</option>
      </select>
    </div>

    <!-- Education Level -->
    <div class="form-group">
      <label>Highest Level of Education</label>
      <select id="aEdu">
        <option value="">Select education level...</option>
        <option value="high-school">High School / GED</option>
        <option value="associates">Associate's Degree</option>
        <option value="bachelors">Bachelor's Degree</option>
        <option value="masters">Master's Degree</option>
        <option value="doctorate">Doctorate (Ph.D. / M.D.)</option>
        <option value="other">Other</option>
      </select>
    </div>

    <!-- Resume upload -->
    <label style="margin-bottom:8px;display:block">Resume File <span style="font-weight:400;color:var(--text3)">(PDF, DOC, DOCX, TXT — max 10 MB)</span></label>
    <div class="upload-zone" id="uploadZone" onclick="document.getElementById('aFile').click()">
      <input type="file" id="aFile" accept=".pdf,.doc,.docx,.txt" style="display:none">
      <div class="uz-icon">📎</div>
      <h4>Click to attach your resume</h4>
      <p>or drag and drop it here</p>
    </div>
    <div class="file-list" id="fileList"></div>

    <div class="divider">
      <div class="divider-line"></div>
      <div class="divider-text">or paste your resume text</div>
      <div class="divider-line"></div>
    </div>

    <!-- Resume text -->
    <div class="form-group">
      <label>Resume / Summary</label>
      <textarea id="aR" rows="6" placeholder="Paste your resume, work history, or a brief summary of your experience here..."></textarea>
    </div>

    <button class="btn-submit" id="submitBtn" onclick="submitApplication()">
      <span class="btn-text">Submit Application</span>
      <span class="spinner">Submitting...</span>
    </button>
  </div>

  <!-- Success screen -->
  <div class="card success-screen" id="successScreen">
    <div class="check">🎉</div>
    <h2>Application Received!</h2>
    <p>Thank you for applying to McCrory Center. Our HR team will review your application and contact you shortly.</p>
  </div>

  <div class="footer">
    &copy; {{ date('Y') }} McCrory Center &mdash; <a href="https://www.mccrorycenter.com" target="_blank" rel="noopener">mccrorycenter.com</a>
  </div>

</div><!-- /page-wrap -->

<script>
// ── Drag & drop ──────────────────────────────────────────
var uploadZone = document.getElementById('uploadZone');
uploadZone.addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('drag-over'); });
uploadZone.addEventListener('dragleave', function(){ this.classList.remove('drag-over'); });
uploadZone.addEventListener('drop', function(e){
  e.preventDefault(); this.classList.remove('drag-over');
  if(e.dataTransfer.files.length) handleFile(e.dataTransfer.files[0]);
});
document.getElementById('aFile').addEventListener('change', function(e){
  if(e.target.files.length) handleFile(e.target.files[0]);
});

var selectedFile = null;
function handleFile(file){
  var allowed = ['application/pdf','application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain'];
  if(!allowed.includes(file.type) && !file.name.match(/\.(pdf|doc|docx|txt)$/i)){
    showError('Only PDF, DOC, DOCX, or TXT files are accepted.'); return;
  }
  if(file.size > 10 * 1024 * 1024){ showError('File must be under 10 MB.'); return; }
  selectedFile = file;
  var fl = document.getElementById('fileList');
  fl.innerHTML = '<div class="file-item"><span>📄</span><span class="file-name">'+esc(file.name)+'</span>'
    +'<span class="file-size" style="color:var(--text3);font-size:11px">'+fSize(file.size)+'</span>'
    +'<span class="file-remove" title="Remove" onclick="removeFile()">✕</span></div>';
}
function removeFile(){
  selectedFile = null;
  document.getElementById('fileList').innerHTML = '';
  document.getElementById('aFile').value = '';
}
function fSize(b){ if(b<1024) return b+'B'; if(b<1048576) return (b/1024).toFixed(1)+'KB'; return (b/1048576).toFixed(1)+'MB'; }
function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

// ── Load categories ──────────────────────────────────────
async function loadCategories(){
  try {
    var r = await fetch('/api/public/job-categories');
    if(!r.ok) return;
    var cats = await r.json();
    var sel = document.getElementById('aC');
    (cats||[]).forEach(function(c){
      var o=document.createElement('option'); o.value=c.id; o.textContent=c.name; sel.appendChild(o);
    });
  } catch(e){ /* non-critical */ }
}

// ── Submit ───────────────────────────────────────────────
async function submitApplication(){
  hideError();
  var f = document.getElementById('aF').value.trim();
  var l = document.getElementById('aL').value.trim();
  if(!f || !l){ showError('First name and last name are required.'); return; }

  var btn = document.getElementById('submitBtn');
  btn.disabled = true; btn.classList.add('loading');

  var fd = new FormData();
  fd.append('first_name', f);
  fd.append('last_name', l);
  var email = document.getElementById('aE').value.trim();
  var phone = document.getElementById('aP').value.trim();
  var streetAddress = document.getElementById('aAddr').value.trim();
  var city = document.getElementById('aCity').value.trim();
  var state = document.getElementById('aState').value.trim();
  var postalCode = document.getElementById('aPostal').value.trim();
  var linkedIn = document.getElementById('aLinkedIn').value.trim();
  var yearsExperience = document.getElementById('aYears').value.trim();
  var desiredPay = document.getElementById('aPay').value.trim();
  var earliestStart = document.getElementById('aStart').value;
  var workAuth = document.getElementById('aAuth').value;
  var cat   = document.getElementById('aC').value;
  var availability = document.getElementById('aAvailability') ? document.getElementById('aAvailability').value : '';
  var edu   = document.getElementById('aEdu').value;
  var text  = document.getElementById('aR').value.trim();
  if(email) fd.append('email', email);
  if(phone) fd.append('phone', phone);
  if(streetAddress) fd.append('street_address', streetAddress);
  if(city) fd.append('city', city);
  if(state) fd.append('state', state);
  if(postalCode) fd.append('postal_code', postalCode);
  if(linkedIn) fd.append('linkedin_url', linkedIn);
  if(yearsExperience !== '') fd.append('years_experience', yearsExperience);
  if(desiredPay !== '') fd.append('desired_pay', desiredPay);
  if(earliestStart) fd.append('earliest_start_date', earliestStart);
  if(workAuth !== '') fd.append('is_authorized_to_work', workAuth);
  if(cat)   fd.append('job_category_id', cat);
  if(availability) fd.append('availability', availability);
  if(edu)   fd.append('education_level', edu);
  if(text)  fd.append('resume_text', text);
  if(selectedFile) fd.append('resume_file', selectedFile);
  fd.append('source', 'Website');

  try {
    var r = await fetch('/api/public/apply', { method: 'POST', body: fd });
    var data = await r.json();
    if(r.ok){
      document.getElementById('formCard').style.display = 'none';
      document.getElementById('successScreen').style.display = 'block';
    } else {
      var msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Submission failed. Please try again.');
      showError(msg);
    }
  } catch(e){
    showError('Network error. Please check your connection and try again.');
  }

  btn.disabled = false; btn.classList.remove('loading');
}

function showError(msg){
  var a = document.getElementById('errorAlert');
  document.getElementById('errorMsg').textContent = ' ' + msg;
  a.classList.add('show');
  a.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function hideError(){
  document.getElementById('errorAlert').classList.remove('show');
}

document.addEventListener('DOMContentLoaded', loadCategories);
</script>
</body>
</html>
