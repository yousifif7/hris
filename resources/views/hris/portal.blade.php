@extends('layouts.app')
@section('title','McCrory Center — Employee Portal')
@section('content')
<div class="animate-in">
  <!-- My Info Banner -->
  <div class="card-section" id="portalBanner" style="display:flex;align-items:center;gap:16px;margin-bottom:20px">
    <div id="portalAvatar" style="width:52px;height:52px;border-radius:50%;background:#6c63ff;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700">?</div>
    <div>
      <div id="portalName" style="font-size:17px;font-weight:700">Loading...</div>
      <div id="portalRole" style="font-size:13px;color:var(--text3)"></div>
    </div>
  </div>

  <!-- Quick tiles -->
  <div class="section-title">Quick Actions</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:12px;margin-bottom:24px">
    <a href="/hris/timeoff"     class="portal-tile">🏖 Time Off</a>
    <a href="/hris/employee"  class="portal-tile">📋 Directory</a>
    <div class="portal-tile" onclick="openDocUpload()">📎 Upload Doc</div>
    <div class="portal-tile" onclick="loadTrainings()">📚 Trainings</div>
    <a href="/hris/settings"   class="portal-tile">⚙ Settings</a>
  </div>

  <!-- Trainings -->
  <div class="section-title">📚 Trainings &amp; Certifications</div>
  <div style="display:flex;gap:8px;margin-bottom:12px">
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('all')">All</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('pending')">Pending</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainings('expiring')">Expiring Soon</button>
    <button class="btn btn-primary btn-sm" onclick="openAddTrainingPortal()">+ Add Training</button>
  </div>
  <div class="table-wrap">
    <table><thead><tr><th>Training</th><th>Employee</th><th>Category</th><th>Due Date</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="trainTbody"><tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr></tbody></table>
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
var _portalEmployees = [];

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
      await loadPortalEmployees();
    loadTrainings('all');
});

    async function loadPortalEmployees(){
      var sel = document.getElementById('ptAddEmployeeId');
      if(!sel) return;

      var r = await apiFetch('/api/employees?per_page=200');
      if(!r || !r.ok){
        _portalEmployees = [];
        sel.innerHTML = '<option value="">No employee list access</option>';
        if(_myEmployeeId){
          sel.innerHTML = '<option value="'+_myEmployeeId+'">My Employee Profile</option>';
        }
        return;
      }

      var json = await r.json();
      _portalEmployees = json.data || [];
      if(!_portalEmployees.length){
        sel.innerHTML = '<option value="">No employees found</option>';
        return;
      }

      sel.innerHTML = '<option value="">Select Employee</option>' + _portalEmployees.map(function(e){
        var fullName = ((e.first_name||'')+' '+(e.last_name||'')).trim();
        return '<option value="'+e.id+'">'+esc(fullName || 'Employee #'+e.id)+'</option>';
      }).join('');

      if(_myEmployeeId){
        sel.value = String(_myEmployeeId);
      }
    }

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
          +'<td class="actions-cell">'
            +(done ? '<button class="btn btn-secondary btn-sm" onclick="toggleTrainingStatus('+t.id+', false)">↺ Reopen</button>' : '<button class="btn btn-success btn-sm" onclick="toggleTrainingStatus('+t.id+', true)">✓ Done</button>')
            +'<button class="btn btn-warning btn-sm" onclick="openEditTrainingPortal('+t.id+',\''+esc(t.name)+'\',\''+esc(t.due_date||'')+'\')" >✏ Edit</button>'
            +'<button class="btn btn-danger btn-sm" onclick="deleteTrainingPortal('+t.id+',\''+esc(t.name)+'\')" >🗑</button>'
          +'</td>'
        +'</tr>';
    }).join('');
}

async function completeTraining(id){
  return toggleTrainingStatus(id, true);
}

