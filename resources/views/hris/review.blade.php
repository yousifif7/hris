@extends('layouts.app')
@section('title','McCrory Center — Pre-Interview Questions')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Candidates here finished their interview and need to complete the post-interview application before HR can move them into <strong style="color:var(--blue)">Verification and Review</strong>.</p>
  <div id="reviewList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading candidates…</div></div>
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
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:48px;margin-bottom:16px">✅</div><h3 style="color:var(--text)">All caught up!</h3><p>No candidates waiting for pre-interview questions.</p></div>';
        document.getElementById('reviewBadge').textContent = '0';
        return;
    }
    document.getElementById('reviewBadge').textContent = items.length;
    el.innerHTML = items.map(function(c){
        var initBg = 'background:'+Cl(c.id);
        var initText = In(c.first_name, c.last_name);
        var category = c.category ? esc(c.category.name) : '—';
        var assignee = c.assigned_to ? esc(c.assigned_to.first_name+' '+c.assigned_to.last_name) : '—';
        var hasApp = c.pre_screening && c.pre_screening.employment_application_submitted_at;
        return '<div class="review-card">'
          +'<div class="top">'
            +'<div class="avatar" style="'+initBg+'">'+initText+'</div>'
            +'<div class="info">'
              +'<h4>'+esc(c.first_name+' '+c.last_name)+'</h4>'
              +'<div class="meta-row"><span>'+category+'</span><span>·</span><span>'+esc(c.source||'')+'</span><span>·</span><span>'+assignee+'</span></div>'
            +'</div>'+B(c.status)
          +'</div>'
          +'<div style="background:var(--bg2);border:1px solid var(--border);border-radius:var(--radius);padding:12px;margin-bottom:14px;font-size:13px">'
            +(hasApp
              ? '<strong style="color:var(--green)">✓ Application submitted.</strong> Move forward to verification when ready.'
              : '<strong style="color:var(--accent)">Waiting on post-interview application.</strong> Send the pre-screening link or edit it from the CRM.')
          +'</div>'
          +'<div class="action-bar">'
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\',\''+esc(c.first_name+' '+c.last_name)+'\')">✗ Reject</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="window.location.href=\'/hris/candidates/'+c.id+'/employment-application\'">Edit Employment App</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +'<button class="btn btn-blue btn-sm" onclick="setStatus('+c.id+',\'verification_and_review\',\''+esc(c.first_name+' '+c.last_name)+'\')">→ Verification</button>'
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status){
    var name = (arguments[2]||'this candidate');
    var confirmMsgs = {
        rejected:                'Reject '+name+'?\n\nThis will send a rejection email.',
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
