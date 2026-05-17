@extends('layouts.app')
@section('title','McCrory Center — Review Queue')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Combined review queue: candidates in <strong style="color:var(--accent)">Hiring</strong> (new applications awaiting an invite) and <strong style="color:var(--accent)">Pre-Interview Questions</strong> (post-interview, awaiting their application).</p>
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
        var isPostInterview = c.status === 'pre_interview_questions';
        var initBg = 'background:'+Cl(c.id);
        var initText = In(c.first_name, c.last_name);
        var category = c.category ? esc(c.category.name) : '—';
        var assignee = c.assigned_to ? esc(c.assigned_to.first_name+' '+c.assigned_to.last_name) : '—';
        return '<div class="review-card">'
          +'<div class="top">'
            +'<div class="avatar" style="'+initBg+'">'+initText+'</div>'
            +'<div class="info">'
              +'<h4>'+esc(c.first_name+' '+c.last_name)+(isPostInterview?' <span style="font-size:11px;color:var(--accent)">(Post-Interview)</span>':'')+'</h4>'
              +'<div class="meta-row"><span>'+category+'</span><span>·</span><span>'+esc(c.source||'')+'</span><span>·</span><span>'+assignee+'</span></div>'
            +'</div>'+B(c.status)
          +'</div>'
          +(c.resume_text?'<div class="resume-preview">'+esc(c.resume_text).replace(/\n/g,'<br>')+'</div>':'')
          +(isPostInterview&&c.pre_screening?'<div style="background:var(--accent-glow);border:1px solid rgba(91,76,219,.15);border-radius:var(--radius);padding:12px;margin-bottom:14px;font-size:13px"><strong style="color:var(--accent)">Pre-Screening:</strong> '+esc(c.pre_screening.education_level)+' · '+esc(c.pre_screening.years_experience)+'yrs · '+esc(c.pre_screening.availability)+'</div>':'')
          +'<div class="action-bar">'
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\',\''+esc(c.first_name+' '+c.last_name)+'\')">✗ Reject</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +(isPostInterview
                ?'<button class="btn btn-blue btn-sm" onclick="setStatus('+c.id+',\'verification_and_review\',\''+esc(c.first_name+' '+c.last_name)+'\')">→ Verification</button>'
                :'<button class="btn btn-blue btn-sm" onclick="setStatus('+c.id+',\'pre_screening\',\''+esc(c.first_name+' '+c.last_name)+'\')">✉ Send Invite</button>')
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status, name){
    name = name || 'this candidate';
    var confirmMsgs = {
        rejected:                'Reject '+name+'?\n\nThis will send a rejection email.',
        pre_screening:           'Send interview invite to '+name+'?',
        verification_and_review: 'Move '+name+' into Verification and Review?'
    };
    var msg = confirmMsgs[status];
    if(msg && !confirm(msg)) return;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:status})});
    if(!r) return;
    var c = await r.json();
    var type = status==='rejected' ? 'error' : 'success';
    toast(esc(c.first_name+' '+c.last_name)+' → '+(SL[status]||status), type);
    loadQueue();
}

document.addEventListener('DOMContentLoaded', loadQueue);
</script>
@endpush
