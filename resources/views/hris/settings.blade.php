@extends('layouts.app')
@section('title','McCrory Center — Settings')
@section('content')
<div class="animate-in">
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    <!-- System Settings -->
    <div class="card-section">
      <div class="section-title">⚙ System Settings</div>
      <form id="settingsForm" onsubmit="saveSettings(event)">
        <div class="form-group"><label>Company Name</label><input type="text" id="sCompanyName" placeholder="Acme Corp"></div>
        <div class="form-group"><label>Timezone</label>
          <select id="sTimezone">
            <option value="America/New_York">Eastern (ET)</option>
            <option value="America/Chicago">Central (CT)</option>
            <option value="America/Denver">Mountain (MT)</option>
            <option value="America/Los_Angeles">Pacific (PT)</option>
            <option value="UTC">UTC</option>
          </select>
        </div>
        <div class="form-group"><label>Default Interview Duration (min)</label><input type="number" id="sInterviewDur" min="15" max="180" step="15"></div>
        <div class="form-group"><label>Offer Deadline (days)</label><input type="number" id="sOfferDeadline" min="1" max="30"></div>
        <div class="form-group"><label>No-Response Follow-up Days</label><input type="number" id="sFollowupDays" min="1" max="30"></div>
        <div class="form-group"><label>Queue Hold Days</label><input type="number" id="sQueueDays" min="1" max="365"></div>
        <div class="form-group"><label>Default Interview Type</label>
          <select id="sInterviewType">
            <option value="zoom">Zoom</option>
            <option value="in_person">In Person</option>
            <option value="phone">Phone</option>
          </select>
        </div>
        <div class="form-group"><label>Zoom Link</label><input type="url" id="sZoomLink" placeholder="https://zoom.us/j/..."></div>
        <div class="form-group"><label>Office Address</label><input type="text" id="sOfficeAddr" placeholder="123 Main St..."></div>
        <div class="form-group"><label>Door Access Info</label><input type="text" id="sDoorAccess" placeholder="e.g. Code: 1234, Fob required"></div>
        <div class="form-group"><label>WiFi Password</label><input type="text" id="sWifiPass" placeholder="Office WiFi password"></div>
        <button type="submit" class="btn btn-primary" style="width:100%">Save Settings</button>
      </form>
    </div>

    <!-- HR Team -->
    <div class="card-section">
      <div class="section-title">👥 HR Team</div>
      <div id="hrTeamList"><div style="text-align:center;padding:30px;color:var(--text3)">⏳ Loading...</div></div>
    </div>

  </div>

  <!-- Email Templates -->
  <div class="card-section" style="margin-top:20px">
    <div class="section-title" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
      <span>📧 Email Templates</span>
      <button class="btn btn-primary btn-sm" onclick="openAddTemplate()">+ New Template</button>
    </div>

    <!-- Token legend -->
    <div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;margin-bottom:14px">
      <div style="font-size:12px;font-weight:600;color:var(--text2);margin-bottom:8px">Available Tokens — insert these in your templates and they'll be replaced with real data when sent:</div>
      <div id="tokenLegend" style="display:flex;flex-wrap:wrap;gap:6px"></div>
    </div>

    <div id="tplList"><div style="text-align:center;padding:30px;color:var(--text3)">⏳ Loading…</div></div>
  </div>

  <!-- SMTP Settings -->
  <div class="card-section" style="margin-top:20px">
    <div class="section-title">📤 SMTP / Email Sending</div>
    <form onsubmit="saveSmtp(event)">
      <div class="form-row">
        <div class="form-group"><label>SMTP Host</label><input type="text" id="sSmtpHost" placeholder="smtp.gmail.com"></div>
        <div class="form-group"><label>SMTP Port</label><input type="number" id="sSmtpPort" placeholder="587"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Username / Email</label><input type="text" id="sSmtpUser" placeholder="you@example.com"></div>
        <div class="form-group"><label>Password</label><input type="password" id="sSmtpPass" placeholder="••••••••"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>From Email</label><input type="email" id="sSmtpFromEmail" placeholder="hr@yourcompany.com"></div>
        <div class="form-group"><label>From Name</label><input type="text" id="sSmtpFromName" placeholder="HR Team"></div>
      </div>
      <div class="form-group"><label>Encryption</label>
        <select id="sSmtpEnc">
          <option value="tls">TLS (recommended)</option>
          <option value="ssl">SSL</option>
          <option value="">None</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Save SMTP Settings</button>
    </form>
  </div>

  <!-- Twilio / SMS Settings -->
  <div class="card-section" style="margin-top:20px">
    <div class="section-title">💬 Twilio SMS Settings</div>
    <form onsubmit="saveTwilio(event)">
      <div class="form-group"><label>Account SID</label><input type="text" id="sTwilioSid" placeholder="ACxxxxxxxxxxxxxxxx"></div>
      <div class="form-group"><label>Auth Token</label><input type="password" id="sTwilioToken" placeholder="••••••••"></div>
      <div class="form-group"><label>From Phone Number (E.164 format)</label><input type="tel" id="sTwilioFrom" placeholder="+12025551234"></div>
      <button type="submit" class="btn btn-primary">Save Twilio Settings</button>
    </form>
  </div>

