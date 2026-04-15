@extends('layouts.app')
@section('title','McCrory Center — Offer Management')
@section('content')
<div class="animate-in">
  <div style="margin-bottom:16px;display:flex;gap:8px;align-items:center">
    <h3 style="font-weight:700;font-size:15px">Offer Letters</h3>
    <span style="flex:1"></span>
    <select id="offerFilter" onchange="loadOffers()" style="width:160px">
      <option value="">All statuses</option>
      <option value="sent">Sent</option>
      <option value="accepted">Accepted</option>
      <option value="declined">Declined</option>
      <option value="expired">Expired</option>
    </select>
  </div>
  <div class="table-wrap">
    <table><thead><tr><th>Candidate</th><th>Role</th><th>Type</th><th>Pay</th><th>Location</th><th>Status</th><th>Sent</th><th>Actions</th></tr></thead>
    <tbody id="offersTbody"><tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading…</td></tr></tbody></table>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadOffers(); }

async function loadOffers(){
    var r = await apiFetch('/api/offers?per_page=50');
    if(!r) return;
    var data = await r.json();
    var items = data.data || [];
    var filter = document.getElementById('offerFilter').value;
    if(filter) items = items.filter(function(o){ return o.status===filter; });
    var tbody = document.getElementById('offersTbody');
    if(!items.length){
        tbody.innerHTML='<tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text3)">No offers yet.</td></tr>';
        return;
    }
    tbody.innerHTML = items.map(function(o){
        var c = o.candidate||{};
        var colMap = {sent:'offer-sent',accepted:'offer-accepted',declined:'rejected',expired:'rejected',draft:'queue',viewed:'invite-sent'};
        var col = colMap[o.status]||'queue';
        return '<tr>'
          +'<td><div class="candidate-name">'+esc((c.first_name||'')+' '+(c.last_name||''))+'</div><div class="candidate-sub">'+esc(c.email||'')+'</div></td>'
          +'<td>'+(c.category?esc(c.category.name):'—')+'</td>'
          +'<td>'+esc(o.employment_type||'—')+'</td>'
          +'<td>'+esc(o.pay_rate?'$'+parseFloat(o.pay_rate).toFixed(2)+(o.pay_type==='hourly'?'/hr':'/yr'):'—')+'</td>'
          +'<td>'+esc(o.location||'—')+'</td>'
          +'<td><span class="badge badge-'+col+'">'+esc(o.status)+'</span></td>'
          +'<td>'+fDate(o.sent_at)+'</td>'
          +'<td class="actions-cell">'
            +(o.status==='sent'
              ?'<button class="btn btn-success btn-sm" onclick="respondOffer('+o.id+',\'accepted\')">Accept</button>'
               +'<button class="btn btn-danger btn-sm" onclick="respondOffer('+o.id+',\'declined\')">Decline</button>'
              :'')
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Profile</button>'
          +'</td></tr>';
    }).join('');
}

async function respondOffer(id, response){
    if(!confirm('Mark offer as '+response+'?')) return;
    var r = await apiFetch('/api/offers/'+id+'/respond', {method:'PATCH', body:JSON.stringify({response:response})});
    if(!r) return;
    toast('Offer '+response+'!');
    loadOffers();
}

document.addEventListener('DOMContentLoaded', loadOffers);
</script>
@endpush
