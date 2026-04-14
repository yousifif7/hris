@extends('layouts.app')
@section('title','TalentFlow — Onboarding')
@section('content')
<div class="animate-in">
  <div class="section-title">🎯 Active Onboarding</div>
  <div id="onboardingList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading…</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadOnboarding(); }

async function loadOnboarding(){
    var r = await apiFetch('/api/onboarding');
    if(!r) return;
    var data = await r.json();
    var items = data.data || data;
    var el = document.getElementById('onboardingList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:40px;margin-bottom:12px">🎉</div><p>No active onboarding.</p></div>';
        return;
    }
    el.innerHTML = items.map(function(item){
        var c = item.candidate||item;
        var cid = c.id||item.candidate_id;
        var tasks = item.onboarding_tasks || c.onboarding_tasks || [];
        var done = tasks.filter(function(t){ return t.is_completed; }).length;
        var total = tasks.length;
        var pct = total>0 ? Math.round(done/total*100) : 0;
        return '<div class="card-section" id="ob-card-'+cid+'">'
          +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">'
            +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(cid)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">'+In(c.first_name||item.first_name,c.last_name||item.last_name)+'</div>'
            +'<div><div style="font-weight:600">'+esc((c.first_name||item.first_name)+' '+(c.last_name||item.last_name))+'</div>'
            +'<div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'—')+' · '+done+'/'+total+' tasks complete</div></div>'
            +'<div style="margin-left:auto">'+B(c.status||item.status)+'</div>'
          +'</div>'
          +'<div class="progress-wrap" style="margin-bottom:14px"><div class="progress-fill" style="width:'+pct+'%"></div></div>'
          +tasks.map(function(t){
              return '<div class="checklist-item '+(t.is_completed?'done':'')+'" onclick="toggleTask('+t.id+',this,'+cid+')">'
                +'<div class="checkbox"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20,6 9,17 4,12"/></svg></div>'
                +'<div class="text">'+esc(t.task_name)+'</div>'
                +(t.due_date?'<div style="font-size:11px;color:var(--text3)">Due '+fDate(t.due_date)+'</div>':'')
                +(t.is_completed&&t.completed_at?'<div style="font-size:11px;color:var(--green)">Done '+fDate(t.completed_at)+'</div>':'')
              +'</div>';
          }).join('')
          +'<div style="margin-top:12px"><button class="btn btn-secondary btn-sm" onclick="viewCandidate('+cid+')">Full Profile</button></div>'
        +'</div>';
    }).join('');
}

async function toggleTask(taskId, rowEl, cid){
    rowEl.style.opacity='0.5';
    var r = await apiFetch('/api/onboarding-tasks/'+taskId+'/toggle', {method:'PATCH'});
    rowEl.style.opacity='1';
    if(!r) return;
    loadOnboarding();
}

document.addEventListener('DOMContentLoaded', loadOnboarding);
</script>
@endpush