</div>

<!-- Template create/edit modal -->
<div class="modal-overlay" id="modal-tplModal" onclick="if(event.target===this)closeModal('tplModal')">
  <div class="modal" style="max-width:720px">
    <div class="modal-header">
      <h3 id="tplModalTitle">New Email Template</h3>
      <button onclick="closeModal('tplModal')">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="tplEditId">
      <div class="form-row">
        <div class="form-group"><label>Template Name *</label><input id="tplName" placeholder="e.g. Rejection Notice"></div>
        <div class="form-group"><label>Category</label>
          <select id="tplCategory">
            <option value="">— Select category —</option>
            <option value="candidate">Candidate</option>
            <option value="offer">Offer</option>
            <option value="onboarding">Onboarding</option>
            <option value="rejection">Rejection</option>
            <option value="general">General</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Subject *</label><input id="tplSubject" placeholder="Subject line…"></div>

      <!-- Token inserter -->
      <div style="margin-bottom:8px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <span style="font-size:12px;font-weight:600;color:var(--text2)">Insert token:</span>
        <select id="tplTokenSelect" style="font-size:12px;max-width:280px" onchange="insertTplToken()">
          <option value="">— Pick a token to insert —</option>
        </select>
      </div>

      <div class="form-group"><label>Body (plain text — tokens will be replaced on send) *</label><textarea id="tplBody" rows="10" placeholder="Dear @{{candidate_name}},&#10;&#10;We are pleased to offer you the position of @{{role}} at @{{company_name}}…"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('tplModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
    </div>
  </div>
</div>
    <div class="section-title" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0">
      <span>🗂 Job Categories</span>
      <button class="btn btn-primary btn-sm" onclick="openAddCategory()">+ Add Category</button>
    </div>
    <div id="catList" style="margin-top:14px"><div style="text-align:center;padding:20px;color:var(--text3)">⏳ Loading...</div></div>
  </div>

</div>

<!-- Edit/Add Category Modal -->
<div class="modal-overlay" id="modal-catModal" onclick="if(event.target===this)closeModal('catModal')">
  <div class="modal" style="max-width:380px">
    <div class="modal-header"><h3 id="catModalTitle">Add Category</h3><button onclick="closeModal('catModal')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="catEditId">
      <div class="form-group"><label>Category Name *</label><input id="catName" placeholder="e.g. Licensed Clinician"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('catModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveCategory()">Save</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _categories = [];
var _tplTokens  = [];

async function pageRefresh(){ await Promise.all([loadSettings(), loadHrTeam(), loadCategories(), loadEmailTemplates(), loadTokens()]); }

async function loadSettings(){
    var r = await apiFetch('/api/settings');
    if(!r) return;
    var data = await r.json();
    var s = data.settings||data||{};
    var set = function(id,val){ var el=document.getElementById(id); if(el&&val!=null) el.value=val; };
    set('sCompanyName',   s.company_name);
    set('sTimezone',      s.timezone);
    set('sInterviewDur',  s.interview_duration||45);
    set('sOfferDeadline', s.offer_deadline||7);
    set('sFollowupDays',  s.followup_days||3);
    set('sQueueDays',     s.queue_days||90);
    set('sInterviewType', s.default_interview_type||'zoom');
    set('sZoomLink',      s.zoom_link);
    set('sOfficeAddr',    s.office_address);
    set('sDoorAccess',    s.door_access_info);
    set('sWifiPass',      s.wifi_password);
    // SMTP
    set('sSmtpHost',      s.smtp_host);
    set('sSmtpPort',      s.smtp_port||587);
    set('sSmtpUser',      s.smtp_username);
    set('sSmtpFromEmail', s.smtp_from_email);
    set('sSmtpFromName',  s.smtp_from_name);
    var encEl = document.getElementById('sSmtpEnc');
    if(encEl && s.smtp_encryption != null) encEl.value = s.smtp_encryption;
    // Twilio
    set('sTwilioSid',   s.twilio_account_sid);
    set('sTwilioFrom',  s.twilio_from_number);
}

async function saveSettings(e){
    e.preventDefault();
    var payload = {
        company_name:            document.getElementById('sCompanyName').value,
        timezone:                document.getElementById('sTimezone').value,
        interview_duration:      parseInt(document.getElementById('sInterviewDur').value)||45,
        offer_deadline:          parseInt(document.getElementById('sOfferDeadline').value)||7,
        followup_days:           parseInt(document.getElementById('sFollowupDays').value)||3,
        queue_days:              parseInt(document.getElementById('sQueueDays').value)||90,
        default_interview_type:  document.getElementById('sInterviewType').value,
        zoom_link:               document.getElementById('sZoomLink').value,
        office_address:          document.getElementById('sOfficeAddr').value,
        door_access_info:        document.getElementById('sDoorAccess').value,
        wifi_password:           document.getElementById('sWifiPass').value
    };
    var r = await apiFetch('/api/settings', {method:'PUT', body:JSON.stringify(payload)});
    if(!r) return;
    toast('Settings saved!');
}

async function loadHrTeam(){
    var r = await apiFetch('/api/settings/hr-team');
    if(!r) return;
    var data = await r.json();
    var team = data.team || data || [];
    var el = document.getElementById('hrTeamList');
    if(!team.length){ el.innerHTML='<div style="color:var(--text3)">No HR staff found.</div>'; return; }
    el.innerHTML = team.map(function(u){
        return '<div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)">'
          +'<div style="width:36px;height:36px;border-radius:50%;background:'+Cl(u.id)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px">'+((u.first_name||'?')[0]).toUpperCase()+'</div>'
          +'<div style="flex:1">'
            +'<div style="font-weight:600;font-size:13px">'+esc(u.name||u.first_name+' '+u.last_name||'—')+'</div>'
            +'<div style="font-size:12px;color:var(--text3)">'+esc(u.email||'')+'</div>'
          +'</div>'
          +'<span class="badge badge-offer-sent" style="font-size:11px">'+esc(u.role||'hr_staff')+'</span>'
          +(u.active_candidates_count!=null?'<span style="font-size:12px;color:var(--text3)">'+u.active_candidates_count+' assigned</span>':'')
        +'</div>';
    }).join('');
}

document.addEventListener('DOMContentLoaded', pageRefresh);

async function saveSmtp(e){
    e.preventDefault();
    var payload = {
        smtp_host:       document.getElementById('sSmtpHost').value,
        smtp_port:       document.getElementById('sSmtpPort').value,
        smtp_username:   document.getElementById('sSmtpUser').value,
        smtp_password:   document.getElementById('sSmtpPass').value,
        smtp_from_email: document.getElementById('sSmtpFromEmail').value,
        smtp_from_name:  document.getElementById('sSmtpFromName').value,
        smtp_encryption: document.getElementById('sSmtpEnc').value
    };
    var r = await apiFetch('/api/settings', {method:'PUT', body:JSON.stringify(payload)});
    if(!r) return;
    toast('SMTP settings saved!');
}

async function saveTwilio(e){
    e.preventDefault();
    var payload = {
        twilio_account_sid: document.getElementById('sTwilioSid').value,
        twilio_auth_token:  document.getElementById('sTwilioToken').value,
        twilio_from_number: document.getElementById('sTwilioFrom').value
    };
    var r = await apiFetch('/api/settings', {method:'PUT', body:JSON.stringify(payload)});
    if(!r) return;
    toast('Twilio settings saved!');
}

async function loadTokens(){
    var r = await apiFetch('/api/settings/email-tokens');
    if(!r) return;
    var data = await r.json();
    _tplTokens = data.tokens||data||[];
    // render legend chips
    var legend = document.getElementById('tokenLegend');
    if(legend){
        legend.innerHTML = _tplTokens.map(function(t){
            return '<span title="'+esc(t.label)+'" style="cursor:pointer;background:var(--surface);border:1px solid var(--border);border-radius:4px;padding:2px 8px;font-size:11px;font-family:monospace;color:var(--accent)" onclick="navigator.clipboard&&navigator.clipboard.writeText(\''+esc(t.token)+'\')">'+esc(t.token)+'</span>';
        }).join('');
    }
    // populate template token select
    var sel = document.getElementById('tplTokenSelect');
    if(sel){
        // group by group field
        var groups = {};
        _tplTokens.forEach(function(t){ var g=t.group||'General'; if(!groups[g]) groups[g]=[]; groups[g].push(t); });
        sel.innerHTML = '<option value="">— Pick a token to insert —</option>';
        Object.keys(groups).forEach(function(g){
            var og = document.createElement('optgroup'); og.label = g;
            groups[g].forEach(function(t){
                var o = document.createElement('option'); o.value = t.token; o.textContent = t.label+' ('+t.token+')'; og.appendChild(o);
            });
            sel.appendChild(og);
        });
    }
}

/* ---- Email Templates ---- */
async function loadEmailTemplates(){
    var r = await apiFetch('/api/settings/email-templates');
    if(!r) return;
    var data = await r.json();
    var tpls = data.templates||data||[];
    var el = document.getElementById('tplList');
    if(!tpls.length){ el.innerHTML='<div style="color:var(--text3);padding:14px 0">No templates yet. Click "+ New Template" to create one.</div>'; return; }
    el.innerHTML = '<table style="width:100%;border-collapse:collapse;font-size:13px">'
      +'<thead><tr style="border-bottom:2px solid var(--border)">'
        +'<th style="text-align:left;padding:8px 10px">Name</th>'
        +'<th style="text-align:left;padding:8px 10px">Slug</th>'
        +'<th style="text-align:left;padding:8px 10px">Category</th>'
        +'<th style="padding:8px 10px">Active</th>'
        +'<th style="padding:8px 10px">Actions</th>'
      +'</tr></thead><tbody>'
      + tpls.map(function(t){
          return '<tr style="border-bottom:1px solid var(--border)">'
            +'<td style="padding:8px 10px;font-weight:500">'+esc(t.name)+'</td>'
            +'<td style="padding:8px 10px;font-family:monospace;font-size:12px;color:var(--text3)">'+esc(t.slug||'')+'</td>'
            +'<td style="padding:8px 10px">'+esc(t.category||'—')+'</td>'
            +'<td style="padding:8px 10px;text-align:center">'+(t.is_active?'✅':'❌')+'</td>'
            +'<td style="padding:8px 10px;text-align:center;white-space:nowrap">'
              +'<button class="btn btn-warning btn-sm" onclick="openEditTemplate('+t.id+')">✏ Edit</button> '
              +'<button class="btn btn-danger btn-sm" onclick="deleteTemplate('+t.id+',\''+esc(t.name)+'\')">🗑</button>'
            +'</td>'
          +'</tr>';
      }).join('')
      +'</tbody></table>';
}

function openAddTemplate(){
    document.getElementById('tplEditId').value = '';
    document.getElementById('tplName').value    = '';
    document.getElementById('tplSubject').value = '';
    document.getElementById('tplBody').value    = '';
    document.getElementById('tplCategory').value = '';
    document.getElementById('tplModalTitle').textContent = 'New Email Template';
    loadTokens();
    openModal('tplModal');
}

async function openEditTemplate(id){
    var r = await apiFetch('/api/settings/email-templates/'+id);
    if(!r) return;
    var t = await r.json();
    t = t.template||t;
    document.getElementById('tplEditId').value  = t.id;
    document.getElementById('tplName').value    = t.name||'';
    document.getElementById('tplSubject').value = t.subject||'';
    document.getElementById('tplBody').value    = t.body||'';
    document.getElementById('tplCategory').value= t.category||'';
    document.getElementById('tplModalTitle').textContent = 'Edit Template';
    loadTokens();
    openModal('tplModal');
}

function insertTplToken(){
    var sel = document.getElementById('tplTokenSelect');
    var token = sel.value; if(!token) return;
    var ta = document.getElementById('tplBody');
    var start = ta.selectionStart, end = ta.selectionEnd;
    ta.value = ta.value.substring(0,start) + token + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + token.length;
    ta.focus();
    sel.value = '';
}

async function saveTemplate(){
    var id      = document.getElementById('tplEditId').value;
    var name    = document.getElementById('tplName').value.trim();
    var subject = document.getElementById('tplSubject').value.trim();
    var body    = document.getElementById('tplBody').value;
    var category= document.getElementById('tplCategory').value;
    if(!name||!subject||!body){ toast('Name, subject and body are required','error'); return; }
    var payload = {name:name, subject:subject, body:body, category:category, is_active:true};
    var r;
    if(id){
        r = await apiFetch('/api/settings/email-templates/'+id, {method:'PUT', body:JSON.stringify(payload)});
    } else {
        r = await apiFetch('/api/settings/email-templates', {method:'POST', body:JSON.stringify(payload)});
    }
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||(e.errors?Object.values(e.errors).flat()[0]:'Save failed'),'error'); return; }
    closeModal('tplModal');
    toast(id ? 'Template updated!' : 'Template created!');
    loadEmailTemplates();
}

