@extends('layouts.app')
@section('title','McCrory Center — Employees')
@section('content')
<div class="animate-in">
  <div style="display:flex;gap:8px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
    <input id="empSearch" placeholder="Search employees…" style="width:220px" oninput="filterEmps()">
    <select id="empDept" onchange="filterEmps()" style="width:180px">
      <option value="">All levels</option>
      <option value="Admin">Admin</option>
      <option value="HR Staff">HR Staff</option>
      <option value="Employee">Employee</option>
    </select>
    <select id="empType" onchange="filterEmps()" style="width:160px">
      <option value="">All types</option>
      <option value="full_time">Full-time</option>
      <option value="part_time">Part-time</option>
      <option value="contract">Contract</option>
    </select>
    <span style="flex:1"></span>
    <span id="empCount" style="font-size:13px;color:var(--text3)"></span>
    <button class="btn btn-primary btn-sm" onclick="openAddEmpModal()">+ Add Employee</button>
  </div>
  <div class="emp-grid" id="empGrid">
    <div style="text-align:center;padding:60px;color:var(--text3);grid-column:1/-1">⏳ Loading…</div>
  </div>
</div>

<!-- Add / Edit Employee Modal -->
<datalist id="empRoleList"></datalist>
<div class="modal-overlay" id="modal-addEmpModal" onclick="if(event.target===this)closeModal('addEmpModal')">
  <div class="modal" style="max-width:580px">
    <div class="modal-header"><h3 id="empModalTitle">Add New Employee</h3><button onclick="closeModal('addEmpModal')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="empEditId">
      <div class="form-row">
        <div class="form-group"><label>First Name *</label><input id="empFN" required></div>
        <div class="form-group"><label>Last Name *</label><input id="empLN" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Email *</label><input type="email" id="empEM" required></div>
        <div class="form-group"><label>Phone</label><input id="empPH" type="tel"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Role / Job Title *</label><input id="empRL" required placeholder="e.g. Licensed Clinician" list="empRoleList"></div>
        <div class="form-group"><label>Department / Access Level *</label>
          <select id="empDP">
            <option value="">— select level —</option>
            <option value="Admin">Admin</option>
            <option value="HR Staff">HR Staff</option>
            <option value="Employee">Employee</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Employment Type *</label>
          <select id="empET">
            <option value="full_time">Full-Time</option>
            <option value="part_time">Part-Time</option>
            <option value="contract">Contract / 1099</option>
          </select>
        </div>
        <div class="form-group"><label>Start Date</label><input type="date" id="empSD"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Pay Rate</label><input type="number" id="empPR" step="0.01" placeholder="25.00"></div>
        <div class="form-group"><label>Pay Type</label>
          <select id="empPT">
            <option value="hourly">Hourly</option>
            <option value="salary">Salary</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Location</label><input id="empLO" placeholder="Main Office"></div>
      <div class="form-group" id="empPWRow">
        <label id="empPWLabel">Password *</label>
        <input type="password" id="empPW" placeholder="Set login password" autocomplete="new-password">
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('addEmpModal')">Cancel</button>
      <button class="btn btn-primary" id="empModalBtn" onclick="saveEmployee()">Add Employee</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _allEmps = [];

async function pageRefresh(){ await loadEmployees(); }

async function loadEmployees(){
    var r = await apiFetch('/api/employees?per_page=200');
    if(!r) return;
    var data = await r.json();
    _allEmps = data.data || data || [];
    renderEmps(_allEmps);
}

function filterEmps(){
    var q    = (document.getElementById('empSearch').value||'').toLowerCase();
    var dept = document.getElementById('empDept').value;
    var type = document.getElementById('empType').value;
    var filtered = _allEmps.filter(function(e){
        var nm = (e.first_name+' '+e.last_name).toLowerCase();
        return (!q    || nm.includes(q) || (e.email||'').toLowerCase().includes(q) || (e.role||'').toLowerCase().includes(q))
            && (!dept || e.department===dept)
            && (!type || e.employment_type===type);
    });
    renderEmps(filtered);
}

