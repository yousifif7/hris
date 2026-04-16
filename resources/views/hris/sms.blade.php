@extends('layouts.app')
@section('title','McCrory Center — SMS')
@section('content')
@verbatim
<style>
.sms-layout{display:grid;grid-template-columns:220px 1fr;height:calc(100vh - 120px);background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow)}
.sms-sidebar{border-right:1px solid var(--border);display:flex;flex-direction:column}
.sms-sidebar-hdr{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.sms-sidebar-hdr h3{font-size:14px;font-weight:700}
.sms-folders{padding:8px}
.sms-folder{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:var(--radius);font-size:13px;font-weight:500;color:var(--text2);cursor:pointer;transition:all .15s;margin-bottom:2px}
.sms-folder:hover{background:var(--surface2)}
.sms-folder.active{background:var(--accent-glow);color:var(--accent);font-weight:600}
.sms-folder svg{width:16px;height:16px;flex-shrink:0}
.sms-main{display:flex;flex-direction:column;overflow:hidden}
.sms-toolbar{display:flex;align-items:center;gap:8px;padding:10px 16px;border-bottom:1px solid var(--border)}
.sms-toolbar input{flex:1;max-width:260px;font-size:12px}
.sms-list{flex:1;overflow-y:auto}
.sms-item{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s}
.sms-item:hover{background:var(--surface2)}
.sms-item.selected{background:var(--accent-glow)}
.sms-avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;flex-shrink:0;background:var(--green)}
.sms-meta{flex:1;min-width:0}
.sms-to{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:var(--text)}
.sms-preview{font-size:12px;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px}
.sms-time{font-size:11px;color:var(--text3);white-space:nowrap;flex-shrink:0}
/* Compose SMS */
.sms-compose-overlay{position:fixed;inset:0;background:rgba(23,43,77,.4);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center}
.sms-compose-overlay.open{display:flex}
.sms-compose-modal{background:var(--surface);border-radius:var(--radius-lg);width:94%;max-width:520px;box-shadow:0 8px 40px rgba(0,0,0,.18)}
.sms-compose-hdr{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
.sms-compose-body{padding:20px;display:flex;flex-direction:column;gap:12px}
.sms-compose-footer{padding:12px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px}
.sms-char-count{font-size:11px;color:var(--text3);text-align:right;margin-top:4px}
/* Reader */
.sms-reader{position:fixed;inset:0;background:rgba(23,43,77,.4);backdrop-filter:blur(4px);z-index:900;display:none;align-items:flex-start;justify-content:center;padding-top:60px}
.sms-reader.open{display:flex}
.sms-reader-inner{background:var(--surface);border-radius:var(--radius-lg);width:90%;max-width:500px;max-height:70vh;overflow-y:auto;box-shadow:0 8px 40px rgba(0,0,0,.18)}
.sms-reader-hdr{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center}
.sms-reader-meta{padding:12px 20px;border-bottom:1px solid var(--border);font-size:12px;color:var(--text2)}
.sms-reader-body{padding:20px;font-size:14px;line-height:1.6;white-space:pre-wrap;word-break:break-word}
</style>
@endverbatim

<div class="animate-in">
  <div class="sms-layout">

    <!-- Sidebar -->
    <div class="sms-sidebar">
      <div class="sms-sidebar-hdr">
        <h3>💬 SMS</h3>
        <button class="btn btn-primary btn-sm" onclick="openSmsCompose()">✏ New</button>
      </div>
      <div class="sms-folders">
        <div class="sms-folder active" id="sfolder-sent" onclick="loadSmsFolder('sent')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/></svg>
          Sent
        </div>
        <div class="sms-folder" id="sfolder-trash" onclick="loadSmsFolder('trash')">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
          Trash
        </div>
      </div>

      <!-- Config status -->
      <div style="padding:12px;margin-top:auto;border-top:1px solid var(--border)">
        <div id="smsConfigStatus" style="font-size:11px;color:var(--text3);line-height:1.5">Checking Twilio…</div>
      </div>
    </div>

    <!-- List -->
    <div class="sms-main">
      <div class="sms-toolbar">
        <input type="text" id="smsSearch" placeholder="Search SMS…" oninput="searchSms(this.value)">
        <button class="btn btn-danger btn-sm" id="sBtnDelete" style="display:none" onclick="bulkDeleteSms()">🗑 Delete</button>
        <span style="margin-left:auto;font-size:12px;color:var(--text3)" id="smsCount"></span>
      </div>
      <div class="sms-list" id="smsList">
        <div style="text-align:center;padding:60px;color:var(--text3)">Loading…</div>
      </div>
    </div>
  </div>
</div>

<!-- SMS reader -->
<div class="sms-reader" id="smsReader" onclick="if(event.target===this)closeSmsReader()">
  <div class="sms-reader-inner">
    <div class="sms-reader-hdr">
      <h4 id="smsReaderTitle">SMS</h4>
      <div style="display:flex;gap:8px">
        <button class="btn btn-primary btn-sm" onclick="replyToSms()">↩ Reply</button>
        <button class="btn btn-danger btn-sm" onclick="deleteSmsMsg()">🗑</button>
        <button onclick="closeSmsReader()" style="font-size:18px;color:var(--text3);padding:4px 8px">✕</button>
      </div>
    </div>
    <div class="sms-reader-meta" id="smsReaderMeta"></div>
    <div class="sms-reader-body" id="smsReaderBody"></div>
  </div>
</div>

<!-- Compose SMS modal -->
<div class="sms-compose-overlay" id="smsComposeOverlay" onclick="if(event.target===this)closeSmsCompose()">
  <div class="sms-compose-modal">
    <div class="sms-compose-hdr">
      <h3>💬 New SMS</h3>
      <button onclick="closeSmsCompose()" style="font-size:18px;color:var(--text3)">✕</button>
    </div>
    <div class="sms-compose-body">
      <div class="form-group" style="margin:0">
        <label>To (phone number)</label>
        <input type="tel" id="smsTo" placeholder="+1 (555) 000-0000">
      </div>
      <div class="form-group" style="margin:0">
        <label>Message</label>
        <textarea id="smsBody" rows="5" placeholder="Type your SMS message…" oninput="updateCharCount()"></textarea>
        <div class="sms-char-count"><span id="smsCharCount">0</span> / 1600 chars</div>
      </div>
    </div>
    <div class="sms-compose-footer">
      <button class="btn btn-secondary" onclick="closeSmsCompose()">Cancel</button>
      <button class="btn btn-primary" onclick="sendSms()">📤 Send SMS</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
var _smsFolder  = 'sent';
var _smsMessages = [];
var _currentSms  = null;
var _smsSelected = new Set();

document.addEventListener('DOMContentLoaded', function(){
    loadSmsFolder('sent');
    checkSmsConfig();
});

async function checkSmsConfig(){
    var r = await apiFetch('/api/settings');
    if(!r) return;
    var s = await r.json();
    var el = document.getElementById('smsConfigStatus');
    if(s.twilio_account_sid && s.twilio_from_number){
        el.innerHTML = '<span style="color:var(--green)">✓ Twilio configured</span><br><span>From: '+esc(s.twilio_from_number)+'</span>';
    } else {
        el.innerHTML = '<span style="color:var(--orange)">⚠ Twilio not configured</span><br><a href="/hris/settings" style="font-size:11px;color:var(--accent)">→ Configure in Settings</a>';
    }
}

function loadSmsFolder(folder){
    _smsFolder = folder;
    _smsSelected.clear();
    document.querySelectorAll('.sms-folder').forEach(function(f){ f.classList.remove('active'); });
    var el = document.getElementById('sfolder-'+folder);
    if(el) el.classList.add('active');
    loading('smsList');
    doLoadSms();
}

async function doLoadSms(search){
    var url = '/api/sms?folder='+_smsFolder+(search?'&search='+encodeURIComponent(search):'');
    var r   = await apiFetch(url);
    if(!r) return;
    var data = await r.json();
    _smsMessages = (data.data) || data || [];
    var mc = document.getElementById('smsCount');
    if(mc) mc.textContent = _smsMessages.length + ' message' + (_smsMessages.length===1?'':'s');
    renderSmsMessages();
}

function renderSmsMessages(){
    var el = document.getElementById('smsList');
    if(!_smsMessages.length){ empty('smsList','No SMS in '+_smsFolder+'.'); return; }
    el.innerHTML = _smsMessages.map(function(m){
        var to   = m.to || '—';
        var prev = (m.body||'').substring(0,80);
        var dt   = m.sent_at || m.created_at;
        var selCls = _smsSelected.has(m.id) ? ' selected' : '';
        var cand = m.candidate ? ' · '+esc(m.candidate.first_name+' '+m.candidate.last_name) : '';
        return '<div class="sms-item'+selCls+'" onclick="openSmsMessage('+m.id+')" data-id="'+m.id+'">'
          +'<div class="sms-avatar">📱</div>'
          +'<div class="sms-meta">'
            +'<div class="sms-to">'+esc(to)+cand+'</div>'
            +'<div class="sms-preview">'+esc(prev)+'</div>'
          +'</div>'
          +'<span class="sms-time">'+fDate(dt)+'</span>'
        +'</div>';
    }).join('');
}

function searchSms(val){
    clearTimeout(window._smsTm);
    window._smsTm = setTimeout(function(){ doLoadSms(val); }, 350);
}

function openSmsMessage(id){
    var msg = _smsMessages.find(function(m){ return m.id===id; });
    if(!msg) return;
    _currentSms = msg;
    document.getElementById('smsReaderTitle').textContent = 'SMS to ' + (msg.to||'—');
    document.getElementById('smsReaderMeta').innerHTML =
        '<b>To:</b> '+esc(msg.to||'—')+' &nbsp; <b>From:</b> '+esc(msg.from||'—')+' &nbsp; <b>Date:</b> '+fD(msg.sent_at||msg.created_at)
        +(msg.candidate?'<br><b>Candidate:</b> '+esc(msg.candidate.first_name+' '+msg.candidate.last_name):'');
    document.getElementById('smsReaderBody').textContent = msg.body || '';
    document.getElementById('smsReader').classList.add('open');
}

function closeSmsReader(){ document.getElementById('smsReader').classList.remove('open'); _currentSms=null; }

function replyToSms(){
    if(!_currentSms) return;
    document.getElementById('smsTo').value   = _currentSms.to||'';
    document.getElementById('smsBody').value = '';
    updateCharCount();
    closeSmsReader();
    document.getElementById('smsComposeOverlay').classList.add('open');
}

async function deleteSmsMsg(){
    if(!_currentSms) return;
    await apiFetch('/api/sms/'+_currentSms.id,{method:'DELETE'});
    closeSmsReader();
    doLoadSms();
    toast('Moved to trash.');
}

async function bulkDeleteSms(){
    if(!confirm('Delete '+_smsSelected.size+' SMS?')) return;
    for(var id of _smsSelected){
        await apiFetch('/api/sms/'+id,{method:'DELETE'});
    }
    _smsSelected.clear();
    doLoadSms();
    toast('Deleted.');
}

/* ── Compose ─────────────────────────────────────────────────── */
function openSmsCompose(to){
    document.getElementById('smsTo').value   = to||'';
    document.getElementById('smsBody').value = '';
    updateCharCount();
    document.getElementById('smsComposeOverlay').classList.add('open');
}

function closeSmsCompose(){ document.getElementById('smsComposeOverlay').classList.remove('open'); }

function updateCharCount(){
    var len = document.getElementById('smsBody').value.length;
    document.getElementById('smsCharCount').textContent = len;
}

async function sendSms(){
    var to   = document.getElementById('smsTo').value.trim();
    var body = document.getElementById('smsBody').value.trim();
    if(!to){   toast('Phone number is required.','error'); return; }
    if(!body){ toast('Message is required.','error'); return; }

    var r = await apiFetch('/api/sms',{method:'POST',body:JSON.stringify({to,body})});
    if(!r) return;
    var data = await r.json();
    if(data.error){ toast(data.error,'error'); return; }
    closeSmsCompose();
    toast('SMS sent!');
    loadSmsFolder('sent');
}
</script>
@endpush
@endsection
