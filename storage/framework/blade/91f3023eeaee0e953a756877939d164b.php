
<?php $__env->startSection('title','TalentFlow — Hiring Pipeline'); ?>
<?php $__env->startSection('content'); ?>
<div class="animate-in">
  <div style="display:flex;gap:8px;margin-bottom:16px;align-items:center">
    <input id="pSearch" placeholder="Search candidates…" style="width:220px" oninput="filterKanban()">
    <select id="pCat" onchange="filterKanban()" style="width:190px"><option value="">All categories</option></select>
    <button class="btn btn-secondary btn-sm" onclick="location.href='<?php echo e(route('hris.intake')); ?>'">+ Add Candidate</button>
  </div>
  <div class="kanban" id="kanbanBoard"><div style="text-align:center;padding:60px;color:var(--text3);width:100%">⏳ Loading pipeline…</div></div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
var _allCands = [];

async function pageRefresh(){ await loadPipeline(); }

async function loadPipeline(){
    // Load categories for filter
    var r2 = await apiFetch('/api/candidates?per_page=200');
    if(!r2) return;
    var d2 = await r2.json();
    _allCands = d2.data || [];
    // Populate category filter from candidates
    var cats = {};
    _allCands.forEach(function(c){ if(c.category) cats[c.category.name]=true; });
    var sel = document.getElementById('pCat');
    sel.innerHTML = '<option value="">All categories</option>';
    Object.keys(cats).sort().forEach(function(n){ var o=document.createElement('option'); o.value=n; o.textContent=n; sel.appendChild(o); });
    renderKanban(_allCands);
}

function filterKanban(){
    var q = document.getElementById('pSearch').value.toLowerCase();
    var cat = document.getElementById('pCat').value;
    var filtered = _allCands.filter(function(c){
        var nm = (c.first_name+' '+c.last_name).toLowerCase();
        var matchQ = !q || nm.includes(q) || (c.email||'').toLowerCase().includes(q);
        var matchC = !cat || (c.category && c.category.name===cat);
        return matchQ && matchC;
    });
    renderKanban(filtered);
}

function renderKanban(cands){
    var cols = [
        {t:'Review',         c:'var(--yellow)', s:['needs_review','post_interview_review']},
        {t:'Invited',        c:'var(--blue)',   s:['invite_sent','no_response']},
        {t:'Interview',      c:'var(--accent)', s:['interview_scheduled']},
        {t:'Screening',      c:'var(--orange)', s:['pre_screening_passed','awaiting_background_check']},
        {t:'Offers',         c:'var(--pink)',   s:['offer_sent']},
        {t:'Accepted',       c:'var(--green)',  s:['offer_accepted','onboarding']},
        {t:'Closed',         c:'var(--red)',    s:['rejected','applicant_declined','queue']},
    ];
    document.getElementById('kanbanBoard').innerHTML = cols.map(function(col){
        var its = cands.filter(function(c){ return col.s.indexOf(c.status)!==-1; });
        return '<div class="kanban-col">'
          +'<div class="kanban-col-header"><span class="dot" style="background:'+col.c+'"></span><h4>'+esc(col.t)+'</h4><span class="count">'+its.length+'</span></div>'
          +'<div class="kanban-cards">'
          +its.map(function(c){
              return '<div class="kanban-card" onclick="viewCandidate('+c.id+')" title="Click to view profile">'
                +'<div class="name">'+esc(c.first_name+' '+c.last_name)+'</div>'
                +'<div class="role">'+(c.category?esc(c.category.name):'—')+'</div>'
                +'<div class="meta"><span>'+esc(c.source||'')+'</span><span>·</span><span>'+esc(fDate(c.created_at))+'</span></div>'
                +'<div style="margin-top:6px">'+B(c.status)+'</div>'
              +'</div>';
          }).join('')
          +'</div></div>';
    }).join('');
}

var qs = new URLSearchParams(location.search);
if(qs.get('search')) document.addEventListener('DOMContentLoaded', function(){ document.getElementById('pSearch').value=qs.get('search'); });
document.addEventListener('DOMContentLoaded', loadPipeline);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\laravel projects\hrportal\resources\views\hris\pipeline.blade.php ENDPATH**/ ?>