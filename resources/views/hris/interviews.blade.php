@extends('layouts.app')
@section('title','McCrory Center — Interviews')
@section('content')
<div class="animate-in">
  <div style="display:flex;gap:8px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
    <select id="statusFilter" onchange="loadInterviews()" style="width:160px">
      <option value="">All Interviews</option>
      <option value="upcoming" selected>Upcoming</option>
      <option value="past">Past</option>
      <option value="scheduled">Scheduled</option>
      <option value="completed">Completed</option>
      <option value="cancelled">Cancelled</option>
      <option value="no_show">No Show</option>
    </select>
    <input id="intSearch" placeholder="Search candidate..." style="width:200px" oninput="filterTable()">
    <span style="flex:1"></span>
    <button class="btn btn-primary btn-sm" onclick="openScheduleInterviewPick()">+ Schedule Interview</button>
  </div>

  <div class="table-wrap">
    <div class="table-header">
      <h3 id="intTableTitle">All Interviews</h3>
      <span id="intCount" style="font-size:12px;color:var(--text3)"></span>
    </div>
    <table>
      <thead><tr><th>Candidate</th><th>Category</th><th>Scheduled</th><th>Type</th><th>Duration</th><th>Interviewer</th><th>Link</th><th>Status</th><th>Notes</th><th>Actions</th></tr></thead>
      <tbody id="intTableBody"><tr><td colspan="10" style="text-align:center;padding:32px;color:var(--text3)">⏳ Loading...</td></tr></tbody>
    </table>
  </div>
</div>

<!-- Edit Interview Modal -->
<div class="modal-overlay" id="modal-editInterview" onclick="if(event.target===this)closeModal('editInterview')">
  <div class="modal" style="max-width:500px">
    <div class="modal-header"><h3>Edit Interview</h3><button onclick="closeModal('editInterview')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="editIntId">
      <div class="form-group"><label>Date & Time</label><input type="datetime-local" id="editIntDate"></div>
      <div class="form-row">
        <div class="form-group"><label>Duration (min)</label><input type="number" id="editIntDur" min="5" max="180"></div>
        <div class="form-group"><label>Type</label>
          <select id="editIntType">
            <option value="zoom">Zoom</option>
            <option value="in_person">In-Person</option>
            <option value="phone">Phone</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Meeting Link</label><input id="editIntLink" placeholder="https://zoom.us/j/..."></div>
      <div class="form-group"><label>Status</label>
        <select id="editIntStatus">
          <option value="scheduled">Scheduled</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
          <option value="no_show">No Show</option>
        </select>
      </div>
      <div class="form-group"><label>Notes</label><textarea id="editIntNotes" rows="3"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('editInterview')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditInterview()">Save Changes</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _allInterviews = [];

async function pageRefresh(){ await loadInterviews(); }

async function loadInterviews(){
    var filter = document.getElementById('statusFilter').value;
    var param  = filter ? '&status=' + filter : '';
    var r = await apiFetch('/api/interviews?per_page=200' + param);
    if(!r) return;
    var data = await r.json();
    _allInterviews = data.data || [];
    filterTable();
}

