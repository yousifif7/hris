<div class="modal-overlay" id="modal-candidateDetail"><div class="modal" style="max-width:760px"><div class="modal-header"><h3 id="detailName"></h3><button onclick="closeModal('candidateDetail')">✕</button></div><div class="modal-body" id="detailBody"></div><div class="modal-footer" id="detailFooter"></div></div></div>

<div class="modal-overlay" id="modal-prescreen"><div class="modal" style="max-width:560px"><div class="modal-header"><h3>Pre-Screening Questionnaire</h3><button onclick="closeModal('prescreen')">✕</button></div><div class="modal-body" id="prescreenBody"></div><div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('prescreen')">Cancel</button><button class="btn btn-primary" onclick="savePrescreen()">Save Responses</button></div></div></div>

<div class="modal-overlay" id="modal-offer"><div class="modal" style="max-width:540px"><div class="modal-header"><h3>Generate Offer Letter</h3><button onclick="closeModal('offer')">✕</button></div><div class="modal-body" id="offerBody"></div><div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('offer')">Cancel</button><button class="btn btn-primary" onclick="sendOffer()">Send Offer</button></div></div></div>

<div class="modal-overlay" id="modal-scheduleInterview"><div class="modal" style="max-width:500px">
  <div class="modal-header"><h3>Schedule Interview</h3><button onclick="closeModal('scheduleInterview')">✕</button></div>
  <div class="modal-body">
    <input type="hidden" id="iCandId">
    <div class="form-group" id="iCandNameGroup"><label>Candidate</label><input id="iCandName" disabled></div>
    <div class="form-group" id="iCandSelectGroup" style="display:none"><label>Candidate</label>
      <select id="iCandSelect" style="width:100%" onchange="document.getElementById('iCandId').value=this.value">
        <option value="">— Select a candidate —</option>
      </select>
    </div>
    <div class="form-group"><label>Date & Time</label><input type="datetime-local" id="iDate"></div>
    <div class="form-row">
      <div class="form-group"><label>Duration (min)</label><input type="number" id="iDur" value="20" min="5" max="120"></div>
      <div class="form-group"><label>Type</label><select id="iType"><option value="zoom">Zoom</option><option value="in_person">In-Person</option><option value="phone">Phone</option></select></div>
    </div>
    <div class="form-group"><label>Meeting Link</label><input id="iLink" placeholder="https://zoom.us/j/..."></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('scheduleInterview')">Cancel</button>
    <button class="btn btn-primary" onclick="submitInterview()">Schedule Interview</button>
  </div>
</div></div>

<div class="modal-overlay" id="modal-addReference"><div class="modal" style="max-width:480px">
  <div class="modal-header"><h3>Add Reference</h3><button onclick="closeModal('addReference')">✕</button></div>
  <div class="modal-body">
    <input type="hidden" id="refCandId">
    <div class="form-group"><label>Reference Name</label><input id="refName"></div>
    <div class="form-row">
      <div class="form-group"><label>Email</label><input type="email" id="refEmail"></div>
      <div class="form-group"><label>Phone</label><input id="refPhone"></div>
    </div>
    <div class="form-group"><label>Relationship</label><input id="refRel" placeholder="e.g. Supervisor, Colleague"></div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('addReference')">Cancel</button>
    <button class="btn btn-primary" onclick="submitReference()">Add & Email Reference</button>
  </div>
</div></div>

<script>
/* Modal action handlers used across multiple pages */
function openScheduleInterview(candId, candName){
    // Show fixed name input, hide select
    document.getElementById('iCandNameGroup').style.display = '';
    document.getElementById('iCandSelectGroup').style.display = 'none';
    document.getElementById('iCandId').value = candId;
    document.getElementById('iCandName').value = candName;
    document.getElementById('iDate').value = '';
    document.getElementById('iLink').value = '';
    openModal('scheduleInterview');
}

async function openScheduleInterviewPick(){
    // Show candidate select, hide name input
    document.getElementById('iCandNameGroup').style.display = 'none';
    document.getElementById('iCandSelectGroup').style.display = '';
    document.getElementById('iCandId').value = '';
    document.getElementById('iDate').value = '';
    document.getElementById('iLink').value = '';
    openModal('scheduleInterview');
    // Populate candidates in pipeline that haven't had an interview yet
    var sel = document.getElementById('iCandSelect');
    sel.innerHTML = '<option value="">⏳ Loading…</option>';
    var r = await apiFetch('/api/candidates?status=invite_sent,needs_review,post_interview_review&per_page=200');
    if(!r){ sel.innerHTML='<option value="">— Failed to load —</option>'; return; }
    var data = await r.json();
    var cands = data.data || [];
    sel.innerHTML = '<option value="">— Select a candidate —</option>'
      + cands.map(function(c){
          return '<option value="'+c.id+'">'+esc(c.first_name+' '+c.last_name)+(c.category?' · '+esc(c.category.name):'')+'</option>';
        }).join('');
    sel.onchange = function(){ document.getElementById('iCandId').value = this.value; };
}

