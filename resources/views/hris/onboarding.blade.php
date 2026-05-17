@extends('layouts.app')
@section('title','McCrory Center — Pre-Onboard Documents')
@section('content')
<div class="animate-in">
  <div class="section-title">Pre-Onboard Documents</div>
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">This page acts as the main onboarding workspace. HR can review outstanding items here and send portal credentials once every required onboarding task is complete.</p>
  <div id="onboardingList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading...</div></div>
</div>

<!-- Task Detail Modal -->
<div class="modal-overlay" id="modal-taskModal" onclick="if(event.target===this)closeModal('taskModal')">
  <div class="modal" style="max-width:460px">
    <div class="modal-header">
      <h3 id="tdTaskTitle" style="font-size:15px"></h3>
      <button onclick="closeModal('taskModal')">✕</button>
    </div>
    <div class="modal-body" style="display:flex;flex-direction:column;gap:14px">
      <div id="tdStatusBadge"></div>

      <!-- Orientation date (shown for orientation-date tasks) -->
      <div id="tdOrientSection" style="display:none">
        <div class="form-group" style="margin-bottom:0">
          <label>Select Orientation Date <span style="color:var(--red)">*</span></label>
          <input type="date" id="tdOrientDate">
        </div>
      </div>

      <!-- Document upload (shown for upload-type tasks) -->
      <div id="tdDocSection" style="display:none">
        <div class="form-group" style="margin-bottom:6px">
          <label>Upload Document <span style="font-size:11px;color:var(--text3)">(optional)</span></label>
          <input type="file" id="tdDocFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
        </div>
        <div id="tdExistingDoc" style="display:none;font-size:12px;padding:6px 0"></div>
      </div>

      <div id="tdHint" style="display:none;font-size:12px;color:var(--text3);background:var(--bg2);padding:10px 12px;border-radius:8px;line-height:1.5"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary btn-sm" onclick="deleteCurrentTask()" style="margin-right:auto;color:var(--red);border-color:var(--red)">🗑 Remove Task</button>
      <button class="btn btn-secondary" onclick="closeModal('taskModal')">Cancel</button>
      <button id="tdCompleteBtn" class="btn btn-success" onclick="toggleCurrentTask()">✓ Mark as Complete</button>
    </div>
  </div>
</div>

<!-- Grant Portal Access / Send Credentials Modal -->
<div class="modal-overlay" id="modal-grantAccessModal" onclick="if(event.target===this)closeModal('grantAccessModal')">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <h3>🎉 Send Portal Credentials</h3>
      <button onclick="closeModal('grantAccessModal')">✕</button>
    </div>
    <div class="modal-body">
      <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px 14px;font-size:13px;color:#166534;margin-bottom:16px">
        ✅ All onboarding tasks complete! Review credentials below and send the employee their portal access.
      </div>
      <input type="hidden" id="gaCandidateId">
      <div class="form-group">
        <label>Employee Login Email</label>
        <input type="email" id="gaEmail" readonly style="background:var(--bg2);cursor:default">
      </div>
      <div class="form-group">
        <label>Temporary Password <span style="color:var(--red)">*</span></label>
        <div style="display:flex;gap:8px">
          <input type="text" id="gaTempPassword" placeholder="Enter or generate a temporary password" style="flex:1">
          <button type="button" class="btn btn-secondary btn-sm" onclick="genTempPass()">Generate</button>
        </div>
      </div>
      <div class="form-group">
        <label>Door / Building Access Info</label>
        <input type="text" id="gaDoorCode" placeholder="Not configured in Settings">
      </div>
      <div class="form-group">
        <label>WiFi Password</label>
        <input type="text" id="gaWifiPass" placeholder="Not configured in Settings">
      </div>
      <div style="font-size:12px;color:var(--text3);padding:4px 0">
        💡 Door and WiFi values are pre-filled from System Settings — edit freely before sending.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('grantAccessModal')">Cancel</button>
      <button class="btn btn-success" onclick="confirmGrantAccess()">🔓 Create Employee &amp; Send Email</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _obSettings  = {};
var _obData      = {}; // keyed by candidate ID
var _tdTask      = null;
var _tdCandidateId = null;

