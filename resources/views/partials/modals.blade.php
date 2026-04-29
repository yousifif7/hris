<div class="modal-overlay" id="modal-candidateDetail"><div class="modal" style="max-width:760px"><div class="modal-header"><h3 id="detailName"></h3><button onclick="closeModal('candidateDetail')">✕</button></div><div class="modal-body" id="detailBody"></div><div class="modal-footer" id="detailFooter"></div></div></div>

<!-- Quick Email modal -->
<div class="modal-overlay" id="modal-quickEmailModal" onclick="if(event.target===this)closeModal('quickEmailModal')">
  <div class="modal" style="max-width:580px">
    <div class="modal-header"><h3>Send Email to Candidate</h3><button onclick="closeModal('quickEmailModal')">✕</button></div>
    <div class="modal-body">
      <!-- Recipient chips + search -->
      <div class="form-group">
        <label>To</label>
        <div style="border:1px solid var(--border);border-radius:var(--radius);padding:6px 10px;min-height:40px;display:flex;flex-wrap:wrap;gap:6px;align-items:center;cursor:text" onclick="document.getElementById('qeSearch').focus()">
          <div id="qeSelected" style="display:contents"></div>
          <input id="qeSearch" type="text" placeholder="Search candidates…" style="border:none;outline:none;background:transparent;font-size:13px;flex:1;min-width:140px" oninput="qeSearchCandidates()">
        </div>
        <div id="qeDropdown" style="display:none;position:absolute;z-index:1200;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);min-width:320px;max-height:220px;overflow-y:auto"></div>
        <div id="qeSendCount" style="font-size:11px;color:var(--text3);margin-top:4px"></div>
      </div>
      <div class="form-group"><label>Subject *</label><input id="qeSubject" placeholder="Subject…"></div>
      <div class="form-group"><label>Message *</label><textarea id="qeBody" rows="8" placeholder="Type your message… use @{{candidate_name}} etc."></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('quickEmailModal')">Cancel</button>
      <button class="btn btn-primary" onclick="sendQuickEmail()">Send Email</button>
    </div>
  </div>
</div>

<!-- Quick SMS modal -->
<div class="modal-overlay" id="modal-quickSmsModal" onclick="if(event.target===this)closeModal('quickSmsModal')">
  <div class="modal" style="max-width:460px">
    <div class="modal-header"><h3>Send SMS to Candidate</h3><button onclick="closeModal('quickSmsModal')">✕</button></div>
    <div class="modal-body">
      <!-- Recipient chips + search -->
      <div class="form-group">
        <label>To</label>
        <div style="border:1px solid var(--border);border-radius:var(--radius);padding:6px 10px;min-height:40px;display:flex;flex-wrap:wrap;gap:6px;align-items:center;cursor:text" onclick="document.getElementById('qsSearch').focus()">
          <div id="qsSelected" style="display:contents"></div>
          <input id="qsSearch" type="text" placeholder="Search candidates…" style="border:none;outline:none;background:transparent;font-size:13px;flex:1;min-width:140px" oninput="qsSearchCandidates()">
        </div>
        <div id="qsDropdown" style="display:none;position:absolute;z-index:1200;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);min-width:300px;max-height:220px;overflow-y:auto"></div>
        <div id="qsSendCount" style="font-size:11px;color:var(--text3);margin-top:4px"></div>
      </div>
      <div class="form-group">
        <label>Message * <span style="float:right;font-size:11px;color:var(--text3)"><span id="qsCharCount">0</span>/160</span></label>
        <textarea id="qsBody" rows="5" placeholder="Type your SMS…" maxlength="160" oninput="qsUpdateCount()"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('quickSmsModal')">Cancel</button>
      <button class="btn btn-primary" onclick="sendQuickSms()">Send SMS</button>
    </div>
  </div>
</div>

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