async function submitInterview(){
    var candId = document.getElementById('iCandId').value;
    var dt = document.getElementById('iDate').value;
    if(!candId){ toast('Please select a candidate','error'); return; }
    if(!dt){ toast('Please select a date and time','error'); return; }
    var body = {
        candidate_id: +candId,
      scheduled_at: dt.replace('T',' ') + ':00',
        duration_minutes: +document.getElementById('iDur').value || 20,
        type: document.getElementById('iType').value,
        meeting_link: document.getElementById('iLink').value || null
    };
    var r = await apiFetch('/api/interviews', {method:'POST', body:JSON.stringify(body)});
    if(!r) return;
    if(!r.ok){
        var err = await r.json();
        toast(err.message || (err.errors ? Object.values(err.errors).flat()[0] : 'Failed to schedule'), 'error');
        return;
    }
    closeModal('scheduleInterview');
    toast('Interview scheduled!');
    if(typeof pageRefresh==='function') pageRefresh();
}

function openAddReference(candId){
    document.getElementById('refCandId').value = candId;
    ['refName','refEmail','refPhone','refRel'].forEach(function(id){ document.getElementById(id).value=''; });
    openModal('addReference');
}

async function submitReference(){
    var candId = document.getElementById('refCandId').value;
    var name = document.getElementById('refName').value.trim();
    var email = document.getElementById('refEmail').value.trim();
    if(!name||!email){ toast('Name and email are required','error'); return; }
    var body = {
        reference_name: name,
        reference_email: email,
        reference_phone: document.getElementById('refPhone').value||null,
        relationship: document.getElementById('refRel').value||null
    };
    var r = await apiFetch('/api/candidates/'+candId+'/references', {method:'POST', body:JSON.stringify(body)});
    if(!r) return;
    closeModal('addReference');
    toast('Reference added — email sent automatically!');
    if(typeof pageRefresh==='function') pageRefresh();
}

function openOfferModal(candId, candName){
    document.getElementById('offerBody').innerHTML =
      '<input type="hidden" id="oId" value="'+candId+'">'
      +'<div class="form-group"><label>Candidate</label><input value="'+esc(candName)+'" disabled></div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Pay Rate</label><input id="oPay" type="number" step="0.01" placeholder="25.00"></div>'
        +'<div class="form-group"><label>Pay Type</label><select id="oPayType"><option value="hourly">Hourly</option><option value="salary">Salary</option></select></div>'
      +'</div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Employment Type</label><select id="oTy"><option>Full-Time</option><option>Part-Time</option><option>1099</option></select></div>'
        +'<div class="form-group"><label>Location</label><input id="oLo" placeholder="Main Office"></div>'
      +'</div>'
      +'<div class="form-group"><label>Required Documents</label><textarea id="oDo" rows="2">Signed offer, I-9, W-4, License, Credentials</textarea></div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Deadline (days)</label><input type="number" id="oDy" value="20"></div>'
        +'<div class="form-group"><label>Start Date</label><input type="date" id="oStart"></div>'
      +'</div>';
    openModal('offer');
}

async function sendOffer(){
    var candId = +document.getElementById('oId').value;
    var pay = parseFloat(document.getElementById('oPay').value);
    if(!pay){ toast('Pay rate is required','error'); return; }
    var body = {
        candidate_id: candId,
        pay_rate: pay,
        pay_type: document.getElementById('oPayType').value,
        employment_type: document.getElementById('oTy').value,
        location: document.getElementById('oLo').value||null,
        required_documents: document.getElementById('oDo').value||null,
        deadline_days: +document.getElementById('oDy').value||20,
        start_date: document.getElementById('oStart').value||null
    };
    var r = await apiFetch('/api/offers', {method:'POST', body:JSON.stringify(body)});
    if(!r) return;
    closeModal('offer');
    toast('Offer letter sent!');
    if(typeof pageRefresh==='function') pageRefresh();
}

function openPrescreenModal(candId, candName){
    document.getElementById('prescreenBody').innerHTML =
      '<input type="hidden" id="psId" value="'+candId+'">'
      +'<div class="form-group"><label>Candidate</label><input value="'+esc(candName)+'" disabled></div>'
      +'<div class="form-group"><label>Highest Education</label><select id="psEdu"><option value="high_school">High School</option><option value="associates">Associates</option><option value="bachelors">Bachelors</option><option value="masters">Masters</option><option value="doctorate">Doctorate</option></select></div>'
      +'<div class="form-group"><label>Years of Experience</label><input type="number" id="psExp" value="0"></div>'
      +'<div class="form-group"><label>Active Licenses</label><input id="psLic" placeholder="e.g. LMSW, LPC"></div>'
      +'<div class="form-group"><label>Availability</label><select id="psAv"><option value="full_time">Full-Time</option><option value="part_time">Part-Time</option><option value="either">Either</option><option value="contractor">1099</option></select></div>'
      +'<div class="form-group"><label>Earliest Start Date</label><input type="date" id="psSt"></div>'
      +'<div class="form-group"><label>Notes</label><textarea id="psN" rows="3"></textarea></div>';
    openModal('prescreen');
}

async function savePrescreen(){
    var candId = document.getElementById('psId').value;
    var body = {
        education_level: document.getElementById('psEdu').value,
        years_experience: +document.getElementById('psExp').value,
        licenses: document.getElementById('psLic').value||null,
        availability: document.getElementById('psAv').value,
        earliest_start_date: document.getElementById('psSt').value||null,
        additional_notes: document.getElementById('psN').value||null
    };
    var r = await apiFetch('/api/candidates/'+candId+'/prescreen', {method:'POST', body:JSON.stringify(body)});
    if(!r) return;
    closeModal('prescreen');
    toast('Pre-screening saved successfully!');
    if(typeof pageRefresh==='function') pageRefresh();
}
</script>

<?php /**PATH F:\laravel projects\hrportal\resources\views\partials\modals.blade.php ENDPATH**/ ?>