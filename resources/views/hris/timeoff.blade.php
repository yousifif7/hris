@extends('layouts.app')
@section('title','McCrory Center — Time Off')
@section('content')
<div class="animate-in">
  <div style="display:flex;gap:8px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
    <h3 style="font-weight:700;font-size:15px">Time-Off Requests</h3>
    <span style="flex:1"></span>
    <select id="toFilter" onchange="loadTimeOff()" style="width:140px">
      <option value="">All</option>
      <option value="pending">Pending</option>
      <option value="approved">Approved</option>
      <option value="denied">Denied</option>
    </select>
    <button class="btn btn-primary btn-sm" onclick="openModal('toNewModal')">+ New Request</button>
  </div>
  <div class="table-wrap">
    <table><thead><tr><th>Employee</th><th>Type</th><th>Start</th><th>End</th><th>Days</th><th>Reason</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="toTbody"><tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr></tbody></table>
  </div>
</div>

<!-- New Request Modal -->
<div class="modal-overlay" id="modal-toNewModal" onclick="if(event.target===this)closeModal('toNewModal')">
  <div class="modal" style="max-width:440px">
    <div class="modal-header"><h3>New Time-Off Request</h3><button onclick="closeModal('toNewModal')">✕</button></div>
    <div class="modal-body">
      <div class="form-group"><label>Employee</label><select id="toEmployee"><option value="">Loading...</option></select></div>
      <div class="form-group"><label>Type</label>
        <select id="toType">
          <option value="Vacation">Vacation</option>
          <option value="Sick">Sick</option>
          <option value="Personal">Personal</option>
          <option value="Bereavement">Bereavement</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group"><label>Start Date</label><input type="date" id="toStart"></div>
        <div class="form-group"><label>End Date</label><input type="date" id="toEnd"></div>
      </div>
      <div class="form-group"><label>Notes (optional)</label><textarea id="toNotes" rows="2" placeholder="Brief reason..."></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('toNewModal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitTimeOff()">Submit Request</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _toRequests = [];

async function pageRefresh(){ await loadTimeOff(); }

async function loadTimeOff(){
    var status = document.getElementById('toFilter').value;
    var url = '/api/time-off?per_page=60'+(status?'&status='+status:'');
    var r = await apiFetch(url);
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];
    _toRequests = items;
    var tbody = document.getElementById('toTbody');
    if(!items.length){
        tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text3)">No requests found.</td></tr>';
        return;
    }
    tbody.innerHTML = items.map(function(req){
        var emp = req.employee||{};
        var s   = req.status||'pending';
        var colMap = {approved:'offer-accepted',denied:'rejected',pending:'offer-sent'};
        var col = colMap[s]||'queue';
        // Calculate days
        var days = '—';
        if(req.start_date&&req.end_date){
            var ms = new Date(req.end_date)-new Date(req.start_date);
            days = Math.round(ms/86400000)+1;
        }
        return '<tr>'
          +'<td><div class="candidate-name">'+esc((emp.first_name||'')+' '+(emp.last_name||''))+'</div></td>'
          +'<td>'+esc(req.type||'—')+'</td>'
          +'<td>'+fDate(req.start_date)+'</td>'
          +'<td>'+fDate(req.end_date)+'</td>'
          +'<td>'+days+'</td>'
          +'<td style="max-width:180px;white-space:normal">'+esc(req.notes||'—')+'</td>'
          +'<td><span class="badge badge-'+col+'">'+s+'</span></td>'
          +'<td class="actions-cell">'
            +(s==='pending'
              ?'<button class="btn btn-success btn-sm" onclick="reviewTO('+req.id+',\'approved\')">✓ Approve</button>'
               +'<button class="btn btn-danger btn-sm" onclick="reviewTO('+req.id+',\'denied\')">✗ Deny</button>'
              :'')
            +'<button class="btn btn-warning btn-sm" onclick="openEditTO('+req.id+')">✏ Edit</button>'
            +'<button class="btn btn-danger btn-sm" onclick="deleteTO('+req.id+')">🗑</button>'
          +'</td>'
        +'</tr>';
    }).join('');
}

