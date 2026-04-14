
<?php $__env->startSection('title','TalentFlow — Automations & Rules'); ?>
<?php $__env->startSection('content'); ?>
<div class="animate-in">

  <!-- Automation Rules -->
  <div class="section-title">⚡ Automation Rules</div>
  <div id="rulesList"><div style="text-align:center;padding:40px;color:var(--text3)">⏳ Loading…</div></div>

  <!-- Email Templates -->
  <div class="section-title" style="margin-top:28px">✉ Email Templates</div>
  <div id="tplList"><div style="text-align:center;padding:40px;color:var(--text3)">⏳ Loading…</div></div>

</div>

<!-- Edit Template Modal -->
<div class="modal-overlay" id="modal-tplModal" onclick="if(event.target===this)closeModal('tplModal')">
  <div class="modal" style="max-width:560px">
    <div class="modal-header"><h3>Edit Email Template</h3><button onclick="closeModal('tplModal')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="tplId">
      <div class="form-group"><label>Subject</label><input type="text" id="tplSubject" placeholder="Email subject…"></div>
      <div class="form-group"><label>Body</label><textarea id="tplBody" rows="8" placeholder="Email body… Use {{candidate_name}}, {{company_name}}, etc."></textarea></div>
      <div style="font-size:12px;color:var(--text3)">Available variables: <code>{{candidate_name}}</code> <code>{{company_name}}</code> <code>{{interview_date}}</code> <code>{{offer_details}}</code></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('tplModal')">Cancel</button>
      <button class="btn btn-primary" onclick="saveTemplate()">Save Template</button>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
async function pageRefresh(){ await Promise.all([loadRules(), loadTemplates()]); }

var _tpls = {};

async function loadRules(){
    var r = await apiFetch('/api/settings/automations');
    if(!r) return;
    var data = await r.json();
    var rules = data.rules || data || [];
    var el = document.getElementById('rulesList');
    if(!rules.length){ el.innerHTML='<div style="padding:20px;color:var(--text3)">No automation rules configured.</div>'; return; }
    el.innerHTML = '<div class="card-section" style="padding:0">'
      + rules.map(function(rule){
          var isOn = rule.is_active||rule.active;
          return '<div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">'
            +'<div style="flex:1">'
              +'<div style="font-weight:600;font-size:14px">'+esc(rule.name||rule.trigger_event||'—')+'</div>'
              +'<div style="font-size:12px;color:var(--text3)">'+esc(rule.description||('Trigger: '+(rule.trigger_event||'—')+' → '+(rule.action_type||'—')))+'</div>'
            +'</div>'
            +'<span class="badge '+(isOn?'badge-offer-accepted':'badge-rejected')+'">'+(isOn?'Active':'Inactive')+'</span>'
          +'</div>';
      }).join('')
      +'</div>';
}

async function loadTemplates(){
    var r = await apiFetch('/api/settings/email-templates');
    if(!r) return;
    var data = await r.json();
    var tpls = data.templates || data || [];
    _tpls = {};
    tpls.forEach(function(t){ _tpls[t.id] = t; });
    var el = document.getElementById('tplList');
    if(!tpls.length){ el.innerHTML='<div style="padding:20px;color:var(--text3)">No email templates found.</div>'; return; }
    el.innerHTML = '<div class="card-section" style="padding:0">'
      + tpls.map(function(t){
          return '<div style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border)">'
            +'<div style="flex:1">'
              +'<div style="font-weight:600;font-size:14px">'+esc(t.name||'—')+'</div>'
              +'<div style="font-size:12px;color:var(--text3)">'+esc(t.subject||'No subject')+'</div>'
            +'</div>'
            +'<button class="btn btn-secondary btn-sm" onclick="editTemplate('+t.id+')">Edit</button>'
          +'</div>';
      }).join('')
      +'</div>';
}

function editTemplate(id){
    var t = _tpls[id];
    if(!t) return;
    document.getElementById('tplId').value      = t.id;
    document.getElementById('tplSubject').value = t.subject||'';
    document.getElementById('tplBody').value    = t.body||'';
    openModal('tplModal');
}

async function saveTemplate(){
    var id = document.getElementById('tplId').value;
    var r  = await apiFetch('/api/settings/email-templates/'+id, {method:'PUT', body:JSON.stringify({
        subject: document.getElementById('tplSubject').value,
        body:    document.getElementById('tplBody').value
    })});
    if(!r) return;
    toast('Template saved!');
    closeModal('tplModal');
    loadTemplates();
}

document.addEventListener('DOMContentLoaded', pageRefresh);
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\laravel projects\hrportal\resources\views/hris/automations.blade.php ENDPATH**/ ?>