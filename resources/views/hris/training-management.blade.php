@extends('layouts.app')
@section('title','McCrory Center — '.$pageTitle)
@section('content')
<div class="animate-in">
  <div class="section-title">{{ $pageTitle }}</div>
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">{{ $pageDescription }}</p>

  <div style="display:flex;gap:8px;margin-bottom:12px;align-items:center;flex-wrap:wrap">
    <button class="btn btn-secondary btn-sm" onclick="loadTrainingsPage('all')">All</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainingsPage('pending')">Pending</button>
    <button class="btn btn-secondary btn-sm" onclick="loadTrainingsPage('expiring')">Expiring Soon</button>
    <span style="flex:1"></span>
    <button class="btn btn-primary btn-sm" onclick="openAddTrainingHr()">+ Add Training</button>
  </div>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Training</th><th>Employee</th><th>Due Date</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="hrTrainTbody"><tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr></tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="modal-hrTrainEdit" onclick="if(event.target===this)closeModal('hrTrainEdit')">
  <div class="modal" style="max-width:380px">
    <div class="modal-header"><h3>Edit Training</h3><button onclick="closeModal('hrTrainEdit')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="hrTrainEditId">
      <div class="form-group"><label>Training Name *</label><input type="text" id="hrTrainEditName"></div>
      <div class="form-group"><label>Due Date</label><input type="date" id="hrTrainEditDueDate"></div>
      <div class="form-group">
        <label>Current File</label>
        <div id="hrTrainEditCurrentFile" style="font-size:12px;color:var(--text2)">No file attached.</div>
      </div>
      <div class="form-group"><label>Replace File (Optional)</label><input type="file" id="hrTrainEditFile"></div>
      <div class="form-group" style="display:flex;align-items:center;gap:8px">
        <input type="checkbox" id="hrTrainEditRemoveFile" style="width:auto;height:auto">
        <label for="hrTrainEditRemoveFile" style="margin:0">Remove existing file</label>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('hrTrainEdit')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditTrainingHr()">Save Changes</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-hrTrainAdd" onclick="if(event.target===this)closeModal('hrTrainAdd')">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><h3>Add Training</h3><button onclick="closeModal('hrTrainAdd')">✕</button></div>
    <div class="modal-body">
      <div class="form-group">
        <label>Employee *</label>
        <select id="hrTrainEmployeeId"><option value="">Loading...</option></select>
      </div>
      <div class="form-group"><label>Training Name *</label><input type="text" id="hrTrainAddName"></div>
      <div class="form-group"><label>Due Date</label><input type="date" id="hrTrainAddDueDate"></div>
      <div class="form-group"><label>Attachment (Optional, one file)</label><input type="file" id="hrTrainAddFile"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('hrTrainAdd')">Cancel</button>
      <button class="btn btn-primary" onclick="saveAddTrainingHr()">Add Training</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-hrTrainView" onclick="if(event.target===this)closeModal('hrTrainView')">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3>Training Details</h3><button onclick="closeModal('hrTrainView')">✕</button></div>
    <div class="modal-body" id="hrTrainViewBody" style="font-size:13px;color:var(--text2)">Loading...</div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('hrTrainView')">Close</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _dwcOnly = @json($dwcOnly);
var _hrEmployees = [];

function trainingStatusInfo(training){
  var done = !!training.is_completed;
  var overdue = !done && training.due_date && new Date(training.due_date) < new Date();
  var status = done ? 'Complete' : overdue ? 'Overdue' : 'Pending';
  var color = done ? 'var(--green)' : overdue ? 'var(--red)' : 'var(--yellow,#d4ac0d)';
  return { text: status, color: color };
}

async function pageRefresh(){ await loadTrainingsPage('all'); }

document.addEventListener('DOMContentLoaded', async function(){
    await loadTrainingEmployees();
    loadTrainingsPage('all');
});

async function loadTrainingEmployees(){
    var sel = document.getElementById('hrTrainEmployeeId');
    if(!sel) return;
    var r = await apiFetch('/api/employees?per_page=200');
    if(!r || !r.ok){ sel.innerHTML = '<option value="">No employees found</option>'; return; }
    var data = await r.json();
    _hrEmployees = data.data || [];
    sel.innerHTML = '<option value="">Select Employee</option>' + _hrEmployees.map(function(e){
        return '<option value="'+e.id+'">'+esc((e.first_name||'')+' '+(e.last_name||''))+'</option>';
    }).join('');
}

async function loadTrainingsPage(filter){
    filter = filter || 'all';
    var url = '/api/trainings?per_page=200'+(filter==='expiring'?'&expiring=1':filter==='pending'?'&completed=0':'');
    var r = await apiFetch(url);
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];
    if(_dwcOnly){
        items = items.filter(function(t){ return /dwc/i.test(String(t.name||'')); });
    }
    var tbody = document.getElementById('hrTrainTbody');
    if(!items.length){
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text3)">No trainings found.</td></tr>';
        return;
    }
    tbody.innerHTML = items.map(function(t){
        var emp = t.employee || {};
        var statusInfo = trainingStatusInfo(t);
        return '<tr>'
          +'<td>'+esc(t.name||'—')+'</td>'
          +'<td>'+esc((emp.first_name||'')+' '+(emp.last_name||''))+'</td>'
          +'<td>'+fDate(t.due_date)+'</td>'
          +'<td><span style="font-weight:600;color:'+statusInfo.color+'">'+statusInfo.text+'</span></td>'
          +'<td class="actions-cell">'
            +'<button class="btn btn-secondary btn-sm" onclick="openViewTrainingHr('+t.id+')">👁 View</button>'
            +(t.is_completed ? '<button class="btn btn-secondary btn-sm" onclick="toggleTrainingHr('+t.id+', false)">↺ Reopen</button>' : '<button class="btn btn-success btn-sm" onclick="toggleTrainingHr('+t.id+', true)">✓ Done</button>')
            +'<button class="btn btn-warning btn-sm" onclick="openEditTrainingHr('+t.id+')">✏ Edit</button>'
            +'<button class="btn btn-danger btn-sm" onclick="deleteTrainingHr('+t.id+',\''+esc(t.name)+'\')">🗑</button>'
          +'</td>'
        +'</tr>';
    }).join('');
}

