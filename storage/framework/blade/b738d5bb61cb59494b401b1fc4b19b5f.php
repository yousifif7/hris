
<?php $__env->startSection('title','TalentFlow — Settings'); ?>
<?php $__env->startSection('content'); ?>
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
        <div class="form-group"><label>Zoom Link</label><input type="url" id="sZoomLink" placeholder="https://zoom.us/j/…"></div>
        <div class="form-group"><label>Office Address</label><input type="text" id="sOfficeAddr" placeholder="123 Main St…"></div>
        <button type="submit" class="btn btn-primary" style="width:100%">Save Settings</button>
      </form>
    </div>

    <!-- HR Team -->
    <div class="card-section">
      <div class="section-title">👥 HR Team</div>
      <div id="hrTeamList"><div style="text-align:center;padding:30px;color:var(--text3)">⏳ Loading…</div></div>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
async function pageRefresh(){ await Promise.all([loadSettings(), loadHrTeam()]); }

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
        office_address:          document.getElementById('sOfficeAddr').value
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
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH F:\laravel projects\hrportal\resources\views/hris/settings.blade.php ENDPATH**/ ?>