async function toggleTrainingStatus(id, completed){
  var r = await apiFetch('/api/trainings/'+id, {method:'PATCH', body: JSON.stringify({is_completed: completed})});
  if(!r || !r.ok){ toast('Failed to update training','error'); return; }
  toast(completed ? 'Training marked complete!' : 'Training reopened.');
  loadTrainings('all');
}

var _portalTrainings = [];

function openEditTrainingPortal(id, name, dueDate){
    document.getElementById('ptEditId').value       = id;
    document.getElementById('ptEditName').value     = name;
    document.getElementById('ptEditDueDate').value  = dueDate ? dueDate.split('T')[0].split(' ')[0] : '';
    openModal('ptEditModal');
}

async function saveEditTrainingPortal(){
    var id      = document.getElementById('ptEditId').value;
    var name    = document.getElementById('ptEditName').value.trim();
    var dueDate = document.getElementById('ptEditDueDate').value;
    if(!name){ toast('Name is required','error'); return; }
    var r = await apiFetch('/api/trainings/'+id, {method:'PATCH', body:JSON.stringify({name:name, due_date:dueDate||null})});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Update failed','error'); return; }
    closeModal('ptEditModal');
    toast('Training updated!');
    loadTrainings('all');
}

async function deleteTrainingPortal(id, name){
    if(!confirm('Delete training "'+name+'"? This cannot be undone.')) return;
    var r = await apiFetch('/api/trainings/'+id, {method:'DELETE'});
    if(!r || !r.ok){ toast('Delete failed','error'); return; }
    toast('Training deleted.');
    loadTrainings('all');
}

function openAddTrainingPortal(){
  document.getElementById('ptAddName').value = '';
  document.getElementById('ptAddDueDate').value = '';
  if(_myEmployeeId){
    document.getElementById('ptAddEmployeeId').value = String(_myEmployeeId);
  }
  openModal('ptAddModal');
}

async function saveAddTrainingPortal(){
  var employeeId = document.getElementById('ptAddEmployeeId').value;
  var name = document.getElementById('ptAddName').value.trim();
  var dueDate = document.getElementById('ptAddDueDate').value;

  if(!employeeId){ toast('Select an employee.','error'); return; }
  if(!name){ toast('Training name is required.','error'); return; }

  var payload = {
    employee_id: parseInt(employeeId, 10),
    name: name,
    due_date: dueDate || null,
  };

  var r = await apiFetch('/api/trainings', { method:'POST', body: JSON.stringify(payload) });
  if(!r || !r.ok){
    var err = r ? await r.json() : {};
    toast(err.message || 'Failed to add training', 'error');
    return;
  }

  closeModal('ptAddModal');
  toast('Training added successfully.', 'success');
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

<!-- Edit Training Modal -->
<div class="modal-overlay" id="modal-ptEditModal" onclick="if(event.target===this)closeModal('ptEditModal')">
  <div class="modal" style="max-width:380px">
    <div class="modal-header"><h3>Edit Training</h3><button onclick="closeModal('ptEditModal')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="ptEditId">
      <div class="form-group"><label>Training Name *</label><input type="text" id="ptEditName"></div>
      <div class="form-group"><label>Due Date</label><input type="date" id="ptEditDueDate"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('ptEditModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditTrainingPortal()">Save Changes</button>
    </div>
  </div>
</div>

<!-- Add Training Modal -->
<div class="modal-overlay" id="modal-ptAddModal" onclick="if(event.target===this)closeModal('ptAddModal')">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><h3>Add Training</h3><button onclick="closeModal('ptAddModal')">✕</button></div>
    <div class="modal-body">
      <div class="form-group">
        <label>Employee *</label>
        <select id="ptAddEmployeeId"><option value="">Loading...</option></select>
      </div>
      <div class="form-group"><label>Training Name *</label><input type="text" id="ptAddName"></div>
      <div class="form-group"><label>Due Date</label><input type="date" id="ptAddDueDate"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('ptAddModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveAddTrainingPortal()">Add Training</button>
    </div>
  </div>
</div>
@endpush
