@extends('layouts.app')
@section('title','McCrory Center — Review Queue')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Candidates listed here have status <strong style="color:var(--accent)">Needs Review</strong> or <strong style="color:var(--accent)">Post-Interview Review</strong>. Take action to advance or close each candidate.</p>
  <div id="reviewQueueList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading review queue…</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadQueue(); }

async function loadQueue(){
    var r = await apiFetch('/api/candidates-pending-review');
    if(!r) return;
    var items = await r.json();
    var el = document.getElementById('reviewQueueList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:48px;margin-bottom:16px">✅</div><h3 style="color:var(--text)">All caught up!</h3><p>No candidates in the review queue.</p></div>';
        return;
    }
    el.innerHTML = items.map(function(c){
        var isP = c.status === 'post_interview_review';
        var initBg = 'background:'+Cl(c.id);
        var initText = In(c.first_name, c.last_name);
        var category = c.category ? esc(c.category.name) : '—';
        var assignee = c.assigned_to ? esc(c.assigned_to.first_name+' '+c.assigned_to.last_name) : '—';
        return '<div class="review-card">'
          +'<div class="top">'
            +'<div class="avatar" style="'+initBg+'">'+initText+'</div>'
            +'<div class="info">'
              +'<h4>'+esc(c.first_name+' '+c.last_name)+(isP?' <span style="font-size:11px;color:var(--accent)">(Post-Interview)</span>':'')+'</h4>'
              +'<div class="meta-row"><span>'+category+'</span><span>·</span><span>'+esc(c.source||'')+'</span><span>·</span><span>'+assignee+'</span></div>'
            +'</div>'+B(c.status)
          +'</div>'
          +(c.resume_text?'<div class="resume-preview">'+esc(c.resume_text).replace(/\n/g,'<br>')+'</div>':'')
          +(isP&&c.pre_screening?'<div style="background:var(--accent-glow);border:1px solid rgba(91,76,219,.15);border-radius:var(--radius);padding:12px;margin-bottom:14px;font-size:13px"><strong style="color:var(--accent)">Pre-Screening:</strong> '+esc(c.pre_screening.education_level)+' · '+esc(c.pre_screening.years_experience)+'yrs · '+esc(c.pre_screening.availability)+'</div>':'')
          +'<div class="action-bar">'
            +'<button class="btn btn-secondary btn-sm" onclick="setStatus('+c.id+',\'no_response\',\''+esc(c.first_name+' '+c.last_name)+'\')">No Response</button>'
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\',\''+esc(c.first_name+' '+c.last_name)+'\')">✗ Reject</button>'
            +'<button class="btn btn-warning btn-sm" onclick="setStatus('+c.id+',\'queue\',\''+esc(c.first_name+' '+c.last_name)+'\')">⏳ Queue</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +(isP?'<button class="btn btn-blue btn-sm" onclick="openScheduleInterview('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">📅 Schedule Interview</button>':'<button class="btn btn-blue btn-sm" onclick="setStatus('+c.id+',\'invite_sent\',\''+esc(c.first_name+' '+c.last_name)+'\')">✉ Send Invite</button>')
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status, name){
    name = name || 'this candidate';
    var confirmMsgs = {
        rejected:    'Reject '+name+'?\n\nThis will send a rejection email.',
        no_response: 'Mark '+name+' as no response?',
        queue:       'Move '+name+' to queue for later?',
        invite_sent: 'Send interview invite to '+name+'?'
    };
    var msg = confirmMsgs[status];
    if(msg && !confirm(msg)) return;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:status})});
    if(!r) return;
    var c = await r.json();
    var toastMsgs = {
        rejected:    '✗ '+esc(c.first_name+' '+c.last_name)+' rejected.',
        no_response: 'No response marked for '+esc(c.first_name+' '+c.last_name)+'.',
        queue:       '⏳ '+esc(c.first_name+' '+c.last_name)+' queued for later.',
        invite_sent: '✉ Invite sent to '+esc(c.first_name+' '+c.last_name)+'.'
    };
    var type = status==='rejected' ? 'error' : 'success';
    toast(toastMsgs[status] || esc(c.first_name+' '+c.last_name)+' updated.', type);
    loadQueue();
}

document.addEventListener('DOMContentLoaded', loadQueue);
</script>
@endpush