function renderEmps(emps){
    document.getElementById('empCount').textContent = emps.length+' employee'+(emps.length!==1?'s':'');
    var grid = document.getElementById('empGrid');
    if(!emps.length){
        grid.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3);grid-column:1/-1">No employees found.</div>';
        return;
    }
    grid.innerHTML = emps.map(function(e){
        var trainings    = e.trainings || [];
        var pendingTrain = trainings.filter(function(t){ return !t.is_completed; }).length;
        var roleLabel = {admin:'Admin',hr_staff:'HR Staff',employee:'Employee'}[e.user_role] || e.department || '';
        var cardId = e.id || ('u'+e.user_id);
        var hasRecord = !!e.id;
        return '<div class="emp-card">'
          +'<div style="cursor:'+(hasRecord?'pointer':'default')+'" '+(hasRecord?'onclick="viewEmployee('+e.id+')"':'')+'>'            +'<div class="header">'
              +'<div class="avatar" style="background:'+Cl(e.user_id||e.id)+'">'+ In(e.first_name,e.last_name)+'</div>'
              +'<div class="info"><h4>'+esc(e.first_name+' '+e.last_name)+'</h4><p>'+esc(e.role||'—')+'</p></div>'
            +'</div>'
            +'<div class="details">'
              +(e.employment_type?'<span class="detail-tag">'+esc(e.employment_type.replace('_',' '))+'</span>':'')
              +(roleLabel?'<span class="detail-tag" style="background:var(--primary-bg,#eff6ff);color:var(--primary)">'+esc(roleLabel)+'</span>':'')
              +(e.start_date?'<span class="detail-tag">Since '+esc(e.start_date)+'</span>':'')
              +(pendingTrain?'<span class="detail-tag" style="background:var(--yellow-bg,#fef9e7);color:var(--yellow,#d4ac0d)">⚠ '+pendingTrain+' training due</span>':'')
            +'</div>'
            +'<div style="margin-top:10px;font-size:12px;color:var(--text3)">'+esc(e.email||'')+'</div>'
          +'</div>'
          +(hasRecord
            ? '<div style="display:flex;gap:6px;margin-top:10px;padding-top:10px;border-top:1px solid var(--border)">'
                +'<button class="btn btn-secondary btn-sm" style="flex:1" onclick="editEmployee('+e.id+',event)">✏ Edit</button>'
                +'<button class="btn btn-danger btn-sm" onclick="deleteEmployee('+e.id+',\''+esc(e.first_name+' '+e.last_name)+'\',event)">🗑 Delete</button>'
              +'</div>'
            : '<div style="margin-top:10px;padding-top:10px;border-top:1px solid var(--border);font-size:11px;color:var(--text3)">No employee record — add via Edit</div>')
        +'</div>';
    }).join('');
}

