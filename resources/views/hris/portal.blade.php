@extends('layouts.app')
@section('title','McCrory Center — Employee Portal')
@section('content')
<div class="animate-in">
  <!-- My Info Banner -->
  <div class="card-section" id="portalBanner" style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
    <div id="portalAvatar" style="width:52px;height:52px;border-radius:50%;background:#6c63ff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700">?</div>
    <div>
      <div id="portalName" style="font-size:17px;font-weight:700">Loading…</div>
      <div id="portalRole" style="font-size:13px;color:var(--text3)"></div>
    </div>
  </div>

  <!-- Quick tiles -->
  <div class="section-title">Quick Actions</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:24px">
    <a href="/hris/timeoff"     class="portal-tile">🏖 Time Off</a>
    <a href="/hris/employees"  class="portal-tile">📋 Directory</a>
    <div class="portal-tile" onclick="openDocUpload()">📎 Upload Doc</div>
    <a href="/hris/onboarding" class="portal-tile">🎯 Onboarding</a>
    <div class="portal-tile" onclick="loadTrainings()">📚 Trainings</div>
    <a href="/hris/settings"   class="portal-tile">⚙ Settings</a>
  </div>

  <!-- Trainings -->
  <div class="section-title">📚 Trainings &amp; Certifications</div>
  <div style="display:flex;gap:8px;margin-bottom:12px">
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('all')">All</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('pending')">Pending</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('expiring')">Expiring Soon</button>
  </div>
  <div class="table-wrap">
    <table><thead><tr><th>Training</th><th>Employee</th><th>Category</th><th>Due Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="trainTbody"><tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading…</td></tr></tbody></table>
  </div>

  <!-- Doc upload modal trigger area -->
  <div id="docUploadArea" style="display:none;margin-top:20px">
    <div class="card-section">
      <div class="section-title">Upload a Document</div>
      <input type="file" id="docFile" style="margin-bottom:8px">
      <button class="btn btn-primary btn-sm" onclick="uploadDoc()">Upload</button>
      <button class="btn btn-secondary btn-sm" onclick="document.getElementById('docUploadArea').style.display='none'">Cancel</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadTrainings('all'); }

var _myEmployeeId = null;

document.addEventListener('DOMContentLoaded', async function(){
    // Load current user info for banner
    var meR = await apiFetch('/api/me');
    if(meR){
        var me = await meR.json();
        var fullName = (me.first_name||'')+' '+(me.last_name||'');
        document.getElementById('portalName').textContent = fullName.trim()||me.name||'';
        document.getElementById('portalRole').textContent = me.role||'';
        document.getElementById('portalAvatar').textContent = ((me.first_name||me.name||'?')[0]||'?').toUpperCase();
        _myEmployeeId = me.employee ? me.employee.id : null;
    }
    loadTrainings('all');
});

async function loadTrainings(filter){
    filter = filter||'all';
    var url = '/api/trainings?per_page=50'+(filter==='expiring'?'&expiring=1':filter==='pending'?'&completed=0':'');
    var r = await apiFetch(url);
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];
    var tbody = document.getElementById('trainTbody');
    if(!items.length){
        tbody.innerHTML='<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">No trainings found.</td></tr>';
        return;
    }
    tbody.innerHTML = items.map(function(t){
        var emp   = t.employee||{};
        var done  = !!t.is_completed;
        var overdue = !done && t.due_date && new Date(t.due_date) < new Date();
        var status  = done?'Complete':overdue?'Overdue':'Pending';
        var sc      = done?'var(--green)':overdue?'var(--red)':'var(--yellow,#d4ac0d)';
        return '<tr>'
          +'<td>'+esc(t.name||'—')+'</td>'
          +'<td>'+esc((emp.first_name||'')+' '+(emp.last_name||''))+'</td>'
          +'<td>'+esc(t.category||'—')+'</td>'
          +'<td>'+fDate(t.due_date)+'</td>'
          +'<td><span style="font-weight:600;color:'+sc+'">'+status+'</span></td>'
          +'<td>'
            +(!done?'<button class="btn btn-success btn-sm" onclick="completeTraining('+t.id+')">Mark Done</button>':'')
          +'</td>'
        +'</tr>';
    }).join('');
}

async function completeTraining(id){
    var r = await apiFetch('/api/trainings/'+id+'/complete', {method:'PATCH'});
    if(!r) return;
    toast('Training marked complete!');
    loadTrainings('all');
}

function openDocUpload(){
    document.getElementById('docUploadArea').style.display = 'block';
}

async function uploadDoc(){
    var file = document.getElementById('docFile').files[0];
    if(!file){ toast('Select a file first.','error'); return; }
    if(!_myEmployeeId){ toast('No employee profile linked to your account.','error'); return; }
    var form = new FormData();
    form.append('file', file);
    form.append('documentable_type', 'employee');
    form.append('documentable_id', _myEmployeeId);
    var r = await apiFetch('/api/documents', {method:'POST', body:form});
    if(!r) return;
    toast('Document uploaded!');
    document.getElementById('docUploadArea').style.display='none';
    document.getElementById('docFile').value='';
}
</script>
@endpush
