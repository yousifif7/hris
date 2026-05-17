@extends('layouts.app')
@section('title','McCrory Center - '.$stageTitle)
@section('content')
<div class="animate-in">
  <div class="section-title">{{ $stageTitle }}</div>
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">{{ $stageDescription }}</p>
  <div id="workflowList"><div style="text-align:center;padding:60px;color:var(--text3)">Loading...</div></div>
</div>

<div class="modal-overlay" id="modal-wfTaskModal" onclick="if(event.target===this)closeModal('wfTaskModal')">
  <div class="modal" style="max-width:500px">
    <div class="modal-header">
      <h3 id="wfTaskTitle" style="font-size:15px"></h3>
      <button onclick="closeModal('wfTaskModal')">x</button>
    </div>
    <div class="modal-body" style="display:flex;flex-direction:column;gap:14px">
      <div id="wfTaskStatus"></div>
      <div class="form-group" style="margin-bottom:0">
        <label>Upload Document (optional)</label>
        <input type="file" id="wfTaskFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>
      <div id="wfTaskDoc" style="display:none;font-size:12px"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('wfTaskModal')">Cancel</button>
      <button id="wfTaskToggleBtn" class="btn btn-success" onclick="toggleWorkflowTask()">Mark Complete</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _stageTaskNames = @json($stageTaskNames);
var _stageStatus = @json($stageStatus);
var _wfData = {};
var _wfCurrentTask = null;
var _wfCurrentCandidateId = null;

async function pageRefresh(){ await loadWorkflowStage(); }

async function loadWorkflowStage(){
    var url = '/api/onboarding' + (_stageStatus ? ('?status=' + encodeURIComponent(_stageStatus)) : '');
    var r = await apiFetch(url);
    if(!r) return;
    var payload = await r.json();
    var items = payload.data || payload;
    var list = document.getElementById('workflowList');

    _wfData = {};

    var cards = items.map(function(entry){
        var c = entry.candidate || entry;
        var cid = c.id || entry.candidate_id;
        var allTasks = entry.onboarding_tasks || c.onboarding_tasks || [];
        var stageTasks = allTasks.filter(function(t){
            return _stageTaskNames.indexOf(String(t.task_name || '').trim()) !== -1;
        });

        _wfData[cid] = {
            candidate: c,
            allTasks: allTasks,
            tasks: stageTasks,
        };

        if(!stageTasks.length){
            return '<div class="card-section">'
              +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">'
                +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(cid)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">'+In(c.first_name,c.last_name)+'</div>'
                +'<div style="flex:1"><div style="font-weight:600">'+esc((c.first_name||'')+' '+(c.last_name||''))+'</div><div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'-')+'</div></div>'
              +'</div>'
              +'<div style="font-size:12px;color:var(--text3);margin-bottom:10px">No tasks found for this stage yet.</div>'
              +'<button class="btn btn-secondary btn-sm" onclick="ensureStageTasks('+cid+')">Add Stage Task(s)</button>'
            +'</div>';
        }

        var done = stageTasks.filter(function(t){ return t.is_completed; }).length;
        var total = stageTasks.length;
        var progress = Math.round((done/Math.max(total,1))*100);

        var taskRows = stageTasks.map(function(t){
            return '<div class="checklist-item '+(t.is_completed?'done':'')+'" onclick="openWorkflowTask('+cid+','+t.id+')">'
              +'<div class="checkbox" style="'+(t.is_completed?'background:var(--green);border-color:var(--green)':'')+'"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20,6 9,17 4,12"/></svg></div>'
              +'<div class="text">'+esc(t.task_name)+'</div>'
              +(t.is_completed?'<span style="font-size:11px;color:var(--green)">Completed</span>':'<span style="font-size:11px;color:var(--text3)">Pending</span>')
            +'</div>';
        }).join('');

        return '<div class="card-section">'
          +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">'
            +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(cid)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">'+In(c.first_name,c.last_name)+'</div>'
            +'<div style="flex:1"><div style="font-weight:600">'+esc((c.first_name||'')+' '+(c.last_name||''))+'</div><div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'-')+' - '+done+'/'+total+' complete</div></div>'
            +B(c.status)
          +'</div>'
          +'<div class="progress-wrap"><div class="progress-fill" style="width:'+progress+'%"></div></div>'
          +taskRows
          +'<div style="margin-top:10px;display:flex;gap:8px;flex-wrap:wrap"><button class="btn btn-secondary btn-sm" onclick="viewCandidate('+cid+')">Full Profile</button></div>'
        +'</div>';
    }).join('');

    list.innerHTML = cards || '<div style="text-align:center;padding:60px;color:var(--text3)">No candidates available in this stage.</div>';
}

async function ensureStageTasks(candidateId){
    var r = await apiFetch('/api/onboarding/'+candidateId+'/ensure-tasks', {
        method: 'POST',
        body: JSON.stringify({ task_names: _stageTaskNames })
    });
    if(!r || !r.ok){ toast('Unable to add stage tasks','error'); return; }
    toast('Stage task(s) added.','success');
    loadWorkflowStage();
}

function openWorkflowTask(candidateId, taskId){
    var entry = _wfData[candidateId];
    if(!entry) return;
    var task = entry.allTasks.find(function(t){ return t.id === taskId; });
    if(!task) return;

    _wfCurrentTask = task;
    _wfCurrentCandidateId = candidateId;

    document.getElementById('wfTaskTitle').textContent = task.task_name;
    document.getElementById('wfTaskFile').value = '';

    var status = document.getElementById('wfTaskStatus');
    var btn = document.getElementById('wfTaskToggleBtn');
    if(task.is_completed){
        status.innerHTML = '<span class="badge badge-offer-accepted">Completed</span>';
        btn.textContent = 'Mark Incomplete';
        btn.className = 'btn btn-warning';
    } else {
        status.innerHTML = '<span class="badge badge-queue">Pending</span>';
        btn.textContent = 'Mark Complete';
        btn.className = 'btn btn-success';
    }

    var doc = document.getElementById('wfTaskDoc');
    if(task.document_path){
        var url = '/' + String(task.document_path).replace(/^\/+/, '');
        doc.style.display = '';
        doc.innerHTML = 'Current document: <a href="'+esc(url)+'" target="_blank">Open</a>';
    } else {
        doc.style.display = 'none';
        doc.innerHTML = '';
    }

    openModal('wfTaskModal');
}

async function toggleWorkflowTask(){
    if(!_wfCurrentTask) return;

    var fileInput = document.getElementById('wfTaskFile');
    var r;
    if(fileInput.files && fileInput.files.length){
        var fd = new FormData();
        fd.append('_method', 'PATCH');
        fd.append('document', fileInput.files[0]);
        r = await apiFetch('/api/onboarding-tasks/'+_wfCurrentTask.id+'/toggle', { method: 'POST', body: fd });
    } else {
        r = await apiFetch('/api/onboarding-tasks/'+_wfCurrentTask.id+'/toggle', { method: 'PATCH' });
    }

    if(!r || !r.ok){ toast('Unable to update task','error'); return; }
    closeModal('wfTaskModal');
    toast('Task updated.','success');
    loadWorkflowStage();
}

document.addEventListener('DOMContentLoaded', loadWorkflowStage);
</script>
@endpush
