@extends('layouts.app')
@section('title','McCrory Center — Dashboard')
@section('content')
<div class="stats-grid animate-in" id="statsGrid">
  <div class="stat-card yellow"><div class="stat-label">Needs Review</div><div class="stat-value" id="sNeedsReview">—</div><div class="stat-change down">Action required</div></div>
  <div class="stat-card purple"><div class="stat-label">Total Pipeline</div><div class="stat-value" id="sPipeline">—</div></div>
  <div class="stat-card blue"><div class="stat-label">Interviews</div><div class="stat-value" id="sInterviews">—</div></div>
  <div class="stat-card pink"><div class="stat-label">Offers Pending</div><div class="stat-value" id="sOffers">—</div></div>
  <div class="stat-card green"><div class="stat-label">Onboarding</div><div class="stat-value" id="sOnboarding">—</div></div>
  <div class="stat-card orange"><div class="stat-label">Employees</div><div class="stat-value" id="sEmployees">—</div></div>
</div>

{{-- Public Apply Link --}}
<div class="card-section animate-in" style="margin-bottom:14px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;animation-delay:.05s">
  <div style="flex:1;min-width:220px">
    <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:2px">🔗 Public Apply Link</div>
    <div style="font-size:12px;color:var(--text2)">Share this URL with candidates — no login required.</div>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <input id="applyLinkInput" readonly style="width:340px;max-width:100%;font-size:12px;background:var(--surface2);color:var(--text2)" value="Loading...">
    <button class="btn btn-secondary btn-sm" onclick="copyApplyLink()">Copy</button>
    <button class="btn btn-secondary btn-sm" onclick="regenerateApplyLink()" title="Generate a new link (invalidates the old one)">Regenerate</button>
  </div>
</div>

<div class="pipeline-bar animate-in" id="pipelineBar" style="animation-delay:.1s"></div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;animation-delay:.15s" class="animate-in">
  <div class="table-wrap">
    <div class="table-header"><h3>Recent Candidates</h3><a href="{{ route('hris.review') }}" class="btn btn-secondary btn-sm">Review Queue →</a></div>
    <table><thead><tr><th>Candidate</th><th>Category</th><th>Status</th><th></th></tr></thead>
    <tbody id="recentTbody"><tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text3)">Loading...</td></tr></tbody></table>
  </div>
  <div class="table-wrap">
    <div class="table-header"><h3>Upcoming Interviews</h3><a href="{{ route('hris.interviews') }}" class="btn btn-secondary btn-sm">All →</a></div>
    <table><thead><tr><th>Candidate</th><th>When</th><th>Type</th></tr></thead>
    <tbody id="interviewTbody"><tr><td colspan="3" style="text-align:center;padding:24px;color:var(--text3)">Loading...</td></tr></tbody></table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadDash(); await loadApplyLink(); }

async function loadApplyLink(){
    var r = await apiFetch('/api/settings/apply-link');
    if(!r) return;
    var data = await r.json();
    var inp = document.getElementById('applyLinkInput');
    if(inp && data.url) inp.value = data.url;
}

async function copyApplyLink(){
    var val = document.getElementById('applyLinkInput').value;
    if(!val || val === 'Loading...') return;
    try { await navigator.clipboard.writeText(val); toast('Link copied to clipboard!'); }
    catch(e) { document.getElementById('applyLinkInput').select(); document.execCommand('copy'); toast('Link copied!'); }
}

async function regenerateApplyLink(){
    if(!confirm('Regenerating the link will invalidate the old one. Continue?')) return;
    var r = await apiFetch('/api/settings/apply-link/regenerate', {method:'POST'});
    if(!r) return;
    var data = await r.json();
    var inp = document.getElementById('applyLinkInput');
    if(inp && data.url){ inp.value = data.url; toast('New apply link generated!'); }
}
async function loadDash(){
    var r = await apiFetch('/api/dashboard');
    if(!r) return;
    var d = await r.json();
    var s = d.stats;
    document.getElementById('sNeedsReview').textContent  = s.needs_review;
    document.getElementById('sPipeline').textContent     = s.total_pipeline;
    document.getElementById('sInterviews').textContent   = s.interviews;
    document.getElementById('sOffers').textContent       = s.offers_pending;
    document.getElementById('sOnboarding').textContent   = s.onboarding;
    document.getElementById('sEmployees').textContent    = s.total_employees;

    var stages = [
        {k:'needs_review',l:'Review'},{k:'invite_sent',l:'Invited'},{k:'interview_scheduled',l:'Interview'},
        {k:'post_interview_review',l:'Post-Int.'},{k:'pre_screening_passed',l:'Screening'},
        {k:'awaiting_background_check',l:'BG Check'},{k:'offer_sent',l:'Offer'},
        {k:'offer_accepted',l:'Accepted'},{k:'onboarding',l:'Onboarding'}
    ];
    document.getElementById('pipelineBar').innerHTML = stages.map(function(st){
        return '<div class="pipeline-stage"><span class="num">'+(d.pipeline[st.k]||0)+'</span>'+esc(st.l)+'</div>';
    }).join('');

    document.getElementById('recentTbody').innerHTML = (d.recent_candidates||[]).map(function(c){
        return '<tr><td><div class="candidate-name">'+esc(c.first_name+' '+c.last_name)+'</div><div class="candidate-sub">'+esc(c.email||'')+'</div></td>'
            +'<td>'+(c.category?esc(c.category.name):'—')+'</td>'
            +'<td>'+B(c.status)+'</td>'
            +'<td><button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">View</button></td></tr>';
    }).join('') || '<tr><td colspan="4" style="text-align:center;padding:20px;color:var(--text3)">No candidates yet.</td></tr>';

    document.getElementById('interviewTbody').innerHTML = (d.upcoming_interviews||[]).map(function(i){
        var c = i.candidate||{};
        return '<tr><td class="candidate-name">'+esc(c.first_name+' '+c.last_name)+'</td>'
            +'<td style="font-size:12px">'+esc(fD(i.scheduled_at))+'</td>'
            +'<td style="text-transform:capitalize">'+esc(i.type||'zoom')+'</td></tr>';
    }).join('') || '<tr><td colspan="3" style="text-align:center;padding:20px;color:var(--text3)">No upcoming interviews.</td></tr>';
}
document.addEventListener('DOMContentLoaded', function(){ loadDash(); loadApplyLink(); });
</script>
@endpush