function filterTable(){
    var q = document.getElementById('intSearch').value.toLowerCase().trim();
    var items = q
        ? _allInterviews.filter(function(i){
            var c = i.candidate || {};
            return (c.first_name+' '+c.last_name).toLowerCase().includes(q)
                || (c.email||'').toLowerCase().includes(q);
          })
        : _allInterviews;

    var filter = document.getElementById('statusFilter').value;
    document.getElementById('intTableTitle').textContent = filter ? capFirst(filter.replace('_',' '))+' Interviews' : 'All Interviews';
    document.getElementById('intCount').textContent = items.length + ' record' + (items.length===1?'':'s');

    var tbody = document.getElementById('intTableBody');
    if(!items.length){
        tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:32px;color:var(--text3)">No interviews found.</td></tr>';
        return;
    }

    var stBadge = {scheduled:'invite-sent', completed:'offer-accepted', cancelled:'rejected', no_show:'rejected'};
    tbody.innerHTML = items.map(function(i){
        var c    = i.candidate   || {};
        var intvr = i.interviewer || {};
        var col  = stBadge[i.status] || 'queue';
        return '<tr>'
          +'<td><div class="candidate-name">'+esc(c.first_name+' '+c.last_name)+'</div>'
              +(c.email?'<div class="candidate-sub">'+esc(c.email)+'</div>':'')+'</td>'
          +'<td>'+(c.category ? esc(c.category.name) : '—')+'</td>'
          +'<td style="font-size:12px;white-space:nowrap">'+fD(i.scheduled_at)+'</td>'
          +'<td style="text-transform:capitalize">'+esc(i.type||'zoom')+'</td>'
          +'<td style="text-align:center">'+(i.duration_minutes||20)+' min</td>'
          +'<td>'+(intvr.first_name ? esc(intvr.first_name+' '+intvr.last_name) : '—')+'</td>'
          +'<td>'+(i.meeting_link
              ? '<a href="'+esc(i.meeting_link)+'" target="_blank" rel="noopener" class="btn btn-blue btn-sm">Join</a>'
              : '—')+'</td>'
          +'<td><span class="badge badge-'+col+'">'+esc(i.status)+'</span></td>'
          +'<td style="max-width:160px;white-space:normal;font-size:12px;color:var(--text2)">'+esc(i.notes||'—')+'</td>'
          +'<td class="actions-cell">'
            +(i.status==='scheduled'
              ? '<button class="btn btn-success btn-sm" onclick="completeInterview('+i.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">✓ Complete</button>'
               +'<button class="btn btn-warning btn-sm" onclick="openEditInterview('+i.id+')">✏ Edit</button>'
               +'<button class="btn btn-danger btn-sm" onclick="deleteInterview('+i.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">🗑</button>'
              : '<button class="btn btn-warning btn-sm" onclick="openEditInterview('+i.id+')">✏ Edit</button>'
               +'<button class="btn btn-danger btn-sm" onclick="deleteInterview('+i.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">🗑</button>')
            +(c.id ? '<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Profile</button>' : '')
          +'</td></tr>';
    }).join('');
}

function capFirst(s){ return s.charAt(0).toUpperCase()+s.slice(1); }

async function completeInterview(id, name){
    if(!confirm('Mark interview with '+name+' as complete?\n\nThis will move them to Post-Interview Review.')) return;
    var notes = prompt('Add interview notes (optional):') || '';
    var r = await apiFetch('/api/interviews/'+id+'/complete', {method:'PATCH', body:JSON.stringify({notes:notes, question_responses:null})});
    if(!r) return;
    toast('✓ Interview completed — '+name+' moved to Post-Interview Review', 'success');
    loadInterviews();
}

function openEditInterview(id){
    var i = _allInterviews.find(function(x){ return x.id===id; });
    if(!i) return;
    document.getElementById('editIntId').value        = i.id;
    document.getElementById('editIntDate').value      = i.scheduled_at ? i.scheduled_at.replace(' ','T').substring(0,16) : '';
    document.getElementById('editIntDur').value       = i.duration_minutes||20;
    document.getElementById('editIntType').value      = i.type||'zoom';
    document.getElementById('editIntLink').value      = i.meeting_link||'';
    document.getElementById('editIntStatus').value    = i.status||'scheduled';
    document.getElementById('editIntNotes').value     = i.notes||'';
    openModal('editInterview');
}

async function saveEditInterview(){
    var id = document.getElementById('editIntId').value;
    var dt = document.getElementById('editIntDate').value;
    if(!dt){ toast('Please select a date and time','error'); return; }
    var body = {
        scheduled_at:     dt.replace('T',' ')+':00',
        duration_minutes: +document.getElementById('editIntDur').value||20,
        type:             document.getElementById('editIntType').value,
        meeting_link:     document.getElementById('editIntLink').value||null,
        status:           document.getElementById('editIntStatus').value,
        notes:            document.getElementById('editIntNotes').value||null,
    };
    var r = await apiFetch('/api/interviews/'+id, {method:'PATCH', body:JSON.stringify(body)});
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||'Update failed','error'); return; }
    closeModal('editInterview');
    toast('Interview updated!');
    loadInterviews();
}

async function deleteInterview(id, name){
    if(!confirm('Delete the interview with '+name+'? This cannot be undone.')) return;
    var r = await apiFetch('/api/interviews/'+id, {method:'DELETE'});
    if(!r) return;
    toast('Interview deleted.');
    loadInterviews();
}

document.addEventListener('DOMContentLoaded', loadInterviews);
</script>
@endpush
