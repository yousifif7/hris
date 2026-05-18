@extends('layouts.app')
@section('title','McCrory Center — Staff Portals')
@section('content')
<style>
  .sp-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:14px;flex-wrap:wrap}
  .sp-title{display:flex;align-items:center;gap:10px}
  .sp-title .dot{width:8px;height:8px;background:var(--accent);border-radius:2px;display:inline-block}
  .sp-title h2{font-size:18px;font-weight:600;color:var(--text)}
  .sp-search-wrap{display:flex;align-items:center;gap:6px;border:1px solid var(--border);border-radius:6px;padding:2px 4px;background:var(--surface);flex:1;min-width:280px;max-width:640px}
  .sp-scope-btn{background:transparent;border:none;color:var(--text2);padding:6px 10px;font-size:13px;font-weight:600;border-right:1px solid var(--border);cursor:pointer}
  .sp-search{flex:1;border:none;outline:none;background:transparent;padding:6px 8px;font-size:13px;color:var(--text)}
  .sp-icon-btn{background:transparent;border:none;color:var(--text3);width:30px;height:30px;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:14px}
  .sp-icon-btn:hover{background:var(--surface2);color:var(--text)}
  .sp-pager{display:flex;align-items:center;gap:6px;color:var(--text2);font-size:12px;margin-left:auto}
  .sp-pager .sp-icon-btn{font-size:18px;line-height:1}
  .sp-table{width:100%;border-collapse:separate;border-spacing:0;background:var(--surface);border:1px solid var(--border);border-radius:8px;overflow:hidden;font-size:13px}
  .sp-table thead th{text-align:left;padding:10px 14px;background:var(--surface);color:var(--text3);font-weight:500;font-size:12px;border-bottom:1px solid var(--border);white-space:nowrap}
  .sp-table tbody td{padding:11px 14px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
  .sp-table tbody tr:last-child td{border-bottom:none}
  .sp-table tbody tr:hover{background:var(--surface2)}
  .sp-link{color:var(--accent);font-weight:500;cursor:pointer;text-decoration:none}
  .sp-link:hover{text-decoration:underline}
  .sp-check{width:32px}
  .sp-scope-menu{position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:6px;min-width:160px;box-shadow:0 6px 20px rgba(0,0,0,.12);z-index:30;padding:4px;display:none}
  .sp-scope-menu.open{display:block}
  .sp-scope-menu .item{padding:7px 10px;font-size:13px;border-radius:4px;cursor:pointer;color:var(--text)}
  .sp-scope-menu .item:hover{background:var(--surface2)}
  .sp-scope-menu .item.active{background:var(--accent-glow);color:var(--accent);font-weight:600}
  .sp-empty{text-align:center;padding:50px;color:var(--text3)}
</style>

<div class="animate-in">
  <div class="sp-toolbar">
    <div class="sp-title">
      <span class="dot"></span>
      <h2>Staff Portals</h2>
    </div>
    <div style="flex:1"></div>
    <button class="btn btn-primary" onclick="openCreateStaffPortal()">+ Create Staff Portal</button>
    <button class="sp-icon-btn" title="Settings">⚙</button>
  </div>

  <div class="sp-toolbar">
    <div class="sp-search-wrap" style="position:relative">
      <button class="sp-scope-btn" onclick="toggleScopeMenu(event)"><span id="spScopeLabel">All</span> ▾</button>
      <input id="spSearch" class="sp-search" type="text" placeholder="Search by name or email…" oninput="spDebouncedSearch()">
      <button class="sp-icon-btn" onclick="loadStaffPortals()" title="Search">🔍</button>
      <button class="sp-icon-btn" title="More">⋮</button>
      <button class="sp-icon-btn" onclick="clearSpFilters()" title="Clear">✕</button>
      <div id="spScopeMenu" class="sp-scope-menu" style="top:38px;left:0">
        <div class="item active" data-scope="all" onclick="setScope('all','All')">All</div>
        <div class="item" data-scope="my" onclick="setScope('my','Only my')">Only my</div>
        <div class="item" data-scope="followed" onclick="setScope('followed','Followed')">Followed</div>
        <div class="item" data-scope="shared" onclick="setScope('shared','Shared')">Shared</div>
      </div>
    </div>
    <div class="sp-pager">
      <span id="spPagerLabel">— / —</span>
      <button class="sp-icon-btn" onclick="spPagePrev()" title="Previous">‹</button>
      <button class="sp-icon-btn" onclick="spPageNext()" title="Next">›</button>
    </div>
  </div>

  <table class="sp-table">
    <thead>
      <tr>
        <th class="sp-check"><input type="checkbox" onclick="spToggleAll(this)"></th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Anticipated Start Date</th>
        <th>Applicant Status</th>
        <th>Clinical License Expiration</th>
        <th>Interview Date And Time</th>
        <th>Authorization Background Check</th>
      </tr>
    </thead>
    <tbody id="spTbody">
      <tr><td colspan="8" class="sp-empty">Loading…</td></tr>
    </tbody>
  </table>
</div>

<!-- Create Staff Portal modal -->
<div class="modal-overlay" id="modal-createStaffPortal" onclick="if(event.target===this)closeModal('createStaffPortal')">
  <div class="modal" style="max-width:560px">
    <div class="modal-header">
      <h3>Create Staff Portal</h3>
      <button onclick="closeModal('createStaffPortal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label>First Name *</label><input id="cspFirst" required></div>
        <div class="form-group"><label>Last Name *</label><input id="cspLast" required></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Email</label><input id="cspEmail" type="email"></div>
        <div class="form-group"><label>Phone</label><input id="cspPhone" type="tel"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Category</label><select id="cspCategory"><option value="">Select…</option></select></div>
        <div class="form-group"><label>Source *</label>
          <select id="cspSource">
            <option>Indeed</option><option>LinkedIn</option><option>Referral</option>
            <option>Website</option><option>Walk-in</option><option>Other</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Anticipated Start Date</label><input id="cspStart" type="date"></div>
        <div class="form-group"><label>Clinical License Expiration</label><input id="cspLicense" type="date"></div>
      </div>
      <div class="form-group">
        <label>Authorization for Background Check</label>
        <input id="cspBgFile" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>
      <div class="form-group">
        <label>Notes</label>
        <textarea id="cspNotes" rows="3" placeholder="Anything HR should know…"></textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('createStaffPortal')">Cancel</button>
      <button class="btn btn-primary" onclick="submitCreateStaffPortal()">Create</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _spScope = 'all';
var _spPage = 1;
var _spPerPage = 30;
var _spSearchTimer = null;

async function pageRefresh(){ await loadStaffPortals(); }

function toggleScopeMenu(e){
    e.stopPropagation();
    document.getElementById('spScopeMenu').classList.toggle('open');
}
document.addEventListener('click', function(e){
    if(!e.target.closest('.sp-search-wrap')) {
        var m = document.getElementById('spScopeMenu'); if(m) m.classList.remove('open');
    }
});

function setScope(scope, label){
    _spScope = scope;
    _spPage = 1;
    document.getElementById('spScopeLabel').textContent = label;
    document.querySelectorAll('#spScopeMenu .item').forEach(function(el){
        el.classList.toggle('active', el.dataset.scope === scope);
    });
    document.getElementById('spScopeMenu').classList.remove('open');
    loadStaffPortals();
}

function clearSpFilters(){
    document.getElementById('spSearch').value = '';
    setScope('all', 'All');
}

function spDebouncedSearch(){
    clearTimeout(_spSearchTimer);
    _spSearchTimer = setTimeout(function(){ _spPage = 1; loadStaffPortals(); }, 250);
}

function spPagePrev(){ if(_spPage > 1){ _spPage--; loadStaffPortals(); } }
function spPageNext(){ _spPage++; loadStaffPortals(); }

function spToggleAll(box){
    document.querySelectorAll('#spTbody input[type=checkbox]').forEach(function(cb){ cb.checked = box.checked; });
}

async function loadStaffPortals(){
    var tbody = document.getElementById('spTbody');
    tbody.innerHTML = '<tr><td colspan="8" class="sp-empty">Loading…</td></tr>';

    // "Followed" and "Shared" aren't backed by tables yet — show an empty state for now.
    if(_spScope === 'followed' || _spScope === 'shared'){
        document.getElementById('spPagerLabel').textContent = '0 / 0';
        var label = _spScope === 'followed' ? 'followed' : 'shared with you';
        tbody.innerHTML = '<tr><td colspan="8" class="sp-empty">No staff portals '+label+' yet.<br><span style="font-size:12px;color:var(--text3)">This view is reserved for a future follow/share feature.</span></td></tr>';
        return;
    }

    var params = new URLSearchParams({
        page:     _spPage,
        per_page: _spPerPage,
        sort:     'last_name',
        direction:'asc',
        include:  'interviews'
    });
    var search = document.getElementById('spSearch').value.trim();
    if(search) params.set('search', search);

    if(_spScope === 'my'){
        var me = window.__currentUserId || (window.user && window.user.id);
        if(me){ params.set('assigned_to', me); }
    }

    var r = await apiFetch('/api/candidates?'+params.toString());
    if(!r) return;
    var data = await r.json();
    var rows = data.data || [];

    var from = data.from || 0;
    var to   = data.to   || 0;
    var tot  = data.total|| 0;
    document.getElementById('spPagerLabel').textContent = tot ? (from+'-'+to+' / '+tot) : '0 / 0';

    if(!rows.length){
        tbody.innerHTML = '<tr><td colspan="8" class="sp-empty">No staff portals found.</td></tr>';
        return;
    }

    tbody.innerHTML = rows.map(function(c){
        var nextInterview = (c.interviews || []).find(function(i){ return i.status === 'scheduled'; });
        var bgLink = c.authorization_background_check_path
            ? '<a class="sp-link" href="/'+esc(c.authorization_background_check_path)+'" target="_blank" rel="noopener">📎 '+esc(c.authorization_background_check_name || 'Document')+'</a>'
            : '<span style="color:var(--text3)">—</span>';

        return '<tr>'
            +'<td class="sp-check"><input type="checkbox"></td>'
            +'<td><a class="sp-link" href="/hris/staff-portals/'+c.id+'">'+esc(c.last_name||'')+'</a></td>'
            +'<td><a class="sp-link" href="/hris/staff-portals/'+c.id+'">'+esc(c.first_name||'')+'</a></td>'
            +'<td>'+(c.earliest_start_date ? esc(formatShortDate(c.earliest_start_date)) : '<span style="color:var(--text3)">—</span>')+'</td>'
            +'<td>'+(c.status ? B(c.status) : '<span style="color:var(--text3)">Not Selected</span>')+'</td>'
            +'<td>'+(c.clinical_license_expires_at ? esc(formatShortDate(c.clinical_license_expires_at)) : '<span style="color:var(--text3)">—</span>')+'</td>'
            +'<td>'+(nextInterview ? esc(fD(nextInterview.scheduled_at)) : '<span style="color:var(--text3)">—</span>')+'</td>'
            +'<td>'+bgLink+'</td>'
        +'</tr>';
    }).join('');
}

function formatShortDate(iso){
    if(!iso) return '';
    var d = new Date(iso);
    if(isNaN(d.getTime())) return iso;
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
}

/* ── Create Staff Portal modal ── */
async function openCreateStaffPortal(){
    // Load categories on first open
    var sel = document.getElementById('cspCategory');
    if(sel && sel.options.length <= 1){
        var r = await apiFetch('/api/job-categories');
        if(r){
            var cats = await r.json();
            (cats||[]).forEach(function(c){
                var o = document.createElement('option');
                o.value = c.id; o.textContent = c.name;
                sel.appendChild(o);
            });
        }
    }
    ['cspFirst','cspLast','cspEmail','cspPhone','cspStart','cspLicense','cspNotes'].forEach(function(id){
        var el = document.getElementById(id); if(el) el.value = '';
    });
    document.getElementById('cspBgFile').value = '';
    document.getElementById('cspSource').value = 'Indeed';
    document.getElementById('cspCategory').value = '';
    openModal('createStaffPortal');
}

async function submitCreateStaffPortal(){
    var first = document.getElementById('cspFirst').value.trim();
    var last  = document.getElementById('cspLast').value.trim();
    if(!first || !last){ toast('First and last name are required','error'); return; }

    var fd = new FormData();
    fd.append('first_name', first);
    fd.append('last_name',  last);
    fd.append('source', document.getElementById('cspSource').value || 'Other');

    var pairs = [
        ['email',  'cspEmail'],
        ['phone',  'cspPhone'],
        ['earliest_start_date',          'cspStart'],
        ['clinical_license_expires_at',  'cspLicense'],
        ['notes',  'cspNotes'],
    ];
    pairs.forEach(function(p){
        var v = (document.getElementById(p[1]).value || '').trim();
        if(v) fd.append(p[0], v);
    });

    var cat = document.getElementById('cspCategory').value;
    if(cat) fd.append('job_category_id', cat);

    var bg = document.getElementById('cspBgFile').files[0];
    if(bg) fd.append('authorization_background_check', bg);

    var r = await apiFetch('/api/candidates', { method:'POST', body: fd });
    if(!r) return;
    if(!r.ok){
        var e = await r.json().catch(function(){ return {}; });
        toast(e.message || 'Failed to create staff portal','error');
        return;
    }
    closeModal('createStaffPortal');
    toast('✓ Staff portal created for '+first+' '+last);
    loadStaffPortals();
}

document.addEventListener('DOMContentLoaded', loadStaffPortals);
</script>
@endpush