<div class="modal-overlay" id="modal-interviewAvailability"><div class="modal" style="max-width:620px">
  <div class="modal-header"><h3>Candidate Scheduling Availability</h3><button onclick="closeModal('interviewAvailability')">✕</button></div>
  <div class="modal-body">
    <input type="hidden" id="avCandId">
    <div class="form-group"><label>Candidate</label><input id="avCandName" disabled></div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end;margin-bottom:14px">
      <div class="form-group" style="margin:0"><label>Date</label><input type="date" id="avDate"></div>
      <div class="form-group" style="margin:0"><label>Start Time</label><input type="time" id="avStart" value="09:00"></div>
      <div class="form-group" style="margin:0"><label>End Time</label><input type="time" id="avEnd" value="09:30"></div>
      <button class="btn btn-secondary btn-sm" onclick="addAvailabilitySlot()">+ Add</button>
    </div>
    <div id="avSlotList" style="display:grid;gap:8px"></div>
    <div style="margin-top:10px;font-size:12px;color:var(--text3)">Add one or more available days and times. Candidate will only be able to choose from these slots.</div>
  </div>
  <div class="modal-footer">
    <button class="btn btn-secondary" onclick="closeModal('interviewAvailability')">Cancel</button>
    <button class="btn btn-primary" onclick="saveAvailabilityAndSendInvite()">Save Slots & Send Invite</button>
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
var _availabilitySlots = [];
var _availabilityDoneCb = null;

function openInterviewAvailability(candId, candName, doneCb){
  _availabilitySlots = [];
  _availabilityDoneCb = doneCb || null;
  document.getElementById('avCandId').value = candId;
  document.getElementById('avCandName').value = candName;
  document.getElementById('avDate').value = '';
  document.getElementById('avStart').value = '09:00';
  document.getElementById('avEnd').value = '09:30';
  renderAvailabilitySlots();
  openModal('interviewAvailability');
}

function addAvailabilitySlot(){
  var d = document.getElementById('avDate').value;
  var st = document.getElementById('avStart').value;
  var en = document.getElementById('avEnd').value;
  if(!d || !st || !en){ toast('Please select date and time range', 'error'); return; }
  if(en <= st){ toast('End time must be after start time', 'error'); return; }
  var startsAt = d + 'T' + st + ':00';
  var endsAt = d + 'T' + en + ':00';
  if(_availabilitySlots.some(function(s){ return s.starts_at===startsAt && s.ends_at===endsAt; })){
    toast('This slot is already added', 'error');
    return;
  }
  _availabilitySlots.push({starts_at: startsAt, ends_at: endsAt});
  _availabilitySlots.sort(function(a,b){ return new Date(a.starts_at)-new Date(b.starts_at); });
  renderAvailabilitySlots();
}

function removeAvailabilitySlot(idx){
  _availabilitySlots.splice(idx, 1);
  renderAvailabilitySlots();
}

function renderAvailabilitySlots(){
  var wrap = document.getElementById('avSlotList');
  if(!_availabilitySlots.length){
    wrap.innerHTML = '<div style="padding:12px;border:1px dashed var(--border);border-radius:var(--radius);font-size:12px;color:var(--text3)">No slots added yet.</div>';
    return;
  }
  wrap.innerHTML = _availabilitySlots.map(function(slot, idx){
    var s = new Date(slot.starts_at);
    var e = new Date(slot.ends_at);
    return '<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius);background:var(--surface2)">'
      +'<div style="font-size:12px"><strong>'+esc(s.toLocaleDateString())+'</strong> · '+esc(s.toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}))+' - '+esc(e.toLocaleTimeString([], {hour:'numeric', minute:'2-digit'}))+'</div>'
      +'<button class="btn btn-danger btn-sm" onclick="removeAvailabilitySlot('+idx+')">Remove</button></div>';
  }).join('');
}