/* ── Hints shown inside task modal ── */
var _taskHints = {
    'upload signed offer letter':        'Ask the candidate to upload the signed offer letter before proceeding.',
    'complete background check consent': 'Ensure the background check consent form has been signed and submitted.',
    'submit references':                 'Candidate should submit 3 professional references.',
    'upload credentials & licenses':     'All relevant licenses and certifications must be uploaded.',
    'complete i-9 verification':         'Verify identity documents and complete the I-9 form (must be done in person).',
    'upload driver\'s license':          'A copy of a valid government-issued photo ID is required.',
    'select orientation date':           'Coordinate with the candidate and set their orientation date.',
    'setup email account':               'Create the employee\'s company email account and note the credentials.',
    'building access & wifi':            'Issue building key/fob and share the WiFi password.',
    'review employee handbook':          'Candidate must confirm they have read and understood the employee handbook.',
    'complete initial training modules': 'Ensure all mandatory training modules have been completed.',
};

async function pageRefresh(){ await Promise.all([loadOnboarding(), loadObSettings()]); }

async function loadObSettings(){
    var r = await apiFetch('/api/settings');
    if(!r) return;
    var d = await r.json();
    _obSettings = d.settings || d || {};
}

async function loadOnboarding(){
    // Only candidates currently in the "Pre-Onboard Documents" stage. The other
    // onboarding sub-stages (Compliance, Clinical, etc.) each have their own sidebar page.
    var r = await apiFetch('/api/onboarding?status=pre_onboard_documents');
    if(!r) return;
    var data  = await r.json();
    var items = data.data || data;
    var el    = document.getElementById('onboardingList');

    if(!items.length){
        el.innerHTML = '<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:40px;margin-bottom:12px">🎉</div><p>No active onboarding.</p></div>';
        return;
    }

    _obData = {};
    el.innerHTML = items.map(function(item){
        var c   = item.candidate || item;
        var cid = c.id || item.candidate_id;
        var tasks = item.onboarding_tasks || c.onboarding_tasks || [];
        var offer = c.latest_offer || null;

        _obData[cid] = {
            tasks:           tasks,
            offerId:         offer ? offer.id : null,
            email:           c.email || '',
            orientationDate: offer ? (offer.orientation_date || '') : '',
        };

        var done  = tasks.filter(function(t){ return t.is_completed; }).length;
        var total = tasks.length;
        var pct   = total > 0 ? Math.round(done / total * 100) : 0;
        var canGrant = (total > 0 && done === total);
        var orientDate = _obData[cid].orientationDate;

        var taskHtml = tasks.map(function(t){
            var checked = t.is_completed;
            var cbStyle = checked
                ? 'background:var(--green);border-color:var(--green)'
                : 'background:transparent';
            return '<div class="checklist-item '+(checked?'done':'')+'"'
              +' style="cursor:pointer;user-select:none"'
              +' onclick="openTaskModal('+t.id+','+cid+')">'
              +'<div class="checkbox" style="'+cbStyle+';pointer-events:none">'
              +'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20,6 9,17 4,12"/></svg></div>'
              +'<div class="text" style="flex:1">'+esc(t.task_name)
              +(/orientation.?date/i.test(t.task_name) && orientDate
                  ? ' <span style="font-size:11px;color:var(--primary);font-weight:600">— '+fDate(orientDate)+'</span>'
                  : '')
              +'</div>'
              +(checked && t.completed_at
                  ? '<span style="font-size:11px;color:var(--green);white-space:nowrap">✓ '+fDate(t.completed_at)+'</span>'
                  : '<span style="font-size:11px;color:var(--text3)">tap to review</span>')
            +'</div>';
        }).join('');

        var grantBtn = canGrant
            ? '<button class="btn btn-success" onclick="openGrantAccessModal('+cid+')">🔓 Send Portal Credentials</button>'
            : '<button class="btn btn-secondary" disabled title="Complete all tasks to unlock" style="opacity:0.45;cursor:not-allowed">🔒 Send Portal Credentials</button>';

        return '<div class="card-section" id="ob-card-'+cid+'" style="padding-bottom:0">'
          +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;cursor:pointer" onclick="toggleObCard('+cid+')" title="Click to expand / collapse">'
            +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(cid)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">'+In(c.first_name||item.first_name,c.last_name||item.last_name)+'</div>'
            +'<div style="flex:1">'
              +'<div style="font-weight:600">'+esc((c.first_name||item.first_name)+' '+(c.last_name||item.last_name))+'</div>'
              +'<div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'—')+' · '+done+'/'+total+' tasks complete'+(orientDate?' · Orientation: '+fDate(orientDate):'')+'</div>'
            +'</div>'
            +'<div style="display:flex;align-items:center;gap:10px">'
              +B(c.status||item.status)
              +'<span id="ob-chevron-'+cid+'" style="font-size:12px;color:var(--text3);transition:transform .2s">▼</span>'
            +'</div>'
          +'</div>'
          +'<div class="progress-wrap" style="margin-bottom:0"><div class="progress-fill" style="width:'+pct+'%"></div></div>'
          +'<div id="ob-body-'+cid+'" style="display:none;padding-top:12px;padding-bottom:14px">'
            +taskHtml
            +'<div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">'
              +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+cid+')">Full Profile</button>'
              +grantBtn
            +'</div>'
          +'</div>'
        +'</div>';
    }).join('');
}

