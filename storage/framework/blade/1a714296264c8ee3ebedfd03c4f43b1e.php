
<?php $__env->startSection('title','TalentFlow — Review Queue'); ?>
<?php $__env->startSection('content'); ?>
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Review each resume and decide: <strong style="color:var(--green)">Invite</strong> for interview, <strong style="color:var(--red)">Reject</strong>, or <strong style="color:var(--text3)">Queue</strong> for later.</p>
  <div id="reviewList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading queue…</div></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
            +(isP
              ?'<button class="btn btn-success btn-sm" onclick="setStatus('+c.id+',\'pre_screening_passed\')">✓ Move to Screening</button>'
              :'<button class="btn btn-success btn-sm" onclick="setStatus('+c.id+',\'invite_sent\')">✉ Invite to Interview</button>'
            )
            +'<button class="btn btn-danger btn-sm" onclick="setStatus('+c.id+',\'rejected\')">✗ Reject</button>'
            +'<button class="btn btn-warning btn-sm" onclick="setStatus('+c.id+',\'queue\')">⏳ Queue</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
            +(isP?'':'<button class="btn btn-blue btn-sm" onclick="openPrescreenModal('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">📋 Pre-Screen</button>')
            +'<button class="btn btn-blue btn-sm" onclick="openScheduleInterview('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">📅 Schedule Interview</button>'
          +'</div>'
        +'</div>';
    }).join('');
}

async function setStatus(id, status){
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:status})});
    if(!r) return;
    var c = await r.json();
    toast(esc(c.first_name+' '+c.last_name)+' → '+(SL[status]||status));
    loadQueue();
}

document.addEventListener('DOMContentLoaded', loadQueue);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\laravel projects\hrportal\resources\views\hris\review.blade.php ENDPATH**/ ?>