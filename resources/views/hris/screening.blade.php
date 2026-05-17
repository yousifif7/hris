@extends('layouts.app')
@section('title','McCrory Center — Verification and Review')
@section('content')
<div class="animate-in">
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">This stage starts after the interview is completed. Review post-interview applications, then track references and clearances through verification.</p>
  <div id="screeningList"><div style="text-align:center;padding:60px;color:var(--text3)">⏳ Loading...</div></div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadScreening(); }

async function loadScreening(){
  var r = await apiFetch('/api/candidates?status=post_interview_review,pre_screening_passed,awaiting_background_check&include=backgroundChecks,references,preScreening&per_page=50');
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];

    var el = document.getElementById('screeningList');
    if(!items.length){
        el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:40px;margin-bottom:12px">🔍</div><p>No candidates in screening stage.</p></div>';
        return;
    }
    el.innerHTML = items.map(function(c){
        if(c.status === 'post_interview_review') return renderPostInterviewCard(c);
        return renderScreeningCard(c);
    }).join('');
}

function renderPostInterviewCard(c){
    return '<div class="card-section">'
      +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">'
        +'<div style="width:40px;height:40px;border-radius:50%;background:'+Cl(c.id)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700">'+In(c.first_name,c.last_name)+'</div>'
        +'<div><div style="font-weight:600">'+esc(c.first_name+' '+c.last_name)+'</div>'
        +'<div style="font-size:12px;color:var(--text3)">'+(c.category?esc(c.category.name):'—')+' · Interview completed</div></div>'
        +'<span style="margin-left:auto">'+B(c.status)+'</span>'
      +'</div>'
      +'<div style="background:var(--bg2);border:1px solid var(--border);border-radius:8px;padding:12px;font-size:13px;line-height:1.6;margin-bottom:12px">'
        +(c.pre_screening
          ?'<strong style="color:var(--accent)">Application submitted:</strong> '+esc(c.pre_screening.education_level||'Not set')
            +(c.pre_screening.years_experience!=null?' · '+esc(c.pre_screening.years_experience)+' yrs':'')
            +(c.pre_screening.availability?' · '+esc(c.pre_screening.availability):'')
          :'<strong style="color:var(--accent)">Waiting on post-interview application.</strong> The candidate has completed the interview but has not yet been moved into the verification/checks phase.')
      +'</div>'
      +'<div style="display:flex;gap:8px;flex-wrap:wrap">'
        +'<button class="btn btn-blue btn-sm" onclick="openPrescreenModal('+c.id+',\''+esc(c.first_name+' '+c.last_name)+'\')">Open Pre-Screen Review</button>'
        +'<button class="btn btn-secondary btn-sm" onclick="editEmploymentApplication('+c.id+')">Edit Employment App</button>'
        +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Full Profile</button>'
      +'</div>'
    +'</div>';
}

function renderScreeningCard(c){
    var bg = c.background_checks || [];
    var refs = c.references || [];
    var checks = ['mdhhs','sam_oig','npdb'];
    var prescreenSection = '';
    if (c.pre_screening) {
        var ps = c.pre_screening;
        var formUrl = ps.uploaded_form_path ? '/' + String(ps.uploaded_form_path).replace(/^\/+/, '') : '';
        prescreenSection = '<div style="margin-bottom:14px;padding:12px;background:var(--bg2);border-radius:8px;border-left:3px solid var(--blue)">'
          +'<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">'
            +'<span style="font-weight:600;font-size:13px">📋 Application Form</span>'
            +'<div style="display:flex;gap:8px;flex-wrap:wrap">'
              +(formUrl?'<button class="btn btn-blue btn-sm" onclick="openUploadedForm(\''+esc(formUrl)+'\')">📄 Open PDF</button>':'')
              +(formUrl?'<button class="btn btn-secondary btn-sm" onclick="downloadUploadedForm(\''+esc(formUrl)+'\',\''+esc(ps.uploaded_form_name || 'completed-form.pdf')+'\')">⬇ Download</button>':'')
              +'<button class="btn btn-secondary btn-sm" onclick="editEmploymentApplication('+c.id+')">✏ Edit Employment App</button>'
              +'<button class="btn btn-secondary btn-sm" onclick="printApplicationForm('+c.id+')">🖨️ Print Summary</button>'
            +'</div>'
          +'</div>'
          +'<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px;color:var(--text2)">'
            +(ps.education_level?'<div><span style="color:var(--text3)">Education:</span> '+esc(ps.education_level)+'</div>':'')
            +(ps.years_experience!=null?'<div><span style="color:var(--text3)">Experience:</span> '+esc(ps.years_experience)+' yrs</div>':'')
            +(ps.availability?'<div><span style="color:var(--text3)">Availability:</span> '+esc(ps.availability)+'</div>':'')
            +(ps.earliest_start_date?'<div><span style="color:var(--text3)">Start Date:</span> '+esc(ps.earliest_start_date)+'</div>':'')
            +(ps.licenses?'<div style="grid-column:span 2"><span style="color:var(--text3)">Licenses:</span> '+esc(ps.licenses)+'</div>':'')
            +(ps.additional_notes?'<div style="grid-column:span 2"><span style="color:var(--text3)">Notes:</span> '+esc(ps.additional_notes)+'</div>':'')
            +(ps.uploaded_form_name?'<div style="grid-column:span 2"><span style="color:var(--text3)">Uploaded PDF:</span> '+esc(ps.uploaded_form_name)+'</div>':'')
            +(ps.employment_application_submitted_at?'<div style="grid-column:span 2"><span style="color:var(--text3)">Employment App:</span> Submitted '+esc(ps.employment_application_submitted_at)+'</div>':'')
          +'</div>'
        +'</div>';
    }

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
      +prescreenSection
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
    toast('✓ Background check updated to: '+status, 'success');
    loadScreening();
}

async function rejectCandidate(id){
    if(!confirm('Reject this candidate?\n\nA rejection email will be sent.')) return;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:'rejected'})});
    if(!r) return;
    toast('✗ Candidate rejected.', 'error');
    loadScreening();
}

function printApplicationForm(candidateId){
    var token = getToken();
  window.open('/hris/candidates/'+candidateId+'/application-print?token='+encodeURIComponent(token), '_blank');
}

function editEmploymentApplication(candidateId){
  window.location.href = '/hris/candidates/' + candidateId + '/employment-application';
}

function openUploadedForm(url){
  window.open(url, '_blank');
}

function downloadUploadedForm(url, filename){
  var link = document.createElement('a');
  link.href = url;
  link.download = filename || 'completed-form.pdf';
  document.body.appendChild(link);
  link.click();
  link.remove();
}

document.addEventListener('DOMContentLoaded', loadScreening);
</script>
@endpush
