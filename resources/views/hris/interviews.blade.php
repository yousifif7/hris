@extends('layouts.app')
@section('title','McCrory Center — Interviews')
@section('content')
<div class="animate-in">
  <div style="display:flex;gap:8px;margin-bottom:20px;align-items:center">
    <div class="tabs" style="flex:1;margin-bottom:0;border-bottom:none">
      <button class="tab active" id="tabUp" onclick="switchTab('upcoming')">Upcoming</button>
      <button class="tab" id="tabPast" onclick="switchTab('past')">Past</button>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openScheduleInterviewPick()">+ Schedule Interview</button>
  </div>

  <div id="interviewList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading…</div></div>

  <div class="table-wrap" style="margin-top:24px">
    <div class="table-header"><h3>All Interviews</h3></div>
    <table><thead><tr><th>Candidate</th><th>Scheduled</th><th>Type</th><th>Interviewer</th><th>Status</th><th>Actions</th></tr></thead>
    <tbody id="intTableBody"></tbody></table>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _currentTab = 'upcoming';
async function pageRefresh(){ await loadInterviews(_currentTab); }

async function loadInterviews(tab){
    _currentTab = tab;
    var list = document.getElementById('interviewList');
    list.innerHTML = '<div style="text-align:center;padding:40px;color:var(--text3)">⏳ Loading…</div>';
    var r = await apiFetch('/api/interviews?status='+tab+'&per_page=50');
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];

    if(tab==='upcoming'){
        if(!items.length){
            list.innerHTML='<div style="text-align:center;padding:40px;color:var(--text3)"><div style="font-size:36px;margin-bottom:12px">📅</div><p>No upcoming interviews scheduled.</p><button class="btn btn-primary" style="margin-top:12px" onclick="openScheduleInterviewPick()">+ Schedule Interview</button></div>';
        } else {
            list.innerHTML = '<div class="section-title">📅 Upcoming Interviews (Zoom · 15-20 min)</div>'
            +items.map(function(i){
                var c = i.candidate||{};
                return '<div class="card-section" style="display:flex;align-items:center;gap:14px">'
                  +'<div style="width:46px;height:46px;border-radius:50%;background:'+Cl(c.id||0)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;flex-shrink:0">'+In(c.first_name,c.last_name)+'</div>'
                  +'<div style="flex:1"><div style="font-weight:600">'+esc(c.first_name+' '+c.last_name)+'</div><div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'—')+'</div></div>'
                  +'<div style="text-align:right"><div style="font-weight:600;font-size:13px">'+fD(i.scheduled_at)+'</div><div style="font-size:11px;color:var(--text3)">'+esc(i.type||'zoom')+' · '+esc(i.duration_minutes||20)+' min</div></div>'
                  +(i.meeting_link?'<a href="'+esc(i.meeting_link)+'" target="_blank" class="btn btn-primary btn-sm">Join</a>':'')
                  +'<button class="btn btn-secondary btn-sm" onclick="completeInterview('+i.id+')">Complete</button>'
                +'</div>';
            }).join('');
        }
    } else {
        list.innerHTML = items.length ? '' : '<div style="text-align:center;padding:40px;color:var(--text3)">No past interviews.</div>';
    }

    // All in table
    document.getElementById('intTableBody').innerHTML = items.map(function(i){
        var c = i.candidate||{};
        var intvr = i.interviewer||{};
        var stBadge = {'scheduled':'invite-sent','completed':'offer-accepted','cancelled':'rejected','no_show':'rejected'}[i.status]||'queue';
        return '<tr>'
          +'<td><div class="candidate-name">'+esc(c.first_name+' '+c.last_name)+'</div></td>'
          +'<td style="font-size:12px">'+fD(i.scheduled_at)+'</td>'
          +'<td style="text-transform:capitalize">'+esc(i.type||'zoom')+'</td>'
          +'<td>'+(intvr.first_name?esc(intvr.first_name+' '+intvr.last_name):'—')+'</td>'
          +'<td><span class="badge badge-'+stBadge+'">'+esc(i.status)+'</span></td>'
          +'<td class="actions-cell">'
            +(i.status==='scheduled'?'<button class="btn btn-secondary btn-sm" onclick="completeInterview('+i.id+')">Complete</button>':'')
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Profile</button>'
          +'</td></tr>';
    }).join('') || '<tr><td colspan="6" style="text-align:center;padding:20px;color:var(--text3)">No interviews.</td></tr>';
}

function switchTab(tab){
    document.getElementById('tabUp').classList.toggle('active', tab==='upcoming');
    document.getElementById('tabPast').classList.toggle('active', tab==='past');
    loadInterviews(tab);
}

async function completeInterview(id){
    var notes = prompt('Interview notes (optional):');
    var body = {notes: notes||'', question_responses: null};
    var r = await apiFetch('/api/interviews/'+id+'/complete', {method:'PATCH', body:JSON.stringify(body)});
    if(!r) return;
    toast('Interview marked complete — candidate moved to Post-Interview Review');
    loadInterviews(_currentTab);
}

document.addEventListener('DOMContentLoaded', function(){ loadInterviews('upcoming'); });
</script>
@endpush