function openAddTrainingHr(){
    document.getElementById('hrTrainAddName').value = _dwcOnly ? 'DWC Training' : '';
    document.getElementById('hrTrainAddDueDate').value = '';
    document.getElementById('hrTrainEmployeeId').value = '';
  document.getElementById('hrTrainAddFile').value = '';
    openModal('hrTrainAdd');
}

async function saveAddTrainingHr(){
    var employeeId = document.getElementById('hrTrainEmployeeId').value;
    var name = document.getElementById('hrTrainAddName').value.trim();
    var dueDate = document.getElementById('hrTrainAddDueDate').value;
  var file = document.getElementById('hrTrainAddFile').files[0];
    if(!employeeId){ toast('Select an employee.','error'); return; }
    if(!name){ toast('Training name is required.','error'); return; }
  var body = new FormData();
  body.append('employee_id', String(parseInt(employeeId,10)));
  body.append('name', name);
  if(dueDate){ body.append('due_date', dueDate); }
  if(file){ body.append('file', file); }
  var r = await apiFetch('/api/trainings', {method:'POST', body:body});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Failed to add training','error'); return; }
    closeModal('hrTrainAdd');
    toast('Training added successfully.','success');
    loadTrainingsPage('all');
}

async function openEditTrainingHr(id){
  var r = await apiFetch('/api/trainings/'+id);
  if(!r || !r.ok){ toast('Failed to load training details','error'); return; }
  var training = await r.json();
    document.getElementById('hrTrainEditId').value = id;
  document.getElementById('hrTrainEditName').value = training.name || '';
  document.getElementById('hrTrainEditDueDate').value = training.due_date ? String(training.due_date).split('T')[0].split(' ')[0] : '';
  document.getElementById('hrTrainEditFile').value = '';
  document.getElementById('hrTrainEditRemoveFile').checked = false;
  document.getElementById('hrTrainEditCurrentFile').innerHTML = training.certificate_url
    ? '<a href="'+esc(training.certificate_url)+'" target="_blank" rel="noopener">Open current file</a>'
    : 'No file attached.';
    openModal('hrTrainEdit');
}

async function saveEditTrainingHr(){
    var id = document.getElementById('hrTrainEditId').value;
    var name = document.getElementById('hrTrainEditName').value.trim();
    var dueDate = document.getElementById('hrTrainEditDueDate').value;
  var file = document.getElementById('hrTrainEditFile').files[0];
  var removeFile = document.getElementById('hrTrainEditRemoveFile').checked;
    if(!name){ toast('Name is required','error'); return; }
  var body = new FormData();
  body.append('name', name);
  body.append('due_date', dueDate || '');
  if(file){ body.append('file', file); }
  if(removeFile){ body.append('remove_certificate', '1'); }
  var r = await apiFetch('/api/trainings/'+id, {method:'PATCH', body:body});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Update failed','error'); return; }
    closeModal('hrTrainEdit');
    toast('Training updated!');
    loadTrainingsPage('all');
}

async function openViewTrainingHr(id){
  openModal('hrTrainView');
  var body = document.getElementById('hrTrainViewBody');
  body.innerHTML = 'Loading...';

  var r = await apiFetch('/api/trainings/'+id);
  if(!r || !r.ok){ body.innerHTML = '<span style="color:var(--red)">Failed to load training details.</span>'; return; }
  var t = await r.json();
  var emp = t.employee || {};
  var employeeName = ((emp.first_name||'')+' '+(emp.last_name||'')).trim() || '—';
  var statusInfo = trainingStatusInfo(t);

  body.innerHTML = ''
    + '<div style="display:grid;grid-template-columns:140px 1fr;gap:10px 14px">'
    + '<div style="color:var(--text3)">Training</div><div>'+esc(t.name||'—')+'</div>'
    + '<div style="color:var(--text3)">Employee</div><div>'+esc(employeeName)+'</div>'
    + '<div style="color:var(--text3)">Due Date</div><div>'+fDate(t.due_date)+'</div>'
    + '<div style="color:var(--text3)">Completed Date</div><div>'+fDate(t.completed_date)+'</div>'
    + '<div style="color:var(--text3)">Status</div><div><span style="font-weight:600;color:'+statusInfo.color+'">'+statusInfo.text+'</span></div>'
    + '<div style="color:var(--text3)">Attachment</div><div>'
    + (t.certificate_url ? '<a href="'+esc(t.certificate_url)+'" target="_blank" rel="noopener">Open uploaded file</a>' : 'No file uploaded')
    + '</div>'
    + '</div>';
}

async function toggleTrainingHr(id, completed){
    var r = await apiFetch('/api/trainings/'+id, {method:'PATCH', body:JSON.stringify({is_completed: completed})});
    if(!r || !r.ok){ toast('Failed to update training','error'); return; }
    toast(completed ? 'Training marked complete!' : 'Training reopened.');
    loadTrainingsPage('all');
}

async function deleteTrainingHr(id, name){
    if(!confirm('Delete training "'+name+'"? This cannot be undone.')) return;
    var r = await apiFetch('/api/trainings/'+id, {method:'DELETE'});
    if(!r || !r.ok){ toast('Delete failed','error'); return; }
    toast('Training deleted.');
    loadTrainingsPage('all');
}
</script>
@endpush