/* ── Collapse / Expand card ── */
function toggleObCard(cid){
    var body    = document.getElementById('ob-body-'+cid);
    var chevron = document.getElementById('ob-chevron-'+cid);
    if(!body) return;
    var open = body.style.display === 'none';
    body.style.display    = open ? '' : 'none';
    chevron.style.transform = open ? 'rotate(180deg)' : '';
}

/* ── Task Detail Modal ── */
function openTaskModal(taskId, cid){
    var entry = _obData[cid];
    if(!entry) return;
    var task = entry.tasks.find(function(t){ return t.id === taskId; });
    if(!task) return;

    _tdTask        = task;
    _tdCandidateId = cid;

    document.getElementById('tdTaskTitle').textContent = task.task_name;

    /* Status badge */
    var statusEl = document.getElementById('tdStatusBadge');
    if(task.is_completed){
        statusEl.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:20px;font-size:13px;color:#16a34a">✓ Completed'+(task.completed_at?' · '+fDate(task.completed_at):'')+'</span>';
        document.getElementById('tdCompleteBtn').textContent = '↩ Mark as Incomplete';
        document.getElementById('tdCompleteBtn').className   = 'btn btn-warning';
    } else {
        statusEl.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;background:var(--bg2);border:1px solid var(--border);border-radius:20px;font-size:13px;color:var(--text3)">⏳ Pending</span>';
        document.getElementById('tdCompleteBtn').textContent = '✓ Mark as Complete';
        document.getElementById('tdCompleteBtn').className   = 'btn btn-success';
    }

    /* Orientation date section */
    var isOrient = /orientation.?date/i.test(task.task_name);
    document.getElementById('tdOrientSection').style.display = isOrient ? '' : 'none';
    if(isOrient) document.getElementById('tdOrientDate').value = entry.orientationDate || '';

    /* Document upload section */
    var needsDoc = /upload|license|i[-]?9|credential/i.test(task.task_name);
    document.getElementById('tdDocSection').style.display = needsDoc ? '' : 'none';
    document.getElementById('tdDocFile').value = '';
    var existDoc = document.getElementById('tdExistingDoc');
    if (task.document_path) {
      existDoc.style.display = '';
      // Support single path or multiple paths (comma/newline separated or array)
      var paths = [];
      if (Array.isArray(task.document_path)) {
        paths = task.document_path;
      } else if (typeof task.document_path === 'string') {
        paths = task.document_path.split(/[,\n]+/).map(function(p){ return p.trim(); }).filter(Boolean);
      } else {
        paths = [task.document_path];
      }

      var html = paths.map(function(p){
        // If file was stored directly in public (e.g. onboarding/ or resumes/), link to '/path'
        var isPublicDirect = /^(onboarding|resumes)\//i.test(p);
        var url = (isPublicDirect ? '/' + esc(p) : '/' + esc(p));
        var parts = (p||'').split('/');
        var name = parts[parts.length-1] || p;
        var ext = (name.split('.').pop()||'').toLowerCase();
        var preview = '';
        if(['jpg','jpeg','png','gif'].indexOf(ext) !== -1){
          preview = '<div style="margin-top:8px"><img src="'+url+'" style="max-width:100%;border-radius:6px;border:1px solid var(--border)"></div>';
        }
        return '📎 <a href="'+url+'" target="_blank" style="color:var(--primary)">'+esc(name)+'</a>'+preview;
      }).join('<br>');

      existDoc.innerHTML = html;
    } else {
      existDoc.style.display = 'none';
    }

    /* Hint */
    var hint   = (_taskHints[task.task_name.toLowerCase()] || '');
    var hintEl = document.getElementById('tdHint');
    hintEl.style.display = hint ? '' : 'none';
    hintEl.textContent   = hint ? '💡 ' + hint : '';

    openModal('taskModal');
}

