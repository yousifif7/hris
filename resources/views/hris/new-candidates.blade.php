@extends('layouts.app')
@section('title','McCrory Center — New Candidates')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Candidates listed here have status <strong style="color:var(--accent)">Needs Review</strong>. Review their application and resume, then send an invite, queue for later, or reject.</p>
  <div id="newCandidatesList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading new candidates…</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadList(); }

async function loadList(){
    var r = await apiFetch('/api/candidates-new');
    if(!r) return;
    var items = await r.json();
    var el = document.getElementById('newCandidatesList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:48px;margin-bottom:16px">✅</div><h3 style="color:var(--text)">All caught up!</h3><p>No new candidates waiting for review.</p></div>';
        return;
    }
    el.innerHTML = items.map(function(c){
        var initBg = 'background:'+Cl(c.id);
        var initText = In(c.first_name, c.last_name);
        var category = c.category ? esc(c.category.name) : '—';
        var assignee = c.assigned_to ? esc(c.assigned_to.first_name+' '+c.assigned_to.last_name) : '—';
        return '<div class="review-card">'
          +'<div class="top">'
            +'<div class="avatar" style="'+initBg+'">'+initText+'</div>'
            +'<div class="info">'
              +'<h4>'+esc(c.first_name+' '+c.last_name)+'</h4>'
              +'<div class="meta-row"><span>'+category+'</span><span>·</span><span>'+esc(c.source||'')+'</span><span>·</span><span>'+assignee+'</span></div>'
            +'</div>'+B(c.status)
          +'</div>'
          +(c.resume_text?'<div class="resume-preview">'+esc(c.resume_text).replace(/\n/g,'<br>')+'</div>':'')
          +'<div class="action-bar">'
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\',\''+esc(c.first_name+' '+c.last_name)+'\')">✗ Reject</button>'
            +'<button class="btn btn-warning btn-sm" onclick="setStatus('+c.id+',\'queue\',\''+esc(c.first_name+' '+c.last_name)+'\')">⏳ Queue</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +'<button class="btn btn-blue btn-sm" onclick="setStatus('+c.id+',\'invite_sent\',\''+esc(c.first_name+' '+c.last_name)+'\')">✉ Send Invite</button>'
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status, name){
    name = name || 'this candidate';
    var confirmMsgs = {
        rejected:    'Reject '+name+'?\n\nThis will send a rejection email.',
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
        queue:       '⏳ '+esc(c.first_name+' '+c.last_name)+' queued for later.',
        invite_sent: '✉ Invite sent to '+esc(c.first_name+' '+c.last_name)+'.'
    };
    var type = status==='rejected' ? 'error' : 'success';
    toast(toastMsgs[status] || esc(c.first_name+' '+c.last_name)+' updated.', type);
    loadList();
}

document.addEventListener('DOMContentLoaded', loadList);
</script>
@endpush
