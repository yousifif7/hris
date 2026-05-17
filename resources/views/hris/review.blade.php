@extends('layouts.app')
@section('title','McCrory Center — Pre-Interview Questions')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">This stage covers candidates who are in the interview-prep flow: <strong style="color:var(--blue)">Invite Sent</strong>, <strong style="color:var(--blue)">Interview Scheduled</strong>, and <strong style="color:var(--blue)">No Response</strong>.</p>
  <div id="reviewList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading invited candidates…</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadQueue(); }

async function loadQueue(){
    var r = await apiFetch('/api/candidates-review-queue');
    if(!r) return;
    var items = await r.json();
    var el = document.getElementById('reviewList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:48px;margin-bottom:16px">✅</div><h3 style="color:var(--text)">All caught up!</h3><p>No candidates waiting for review.</p></div>';
        document.getElementById('reviewBadge').textContent = '0';
        return;
    }
    document.getElementById('reviewBadge').textContent = items.length;
    el.innerHTML = items.map(function(c){
      var isScheduled = c.status === 'interview_scheduled';
      var isNoResponse = c.status === 'no_response';
      var nextInterview = (c.interviews || []).find(function(i){ return i.status === 'scheduled'; });
        var initBg = 'background:'+Cl(c.id);
        var initText = In(c.first_name, c.last_name);
        var category = c.category ? esc(c.category.name) : '—';
        var assignee = c.assigned_to ? esc(c.assigned_to.first_name+' '+c.assigned_to.last_name) : '—';
        return '<div class="review-card">'
          +'<div class="top">'
            +'<div class="avatar" style="'+initBg+'">'+initText+'</div>'
            +'<div class="info">'
              +'<h4>'+esc(c.first_name+' '+c.last_name)
                +(isScheduled?' <span style="font-size:11px;color:var(--blue)">(Scheduled)</span>':'')
                +(isNoResponse?' <span style="font-size:11px;color:var(--yellow)">(No Response)</span>':'')
              +'</h4>'
              +'<div class="meta-row"><span>'+category+'</span><span>·</span><span>'+esc(c.source||'')+'</span><span>·</span><span>'+assignee+'</span></div>'
            +'</div>'+B(c.status)
          +'</div>'
          +(c.resume_text?'<div class="resume-preview">'+esc(c.resume_text).replace(/\n/g,'<br>')+'</div>':'')
          +(nextInterview?'<div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:12px;margin-bottom:14px;font-size:13px"><strong style="color:var(--blue)">Scheduled Interview:</strong> '+esc(fD(nextInterview.scheduled_at))+' · '+esc(nextInterview.type||'zoom')+'</div>':'')
          +'<div class="action-bar">'
            +(isScheduled
              ?'<button class="btn btn-secondary btn-sm" onclick="window.location.href=\'/hris/interviews\'">Manage Interview</button>'
              :'<button class="btn btn-secondary btn-sm" onclick="setStatus('+c.id+',\'no_response\',\''+esc(c.first_name+' '+c.last_name)+'\')">No Response</button>')
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\',\''+esc(c.first_name+' '+c.last_name)+'\')">✗ Reject</button>'
            +'<button class="btn btn-warning btn-sm" onclick="setStatus('+c.id+',\'queue\',\''+esc(c.first_name+' '+c.last_name)+'\')">⏳ Queue</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +(isScheduled
              ?''
              :'<button class="btn btn-blue btn-sm" onclick="openScheduleInterview('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">📅 Schedule Interview</button>')
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status){
    var name = (arguments[2]||'this candidate');

    var confirmMsgs = {
        rejected:         'Reject '+name+'?\n\nThis will send a rejection email.',
        no_response:      'Mark '+name+' as no response?',
        queue:            'Move '+name+' to queue for later?'
    };
    var msg = confirmMsgs[status];
    if(msg && !confirm(msg)) return;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:status})});
    if(!r) return;
    var c = await r.json();
    var toastMsgs = {
        rejected:    '✗ '+esc(c.first_name+' '+c.last_name)+' rejected.',
        no_response: 'No response marked for '+esc(c.first_name+' '+c.last_name)+'.',
        queue:       '⏳ '+esc(c.first_name+' '+c.last_name)+' queued for later.'
    };
    var type = status==='rejected' ? 'error' : 'success';
    toast(toastMsgs[status] || esc(c.first_name+' '+c.last_name)+' → '+(SL[status]||status), type);
    loadQueue();
}

document.addEventListener('DOMContentLoaded', loadQueue);
</script>
@endpush
