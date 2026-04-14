@extends('layouts.app')
@section('title','TalentFlow — Resume Intake')
@section('content')
<div class="animate-in">
  <div class="section-title">📄 Upload Resumes</div>
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Upload resumes from Indeed, LinkedIn, or direct applications. Candidates auto-assign to HR staff via round-robin.</p>
  <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fileInput').click()">
    <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx,.txt" style="display:none">
    <div class="icon">📁</div>
    <h3>Drag & drop resumes here</h3>
    <p>or click to browse — PDF, DOC, DOCX, TXT (max 10 MB each)</p>
  </div>
  <div id="uploadProgress" style="margin-top:12px"></div>

  <div style="margin-top:28px" class="section-title">✍️ Manual Entry</div>
  <div class="card-section">
    <div class="form-row">
      <div class="form-group"><label>First Name *</label><input id="mF" required></div>
      <div class="form-group"><label>Last Name *</label><input id="mL" required></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Email</label><input id="mE" type="email"></div>
      <div class="form-group"><label>Phone</label><input id="mP" type="tel"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Category</label><select id="mC"><option value="">Select…</option></select></div>
      <div class="form-group"><label>Source *</label><select id="mS"><option>Indeed</option><option>LinkedIn</option><option>Referral</option><option>Website</option><option>Walk-in</option><option>Other</option></select></div>
    </div>
    <div class="form-group"><label>Resume / Summary</label><textarea id="mR" rows="5" placeholder="Paste the full resume text or a summary…"></textarea></div>
    <button class="btn btn-primary" onclick="manualAdd()">Add to Review Queue</button>
  </div>

  <div style="margin-top:28px" class="section-title">🕐 Recent Additions</div>
  <div class="table-wrap">
    <table><thead><tr><th>Candidate</th><th>Category</th><th>Source</th><th>Status</th><th>Added</th><th></th></tr></thead>
    <tbody id="recentTbody"></tbody></table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadRecent(); }

async function loadCategories(){
    var r = await apiFetch('/api/settings');
    if(!r) return;
    // categories from candidates endpoint metadata — we'll do a quick pull
    var r2 = await apiFetch('/api/candidates?per_page=1');
    // fallback: fetch job categories via employees or just load hardcoded defaults
    // We don't have a /api/categories endpoint, so load from settings or use typical values
    var cats = ['Licensed Clinician','Masters Level','Bachelors Level','Supports Coordinator','Administrative'];
    var sel = document.getElementById('mC');
    if(!sel) return;
    cats.forEach(function(c){ var o=document.createElement('option'); o.value=c; o.textContent=c; sel.appendChild(o); });
}

async function loadRecent(){
    var r = await apiFetch('/api/candidates?sort=created_at&direction=desc&per_page=10');
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];
    var tbody = document.getElementById('recentTbody');
    if(!items.length){ tbody.innerHTML='<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">No candidates yet.</td></tr>'; return; }
    tbody.innerHTML = items.map(function(c){
        return '<tr><td><div class="candidate-name">'+esc(c.first_name+' '+c.last_name)+'</div><div class="candidate-sub">'+esc(c.email||'')+'</div></td>'
            +'<td>'+(c.category?esc(c.category.name):'—')+'</td>'
            +'<td>'+esc(c.source||'—')+'</td>'
            +'<td>'+B(c.status)+'</td>'
            +'<td>'+esc(fDate(c.created_at))+'</td>'
            +'<td><button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">View</button></td></tr>';
    }).join('');
    updateReviewBadge();
}

async function manualAdd(){
    var f = document.getElementById('mF').value.trim();
    var l = document.getElementById('mL').value.trim();
    if(!f||!l){ toast('First and last name are required','error'); return; }
    var body = {
        first_name: f, last_name: l,
        email: document.getElementById('mE').value||null,
        phone: document.getElementById('mP').value||null,
        job_category_id: null,
        source: document.getElementById('mS').value,
        notes: null,
        resume_text: document.getElementById('mR').value||null
    };
    // Map category name to id — we'll send name and handle on server, or skip for now
    var r = await apiFetch('/api/candidates', {method:'POST', body:JSON.stringify(body)});
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||'Failed to add candidate','error'); return; }
    toast(f+' '+l+' added to review queue!');
    ['mF','mL','mE','mP','mR'].forEach(function(id){ document.getElementById(id).value=''; });
    loadRecent();
    updateReviewBadge();
}

document.getElementById('fileInput').addEventListener('change', handleFileUpload);
document.getElementById('uploadZone').addEventListener('dragover', function(e){ e.preventDefault(); this.classList.add('drag-over'); });
document.getElementById('uploadZone').addEventListener('dragleave', function(){ this.classList.remove('drag-over'); });
document.getElementById('uploadZone').addEventListener('drop', function(e){ e.preventDefault(); this.classList.remove('drag-over'); if(e.dataTransfer.files.length) handleFiles(e.dataTransfer.files); });

async function handleFileUpload(e){ handleFiles(e.target.files); }

async function handleFiles(files){
    var prog = document.getElementById('uploadProgress');
    prog.innerHTML = '<div style="color:var(--text2);font-size:13px">Uploading '+files.length+' file(s)…</div>';
    var fd = new FormData();
    for(var i=0;i<files.length;i++) fd.append('resumes[]', files[i]);
    var r = await apiFetch('/api/candidates-upload', {method:'POST', body:fd});
    if(!r){ prog.innerHTML=''; return; }
    var data = await r.json();
    if(r.ok){
        toast((data.created||[]).length + ' resume(s) uploaded!');
        prog.innerHTML = '';
        loadRecent();
        updateReviewBadge();
    } else {
        prog.innerHTML = '<div style="color:var(--red);font-size:13px">'+(data.message||'Upload failed')+'</div>';
    }
    document.getElementById('fileInput').value='';
}

document.addEventListener('DOMContentLoaded', function(){ loadCategories(); loadRecent(); });
</script>
@endpush