async function reviewTO(id, action){
    var msgs = { approved: 'Approve this time-off request?', denied: 'Deny this time-off request?' };
    if(!confirm(msgs[action] || 'Continue?')) return;
    var r = await apiFetch('/api/time-off/'+id+'/review', {method:'PATCH', body:JSON.stringify({status:action})});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Action failed','error'); return; }
    toast(action==='approved' ? '✓ Time-off approved!' : '✗ Time-off denied.', action==='approved'?'success':'error');
    loadTimeOff();
}

async function submitTimeOff(){
    var empId = document.getElementById('toEmployee').value;
    var start = document.getElementById('toStart').value;
    var end   = document.getElementById('toEnd').value;
    if(!empId){ toast('Please select an employee.','error'); return; }
    if(!start||!end){ toast('Please fill start and end dates.','error'); return; }
    var days = Math.round((new Date(end)-new Date(start))/86400000)+1;
    var payload = {
        employee_id: parseInt(empId),
        type:        document.getElementById('toType').value,
        start_date:  start,
        end_date:    end,
        days:        days,
        notes:       document.getElementById('toNotes').value
    };
    var r = await apiFetch('/api/time-off', {method:'POST', body:JSON.stringify(payload)});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Failed to submit','error'); return; }
    toast('Request submitted!');
    closeModal('toNewModal');
    loadTimeOff();
}

async function populateEmpPicker(){
    var r = await apiFetch('/api/employees?per_page=100');
    if(!r) return;
    var data = await r.json();
    var sel = document.getElementById('toEmployee');
    sel.innerHTML = '<option value="">-- Select Employee --</option>';
    (data.data||[]).forEach(function(e){
        var o = document.createElement('option');
        o.value = e.id; o.textContent = e.first_name+' '+e.last_name;
        sel.appendChild(o);
    });
}


function openEditTO(id){
    var req = _toRequests.find(function(x){ return x.id===id; });
    if(!req) return;
    document.getElementById('editToId').value    = id;
    document.getElementById('editToType').value  = req.type||'Vacation';
    document.getElementById('editToStart').value = req.start_date ? req.start_date.split('T')[0].split(' ')[0] : '';
    document.getElementById('editToEnd').value   = req.end_date   ? req.end_date.split('T')[0].split(' ')[0]   : '';
    document.getElementById('editToNotes').value = req.notes||'';
    openModal('editToModal');
}

async function saveEditTO(){
    var id    = document.getElementById('editToId').value;
    var start = document.getElementById('editToStart').value;
    var end   = document.getElementById('editToEnd').value;
    if(!start||!end){ toast('Start and end dates are required','error'); return; }
    var body = {
        type:       document.getElementById('editToType').value,
        start_date: start,
        end_date:   end,
        notes:      document.getElementById('editToNotes').value||null,
    };
    var r = await apiFetch('/api/time-off/'+id, {method:'PATCH', body:JSON.stringify(body)});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Update failed','error'); return; }
    closeModal('editToModal');
    toast('Request updated!');
    loadTimeOff();
}

async function deleteTO(id){
    if(!confirm('Delete this time-off request? This cannot be undone.')) return;
    var r = await apiFetch('/api/time-off/'+id, {method:'DELETE'});
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Delete failed','error'); return; }
    toast('Time-off request deleted.');
    loadTimeOff();
}

document.addEventListener('DOMContentLoaded', function(){ loadTimeOff(); populateEmpPicker(); });
</script>

<!-- Edit Time-Off Modal -->
<div class="modal-overlay" id="modal-editToModal" onclick="if(event.target===this)closeModal('editToModal')">
  <div class="modal" style="max-width:420px">
    <div class="modal-header"><h3>Edit Time-Off Request</h3><button onclick="closeModal('editToModal')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="editToId">
      <div class="form-group"><label>Type</label>
        <select id="editToType">
          <option value="Vacation">Vacation</option>
          <option value="Sick">Sick</option>
          <option value="Personal">Personal</option>
          <option value="Bereavement">Bereavement</option>
        </select>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group"><label>Start Date</label><input type="date" id="editToStart"></div>
        <div class="form-group"><label>End Date</label><input type="date" id="editToEnd"></div>
      </div>
      <div class="form-group"><label>Notes</label><textarea id="editToNotes" rows="2"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('editToModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditTO()">Save Changes</button>
    </div>
  </div>
</div>
@endpush