async function deleteTemplate(id, name){
    if(!confirm('Delete template "'+name+'"? This cannot be undone.')) return;
    var r = await apiFetch('/api/settings/email-templates/'+id, {method:'DELETE'});
    if(!r) return;
    toast('"'+name+'" deleted.');
    loadEmailTemplates();
}

async function loadCategories(){
    var r = await apiFetch('/api/job-categories');
    if(!r) return;
    var data = await r.json();
    _categories = data || [];
    renderCategories();
}

function renderCategories(){
    var el = document.getElementById('catList');
    if(!el) return;
    if(!_categories.length){ el.innerHTML='<div style="color:var(--text3);padding:10px 0">No categories yet. Add one above.</div>'; return; }
    el.innerHTML = _categories.map(function(c){
        return '<div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">'
          +'<span style="flex:1;font-weight:500;font-size:13px">'+esc(c.name)+'</span>'
          +'<button class="btn btn-warning btn-sm" onclick="openEditCategory('+c.id+')">✏ Edit</button>'
          +'<button class="btn btn-danger btn-sm" onclick="deleteCategory('+c.id+',\''+esc(c.name)+'\')">🗑</button>'
        +'</div>';
    }).join('');
}

function openAddCategory(){
    document.getElementById('catEditId').value = '';
    document.getElementById('catName').value   = '';
    document.getElementById('catModalTitle').textContent = 'Add Category';
    openModal('catModal');
}

