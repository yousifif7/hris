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
    <tbody id="offersTbody"><tr><td colspan="8" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr></tbody></table>
  </div>
</div>

<!-- Edit Offer Modal -->
<div class="modal-overlay" id="modal-editOffer" onclick="if(event.target===this)closeModal('editOffer')">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3>Edit Offer</h3><button onclick="closeModal('editOffer')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="editOfferId">
      <div class="form-row">
        <div class="form-group"><label>Pay Rate</label><input type="number" id="editOfferPay" step="0.01"></div>
        <div class="form-group"><label>Pay Type</label>
          <select id="editOfferPayType">
            <option value="hourly">Hourly</option>
            <option value="salary">Salary</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Employment Type</label>
          <select id="editOfferEmpType">
            <option>Full-Time</option>
            <option>Part-Time</option>
            <option>1099</option>
          </select>
        </div>
        <div class="form-group"><label>Location</label><input id="editOfferLoc"></div>
      </div>
      <div class="form-group"><label>Required Documents</label><textarea id="editOfferDocs" rows="2"></textarea></div>
      <div class="form-row">
        <div class="form-group"><label>Deadline (days)</label><input type="number" id="editOfferDays"></div>
        <div class="form-group"><label>Start Date</label><input type="date" id="editOfferStart"></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('editOffer')">Cancel</button>
      <button class="btn btn-primary" onclick="saveEditOffer()">Save Changes</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _allOffers = [];

async function pageRefresh(){ await loadOffers(); }

async function loadOffers(){
    var r = await apiFetch('/api/offers?per_page=50');
    if(!r) return;
    var data = await r.json();
    _allOffers = data.data || [];
    renderOffers();
}

function renderOffers(){
    var filter = document.getElementById('offerFilter').value;
    var items = filter ? _allOffers.filter(function(o){ return o.status===filter; }) : _allOffers;
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
            +'<button class="btn btn-warning btn-sm" onclick="openEditOffer('+o.id+')">✏ Edit</button>'
            +'<button class="btn btn-danger btn-sm" onclick="deleteOffer('+o.id+',\''+esc((c.first_name||'')+' '+(c.last_name||''))+'\')">🗑</button>'
            +'<button class="btn btn-secondary btn-sm" onclick="viewCandidate('+c.id+')">Profile</button>'
          +'</td></tr>';
    }).join('');
}

async function respondOffer(id, response){
    var msgs = {
        accepted: 'Mark this offer as accepted?\n\nOnboarding tasks will be created.',
        declined: 'Mark this offer as declined?'
    };
    if(!confirm(msgs[response]||'Continue?')) return;
    var r = await apiFetch('/api/offers/'+id+'/respond', {method:'PATCH', body:JSON.stringify({response:response})});
    if(!r) return;
    if(response==='accepted'){
        toast('✓ Offer accepted! Onboarding started.', 'success');
    } else {
        toast('✗ Offer declined.', 'error');
    }
    loadOffers();
}

function openEditOffer(id){
    var o = _allOffers.find(function(x){ return x.id===id; });
    if(!o) return;
    document.getElementById('editOfferId').value       = o.id;
    document.getElementById('editOfferPay').value      = o.pay_rate||'';
    document.getElementById('editOfferPayType').value  = o.pay_type||'hourly';
    document.getElementById('editOfferEmpType').value  = o.employment_type||'Full-Time';
    document.getElementById('editOfferLoc').value      = o.location||'';
    document.getElementById('editOfferDocs').value     = o.required_documents||'';
    document.getElementById('editOfferDays').value     = o.deadline_days||20;
    document.getElementById('editOfferStart').value    = o.start_date ? o.start_date.split('T')[0].split(' ')[0] : '';
    openModal('editOffer');
}

async function saveEditOffer(){
    var id = document.getElementById('editOfferId').value;
    var body = {
        pay_rate:           parseFloat(document.getElementById('editOfferPay').value)||null,
        pay_type:           document.getElementById('editOfferPayType').value,
        employment_type:    document.getElementById('editOfferEmpType').value,
        location:           document.getElementById('editOfferLoc').value||null,
        required_documents: document.getElementById('editOfferDocs').value||null,
        deadline_days:      +document.getElementById('editOfferDays').value||null,
        start_date:         document.getElementById('editOfferStart').value||null,
    };
    var r = await apiFetch('/api/offers/'+id, {method:'PATCH', body:JSON.stringify(body)});
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||'Update failed','error'); return; }
    closeModal('editOffer');
    toast('Offer updated!');
    loadOffers();
}

async function deleteOffer(id, name){
    if(!confirm('Delete the offer for '+name+'? This cannot be undone.')) return;
    var r = await apiFetch('/api/offers/'+id, {method:'DELETE'});
    if(!r) return;
    toast('Offer deleted.');
    loadOffers();
}

document.addEventListener('DOMContentLoaded', loadOffers);
</script>
@endpush