async function toggleCurrentTask(){
    if(!_tdTask) return;
    var btn   = document.getElementById('tdCompleteBtn');
    btn.disabled = true;

    var entry    = _obData[_tdCandidateId];
    var isOrient = /orientation.?date/i.test(_tdTask.task_name);

    /* Save orientation date before marking complete */
    if(isOrient && !_tdTask.is_completed){
        var dateVal = document.getElementById('tdOrientDate').value;
        if(!dateVal){ toast('Please select an orientation date first','error'); btn.disabled=false; return; }
        if(entry.offerId){
            var rd = await apiFetch('/api/offers/'+entry.offerId, {method:'PATCH', body:JSON.stringify({orientation_date:dateVal})});
            if(!rd || !rd.ok){ toast('Failed to save orientation date','error'); btn.disabled=false; return; }
        }
    }

    /* Toggle the task (with optional document upload) */
    var fileInput = document.getElementById('tdDocFile');
    var needsDoc  = document.getElementById('tdDocSection').style.display !== 'none';
    var r;
    if(needsDoc && fileInput.files.length){
      var fd = new FormData();
      fd.append('document', fileInput.files[0]);
      // Use POST with method override so PHP/Laravel properly parses multipart file uploads
      fd.append('_method', 'PATCH');
      r = await apiFetch('/api/onboarding-tasks/'+_tdTask.id+'/toggle', { method: 'POST', body: fd });
    } else {
      r = await apiFetch('/api/onboarding-tasks/'+_tdTask.id+'/toggle', {method:'PATCH'});
    }

    btn.disabled = false;
    if(!r || !r.ok){ toast('Failed to update task','error'); return; }

    var result   = await r.json();
    var progress = result.progress || {};

    closeModal('taskModal');
    await loadOnboarding();

    /* Auto-open credentials modal when all tasks are done */
    if(result.task && result.task.is_completed && progress.total > 0 && progress.completed === progress.total){
        setTimeout(function(){
            toast('🎉 All tasks complete! Ready to send portal credentials.');
            openGrantAccessModal(_tdCandidateId);
        }, 350);
    }
}

async function deleteCurrentTask(){
    if(!_tdTask) return;
    if(!confirm('Delete task "'+_tdTask.task_name+'"?')) return;
    var r = await apiFetch('/api/onboarding-tasks/'+_tdTask.id, {method:'DELETE'});
    if(!r) return;
    closeModal('taskModal');
    toast('Task deleted.');
    loadOnboarding();
}

/* ── Grant Portal Access ── */
function openGrantAccessModal(cid){
    var entry = _obData[cid] || {};
    document.getElementById('gaCandidateId').value  = cid;
    document.getElementById('gaEmail').value        = entry.email || '';
    document.getElementById('gaDoorCode').value     = _obSettings.door_access_info || '';
    document.getElementById('gaWifiPass').value     = _obSettings.wifi_password    || '';
    document.getElementById('gaTempPassword').value = '';
    openModal('grantAccessModal');
}

function genTempPass(){
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    var pass  = '';
    for(var i=0;i<12;i++) pass += chars[Math.floor(Math.random()*chars.length)];
    document.getElementById('gaTempPassword').value = pass;
}

async function confirmGrantAccess(){
    var cid      = document.getElementById('gaCandidateId').value;
    var tempPass = document.getElementById('gaTempPassword').value.trim();
    if(!tempPass){ toast('Please set a temporary password','error'); return; }

    var r = await apiFetch('/api/candidates/'+cid+'/convert', {
        method: 'POST',
        body: JSON.stringify({
            access_info: {
                email_login:   document.getElementById('gaEmail').value,
                temp_password: tempPass,
                door_code:     document.getElementById('gaDoorCode').value,
                wifi_password: document.getElementById('gaWifiPass').value,
            }
        })
    });
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||'Failed to grant access','error'); return; }

    closeModal('grantAccessModal');
    toast('✅ Employee created and credentials email sent!');
    loadOnboarding();
}

document.addEventListener('DOMContentLoaded', pageRefresh);
</script>
@endpush