function openEditCategory(id){
    var c = _categories.find(function(x){ return x.id===id; });
    if(!c) return;
    document.getElementById('catEditId').value = c.id;
    document.getElementById('catName').value   = c.name;
    document.getElementById('catModalTitle').textContent = 'Edit Category';
    openModal('catModal');
}

async function saveCategory(){
    var name   = document.getElementById('catName').value.trim();
    var editId = document.getElementById('catEditId').value;
    if(!name){ toast('Name is required','error'); return; }
    var r;
    if(editId){
        r = await apiFetch('/api/job-categories/'+editId, {method:'PATCH', body:JSON.stringify({name:name})});
    } else {
        r = await apiFetch('/api/job-categories', {method:'POST', body:JSON.stringify({name:name})});
    }
    if(!r) return;
    if(!r.ok){ var e=await r.json(); toast(e.message||(e.errors?Object.values(e.errors).flat()[0]:'Save failed'),'error'); return; }
    closeModal('catModal');
    toast(editId ? 'Category updated!' : 'Category added!');
    loadCategories();
}

async function deleteCategory(id, name){
    if(!confirm('Deactivate "'+name+'"? It will no longer appear in the apply form.')) return;
    var r = await apiFetch('/api/job-categories/'+id, {method:'DELETE'});
    if(!r) return;
    toast('"'+name+'" deactivated.');
    loadCategories();
}
</script>
@endpush