async function saveAvailabilityAndSendInvite(){
  var candId = document.getElementById('avCandId').value;
  if(!candId){ toast('Candidate is required', 'error'); return; }
  if(!_availabilitySlots.length){ toast('Add at least one availability slot', 'error'); return; }

  var rSlots = await apiFetch('/api/candidates/'+candId+'/interview-slots', {
    method:'POST',
    body:JSON.stringify({ slots: _availabilitySlots })
  });
  if(!rSlots) return;
  if(!rSlots.ok){
    var es = await rSlots.json();
    toast(es.message || (es.errors ? Object.values(es.errors).flat()[0] : 'Failed to save slots'), 'error');
    return;
  }

  var rInvite = await apiFetch('/api/candidates/'+candId+'/status', {
    method:'PATCH',
    body:JSON.stringify({status:'invite_sent'})
  });
  if(!rInvite) return;
  if(!rInvite.ok){
    var ei = await rInvite.json();
    toast(ei.message || (ei.errors ? Object.values(ei.errors).flat()[0] : 'Failed to send invite'), 'error');
    return;
  }

  closeModal('interviewAvailability');
  toast('Interview invite sent with selectable calendar slots!', 'success');
  if(typeof _availabilityDoneCb === 'function') _availabilityDoneCb();
}

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
    sel.innerHTML = '<option value="">⏳ Loading...</option>';
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
        +'<div class="form-group"><label>Pay Rate *</label><input id="oPay" type="number" step="0.01" placeholder="25.00"></div>'
        +'<div class="form-group"><label>Pay Type</label><select id="oPayType"><option value="hourly">Hourly</option><option value="salary">Salary</option></select></div>'
      +'</div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Employment Type</label><select id="oTy"><option>Full-Time</option><option>Part-Time</option><option>1099</option></select></div>'
        +'<div class="form-group"><label>Location</label><input id="oLo" placeholder="Main Office"></div>'
      +'</div>'
      +'<div class="form-group"><label>Required Documents</label><textarea id="oDo" rows="2">Signed offer, I-9, W-4, License, Credentials</textarea></div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Start Date</label><input type="date" id="oStart"></div>'
        +'<div class="form-group"><label>Orientation Date</label><input type="date" id="oOrient"></div>'
      +'</div>'
      +'<div class="form-row">'
        +'<div class="form-group"><label>Deadline to Respond (days)</label><input type="number" id="oDy" value="7" min="1"></div>'
      +'</div>'
      +'<div class="form-group"><label>Internal Notes <span style="font-size:11px;color:var(--text3)">(not sent to candidate)</span></label>'
        +'<textarea id="oNotes" rows="2" placeholder="Any internal notes about this offer…"></textarea></div>'
      +'<div style="background:var(--accent-glow);border:1px solid rgba(90,198,204,.2);border-radius:var(--radius);padding:10px 14px;font-size:12px;color:var(--text2);margin-top:4px">'
        +'<strong>📧 An offer email</strong> with all details and a personalised accept/decline link will be sent automatically to the candidate.</div>';
    openModal('offer');
}

async function sendOffer(){
    var candId = +document.getElementById('oId').value;
    var pay = parseFloat(document.getElementById('oPay').value);
    if(!candId){ toast('Candidate not set','error'); return; }
    if(!pay || pay <= 0){ toast('Pay rate is required','error'); return; }
    var body = {
        candidate_id: candId,
        pay_rate: pay,
        pay_type: document.getElementById('oPayType').value,
        employment_type: document.getElementById('oTy').value,
        location: document.getElementById('oLo').value||null,
        required_documents: document.getElementById('oDo').value||null,
        deadline_days: +document.getElementById('oDy').value||7,
        start_date: document.getElementById('oStart').value||null,
        orientation_date: document.getElementById('oOrient').value||null,
        notes: document.getElementById('oNotes').value||null
    };
    var btn = document.querySelector('#modal-offer .btn-primary');
    if(btn){ btn.disabled=true; btn.textContent='Sending…'; }
    var r = await apiFetch('/api/offers', {method:'POST', body:JSON.stringify(body)});
    if(btn){ btn.disabled=false; btn.textContent='Send Offer'; }
    if(!r) return;
    if(!r.ok){
        var e = await r.json();
        toast(e.message||(e.errors?Object.values(e.errors).flat()[0]:'Failed to send offer'),'error');
        return;
    }
    closeModal('offer');
    toast('✉ Offer sent — candidate will receive an email with accept/decline link!', 'success');
    if(typeof pageRefresh==='function') pageRefresh();
    updateReviewBadge();
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