async function viewEmployee(id){
    var r = await apiFetch('/api/employees/'+id);
    if(!r) return;
    var e = await r.json();
    document.getElementById('detailName').textContent = e.first_name+' '+e.last_name;
    var trainHtml = (e.trainings||[]).map(function(t){
        var col = t.is_completed?'var(--green)':'var(--yellow,#d4ac0d)';
        return '<div class="bg-check-row">'
          +'<span>'+esc(t.name)+'</span>'
          +'<span style="color:'+col+';font-weight:600">'+(t.is_completed?'Complete ✓':'Due '+fDate(t.due_date))+'</span>'
        +'</div>';
    }).join('');
    document.getElementById('detailBody').innerHTML =
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;margin-bottom:14px">'
        +'<div><span style="color:var(--text3)">Email</span><br>'+esc(e.email||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Phone</span><br>'+esc(e.phone||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Role</span><br>'+esc(e.role||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Type</span><br>'+esc((e.employment_type||'—').replace('_',' '))+'</div>'
        +'<div><span style="color:var(--text3)">Department</span><br>'+esc(e.department||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Start Date</span><br>'+esc(e.start_date||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Pay</span><br>'+esc(e.pay_rate?'$'+parseFloat(e.pay_rate).toFixed(2)+(e.pay_type==='hourly'?'/hr':'/yr'):'—')+'</div>'
        +'<div><span style="color:var(--text3)">Location</span><br>'+esc(e.location||'—')+'</div>'
      +'</div>'
      +(trainHtml?'<div class="section-title" style="font-size:13px;margin-bottom:8px">📚 Trainings & Certs</div><div class="bg-check-grid">'+trainHtml+'</div>':'');
    document.getElementById('detailFooter').innerHTML=
      '<button class="btn btn-danger btn-sm" onclick="deleteEmployee('+e.id+',\''+esc(e.first_name+' '+e.last_name)+'\'); closeModal(\'candidateDetail\')">🗑 Delete</button>'
      +'<button class="btn btn-secondary" onclick="closeModal(\'candidateDetail\')">Close</button>'
      +'<button class="btn btn-primary" onclick="editEmployee('+e.id+')">✏ Edit</button>';
    openModal('candidateDetail');
}

document.addEventListener('DOMContentLoaded', function(){ loadEmployees(); loadRolesList(); });

async function loadRolesList(){
    var r = await apiFetch('/api/job-categories');
    if(!r) return;
    var cats = await r.json();
    // Populate job-title datalist only (department is now a static fixed list)
    var dl = document.getElementById('empRoleList');
    if(dl) dl.innerHTML = (cats||[]).map(function(c){
        return '<option value="'+esc(c.name)+'">';
    }).join('');
}

function openAddEmpModal(){
    document.getElementById('empEditId').value = '';
    document.getElementById('empModalTitle').textContent = 'Add New Employee';
    document.getElementById('empModalBtn').textContent = 'Add Employee';
    ['empFN','empLN','empEM','empPH','empRL','empDP','empSD','empPR','empLO','empPW'].forEach(function(id){
        document.getElementById(id).value = '';
    });
    document.getElementById('empET').value = 'full_time';
    document.getElementById('empPT').value = 'hourly';
    document.getElementById('empPWLabel').textContent = 'Password *';
    document.getElementById('empPW').required = true;
    openModal('addEmpModal');
}

async function editEmployee(id, evt){
    if(evt) evt.stopPropagation();
    var r = await apiFetch('/api/employees/'+id);
    if(!r) return;
    var e = await r.json();
    document.getElementById('empEditId').value = e.id;
    document.getElementById('empModalTitle').textContent = 'Edit — '+e.first_name+' '+e.last_name;
    document.getElementById('empModalBtn').textContent = 'Save Changes';
    document.getElementById('empFN').value  = e.first_name||'';
    document.getElementById('empLN').value  = e.last_name||'';
    document.getElementById('empEM').value  = e.email||'';
    document.getElementById('empPH').value  = e.phone||'';
    document.getElementById('empRL').value  = e.role||'';
    document.getElementById('empDP').value  = e.department||'';
    document.getElementById('empET').value  = e.employment_type||'full_time';
    document.getElementById('empSD').value  = e.start_date ? e.start_date.split('T')[0].split(' ')[0] : '';
    document.getElementById('empPR').value  = e.pay_rate||'';
    document.getElementById('empPT').value  = e.pay_type||'hourly';
    document.getElementById('empLO').value  = e.location||'';
    // Map system role back to department dropdown option
    var roleToLabel = {admin:'Admin', hr_staff:'HR Staff', employee:'Employee'};
    document.getElementById('empDP').value = roleToLabel[e.user_role] || e.department || '';
    document.getElementById('empPW').value   = '';
    document.getElementById('empPWLabel').textContent = 'New Password (leave blank to keep)';
    document.getElementById('empPW').required = false;
    openModal('addEmpModal');
}

async function deleteEmployee(id, name, evt){
    if(evt) evt.stopPropagation();
    if(!confirm('Remove '+name+' from the system? This cannot be undone.')) return;
    var r = await apiFetch('/api/employees/'+id, {method:'DELETE'});
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||'Delete failed','error'); return; }
    toast(name+' removed.');
    loadEmployees();
}

async function saveEmployee(){
    var fn = document.getElementById('empFN').value.trim();
    var ln = document.getElementById('empLN').value.trim();
    var em = document.getElementById('empEM').value.trim();
    var rl = document.getElementById('empRL').value.trim();
    var editId = document.getElementById('empEditId').value;
    var isEdit = !!editId;
    if(!fn||!ln){ toast('First and last name are required','error'); return; }
    if(!em){ toast('Email is required','error'); return; }
    if(!rl){ toast('Role / Job Title is required','error'); return; }
    var dept = document.getElementById('empDP').value;
    if(!dept){ toast('Department / access level is required','error'); return; }
    var payload = {
        first_name:      fn,
        last_name:       ln,
        email:           em,
        phone:           document.getElementById('empPH').value||null,
        role:            rl,
        department:      dept,
        employment_type: document.getElementById('empET').value,
        start_date:      document.getElementById('empSD').value||null,
        pay_rate:        document.getElementById('empPR').value ? parseFloat(document.getElementById('empPR').value) : null,
        pay_type:        document.getElementById('empPT').value,
        location:        document.getElementById('empLO').value||null,
    };
    var pw = document.getElementById('empPW').value;
    if(!isEdit && !pw){ toast('Password is required','error'); return; }
    if(pw) payload.password = pw;
    var url    = isEdit ? '/api/employees/'+editId : '/api/employees';
    var method = isEdit ? 'PUT' : 'POST';
    var r = await apiFetch(url, {method:method, body:JSON.stringify(payload)});
    if(!r) return;
    if(!r.ok){
        var err = await r.json();
        toast(err.message || (err.errors ? Object.values(err.errors).flat()[0] : 'Save failed'), 'error');
        return;
    }
    var res = await r.json();
    if(isEdit){
        toast(fn+' '+ln+' updated!');
    } else {
        var msg = fn+' '+ln+' added!';
        if(res.temp_password) msg += ' Temp password: '+res.temp_password;
        toast(msg, 'info', 8000);
    }
    closeModal('addEmpModal');
    loadEmployees();
}
</script>
@endpush
