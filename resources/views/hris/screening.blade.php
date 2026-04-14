@extends('layouts.app')
@section('title','TalentFlow — Screening & Checks')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Track applications, consent forms, references, and clearances (MDHHS, SAM/OIG, NPDB).</p>
  <div id="screeningList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading…</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadScreening(); }

async function loadScreening(){
    var r = await apiFetch('/api/candidates?status=pre_screening_passed,awaiting_background_check&per_page=50');
    if(!r) return;
    var data = await r.json();

    // Also load awaiting_background_check
    var r2 = await apiFetch('/api/candidates?status=awaiting_background_check&per_page=50');
    var d2 = r2 ? await r2.json() : {data:[]};

    // Combine uniquely
    var seen = {};
    var items = [];
    (data.data||[]).concat(d2.data||[]).forEach(function(c){
        if(!seen[c.id]){ seen[c.id]=true; items.push(c); }
    });

    var el = document.getElementById('screeningList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:40px;margin-bottom:12px">🔍</div><p>No candidates in screening stage.</p></div>';
        return;
    }
    el.innerHTML = items.map(function(c){
        return renderScreeningCard(c);
    }).join('');
}

function renderScreeningCard(c){
    var bg = c.background_checks || [];
    var refs = c.references || [];
    var checks = ['mdhhs','sam_oig','npdb'];
    var bgRows = checks.map(function(type){
        var found = bg.find(function(b){ return b.check_type===type; });
        var status = found ? found.status : 'pending';
        var bid = found ? found.id : null;
        var col = status==='complete'?'var(--green)':status==='failed'?'var(--red)':'var(--yellow)';
        var label = type==='sam_oig'?'SAM/OIG':type.toUpperCase();
        return '<div class="bg-check-row"><span>'+esc(label)+' Clearance</span>'
          +'<div style="display:flex;gap:8px;align-items:center"><span style="font-weight:600;color:'+col+'">'+esc(status)+'</span>'
          +(bid?'<select style="font-size:11px;padding:3px 6px" onchange="updateBgCheck('+bid+',this.value)">'
              +'<option value="pending"'+(status==='pending'?' selected':'')+'>Pending</option>'
              +'<option value="in_progress"'+(status==='in_progress'?' selected':'')+'>In Progress</option>'
              +'<option value="complete"'+(status==='complete'?' selected':'')+'>Complete</option>'
              +'<option value="failed"'+(status==='failed'?' selected':'')+'>Failed</option>'
              +'</select>':'')
          +'</div></div>';
    }).join('');

    var refRow = '<div class="bg-check-row"><span>References</span>'
      +'<div style="display:flex;gap:8px;align-items:center">'
      +'<span style="font-weight:600;color:var(--blue)">'+refs.filter(function(r){ return r.status==='received'; }).length+'/'+refs.length+' received</span>'
      +'<button class="btn btn-blue btn-sm" onclick="openAddReference('+c.id+')">+ Add</button>'
      +'</div></div>';

    var allDone = bg.length>=3 && bg.every(function(b){ return b.status==='complete'; });

    return '<div class="card-section">'
      +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">'
        +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(c.id)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">'+In(c.first_name,c.last_name)+'</div>'
        +'<div><div style="font-weight:600">'+esc(c.first_name+' '+c.last_name)+'</div>'
        +'<div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'—')+' · '+B(c.status)+'</div></div>'
        +(allDone?'<span class="badge badge-offer-accepted" style="margin-left:auto">✅ All Checks Clear</span>':'')
      +'</div>'
      +'<div class="bg-check-grid">'+bgRows+refRow+'</div>'
      +'<div style="margin-top:12px;display:flex;gap:8px">'
        +(allDone?'<button class="btn btn-primary btn-sm" onclick="openOfferModal('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">✅ Approve → Send Offer</button>':'')
        +'<button class="btn btn-danger btn-sm" onclick="rejectCandidate('+c.id+')">✗ Reject</button>'
        +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">👤 Full Profile</button>'
      +'</div>'
    +'</div>';
}

async function updateBgCheck(id, status){
    var r = await apiFetch('/api/background-checks/'+id, {method:'PATCH', body:JSON.stringify({status:status})});
    if(!r) return;
    toast('Background check updated');
    loadScreening();
}

async function rejectCandidate(id){
    if(!confirm('Reject this candidate?')) return;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:'rejected'})});
    if(!r) return;
    toast('Candidate rejected');
    loadScreening();
}

document.addEventListener('DOMContentLoaded', loadScreening);
</script>
@endpush
