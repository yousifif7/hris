@extends('layouts.app')
@section('title','McCrory Center — Resume Intake')
@section('content')
<div class="animate-in">

  {{-- Share Apply Link --}}
  <div class="card-section" style="margin-bottom:24px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
    <div style="flex:1;min-width:220px">
      <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:2px">🔗 Public Apply Link</div>
      <div style="font-size:12px;color:var(--text2)">Share this URL with candidates — no login required.</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
      <input id="applyLinkInput" readonly style="width:340px;max-width:100%;font-size:12px;background:var(--surface2);color:var(--text2)" value="Loading...">
      <button class="btn btn-secondary btn-sm" onclick="copyApplyLink()">Copy</button>
      <button class="btn btn-secondary btn-sm" onclick="regenerateApplyLink()" title="Generate a new link (invalidates the old one)">Regenerate</button>
    </div>
  </div>

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
      <div class="form-group"><label>Category</label><select id="mC"><option value="">Select...</option></select></div>
      <div class="form-group"><label>Source *</label><select id="mS"><option>Indeed</option><option>LinkedIn</option><option>Referral</option><option>Website</option><option>Walk-in</option><option>Other</option></select></div>
    </div>
    <div class="form-group"><label>Resume / Summary</label><textarea id="mR" rows="5" placeholder="Paste the full resume text or a summary..."></textarea></div>
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

async function loadApplyLink(){
    var r = await apiFetch('/api/settings/apply-link');
    if(!r) return;
    var data = await r.json();
    var inp = document.getElementById('applyLinkInput');
    if(inp && data.url) inp.value = data.url;
}

async function copyApplyLink(){
    var val = document.getElementById('applyLinkInput').value;
    if(!val || val === 'Loading...') return;
    try { await navigator.clipboard.writeText(val); toast('Link copied to clipboard!'); }
    catch(e) { document.getElementById('applyLinkInput').select(); document.execCommand('copy'); toast('Link copied!'); }
}

async function regenerateApplyLink(){
    if(!confirm('Regenerating the link will invalidate the old one. Continue?')) return;
    var r = await apiFetch('/api/settings/apply-link/regenerate', {method:'POST'});
    if(!r) return;
    var data = await r.json();
    var inp = document.getElementById('applyLinkInput');
    if(inp && data.url){ inp.value = data.url; toast('New apply link generated!'); }
}

async function loadCategories(){
    var r = await apiFetch('/api/job-categories');
    if(!r) return;
    var cats = await r.json();
    var sel = document.getElementById('mC');
    if(!sel) return;
    sel.innerHTML = '<option value="">Select...</option>';
    (cats||[]).forEach(function(c){
        var o = document.createElement('option');
        o.value = c.id; o.textContent = c.name;
        sel.appendChild(o);
    });
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
        job_category_id: document.getElementById('mC').value ? parseInt(document.getElementById('mC').value) : null,
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
    prog.innerHTML = '<div style="color:var(--text2);font-size:13px">Uploading '+files.length+' file(s)...</div>';
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

document.addEventListener('DOMContentLoaded', function(){ loadApplyLink(); loadCategories(); loadRecent(); });
</script>
@endpush
