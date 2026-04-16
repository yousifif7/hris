@extends('layouts.app')
@section('title','McCrory Center — Messaging')
@section('content')
@verbatim
<style>
.msg-layout{display:grid;grid-template-columns:220px 1fr;height:calc(100vh - 120px);gap:0;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow)}
.msg-sidebar{border-right:1px solid var(--border);display:flex;flex-direction:column}
.msg-sidebar-hdr{padding:14px 16px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.msg-sidebar-hdr h3{font-size:14px;font-weight:700}
.msg-folders{flex:1;padding:8px}
.msg-folder{display:flex;align-items:center;gap:8px;padding:9px 12px;border-radius:var(--radius);font-size:13px;font-weight:500;color:var(--text2);cursor:pointer;transition:all .15s;margin-bottom:2px}
.msg-folder:hover{background:var(--surface2);color:var(--text)}
.msg-folder.active{background:var(--accent-glow);color:var(--accent);font-weight:600}
.msg-folder .folder-icon{width:16px;height:16px;flex-shrink:0}
.msg-folder .folder-badge{margin-left:auto;background:var(--accent);color:#fff;font-size:10px;font-weight:700;padding:1px 6px;border-radius:8px}
.msg-main{display:flex;flex-direction:column;flex:1;overflow:hidden}
.msg-toolbar{display:flex;align-items:center;gap:8px;padding:10px 16px;border-bottom:1px solid var(--border);flex-shrink:0}
.msg-toolbar input{flex:1;max-width:260px;font-size:12px}
.msg-list{flex:1;overflow-y:auto}
.msg-item{display:flex;align-items:flex-start;gap:12px;padding:12px 16px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .15s}
.msg-item:hover{background:var(--surface2)}
.msg-item.unread{background:rgba(90,198,204,.04)}
.msg-item.selected{background:var(--accent-glow)}
.msg-item .msg-check{width:16px;height:16px;flex-shrink:0;margin-top:2px}
.msg-avatar{width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;flex-shrink:0}
.msg-meta{flex:1;min-width:0}
.msg-from{font-size:13px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.msg-item.unread .msg-from{font-weight:700}
.msg-subj{font-size:12px;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px}
.msg-preview{font-size:11px;color:var(--text3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px}
.msg-time{font-size:11px;color:var(--text3);white-space:nowrap;flex-shrink:0}
.msg-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:6px}
/* Reader pane */
.msg-reader{position:fixed;inset:0;background:rgba(23,43,77,.4);backdrop-filter:blur(4px);z-index:900;display:none;align-items:flex-start;justify-content:center;padding-top:40px}
.msg-reader.open{display:flex}
.msg-reader-inner{background:var(--surface);border-radius:var(--radius-lg);width:92%;max-width:700px;max-height:80vh;overflow-y:auto;box-shadow:0 8px 40px rgba(0,0,0,.18)}
.msg-reader-hdr{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:space-between;gap:12px}
.msg-reader-hdr h3{font-size:16px;font-weight:700;color:var(--text);flex:1}
.msg-reader-meta{padding:14px 24px;border-bottom:1px solid var(--border);display:flex;flex-wrap:wrap;gap:8px;font-size:12px;color:var(--text2)}
.msg-reader-body{padding:24px;font-size:14px;line-height:1.7;color:var(--text);white-space:pre-wrap;word-break:break-word}
/* Compose */
.compose-overlay{position:fixed;inset:0;background:rgba(23,43,77,.4);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center}
.compose-overlay.open{display:flex}
.compose-modal{background:var(--surface);border-radius:var(--radius-lg);width:94%;max-width:740px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 8px 40px rgba(0,0,0,.18)}
.compose-hdr{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.compose-hdr h3{font-size:15px;font-weight:700}
.compose-fields{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;flex-direction:column;gap:8px}
.compose-row{display:flex;align-items:center;gap:10px}
.compose-row label{font-size:12px;font-weight:600;color:var(--text2);width:50px;flex-shrink:0}
.compose-row input,.compose-row select{flex:1;font-size:13px}
.compose-body-wrap{flex:1;display:flex;flex-direction:column;overflow:hidden}
.compose-toolbar{display:flex;gap:6px;padding:8px 16px;border-bottom:1px solid var(--border);flex-wrap:wrap;align-items:center}
.compose-toolbar select{font-size:12px;padding:4px 8px;max-width:200px}
.compose-toolbar .token-btn{font-size:11px;padding:3px 8px;border-radius:5px;background:var(--surface2);border:1px solid var(--border);cursor:pointer;color:var(--text2);transition:all .15s}
.compose-toolbar .token-btn:hover{border-color:var(--accent);color:var(--accent)}
.compose-textarea{flex:1;padding:16px 20px;border:none;resize:none;font-size:13px;line-height:1.7;min-height:200px;outline:none;background:transparent;color:var(--text)}
.compose-footer{padding:12px 20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;gap:8px}
.token-dropdown{position:absolute;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);z-index:1100;min-width:260px;max-height:300px;overflow-y:auto;display:none}
.token-dropdown.open{display:block}
.token-group-hdr{padding:6px 12px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text3);background:var(--surface2);border-bottom:1px solid var(--border)}
.token-opt{padding:7px 12px;font-size:12px;cursor:pointer;color:var(--text)}
.token-opt:hover{background:var(--accent-glow);color:var(--accent)}
.token-opt code{font-size:11px;background:var(--surface2);padding:1px 5px;border-radius:4px;margin-right:6px}
</style>
@endverbatim

<div class="animate-in">
  <div class="msg-layout">

    <!-- Sidebar folders -->
    <div class="msg-sidebar">
      <div class="msg-sidebar-hdr">
        <h3>📧 Email</h3>
        <button class="btn btn-primary btn-sm" onclick="openCompose()">✏ Compose</button>
      </div>
      <div class="msg-folders">
        <div class="msg-folder active" id="folder-inbox" onclick="loadFolder('inbox')">
          <svg class="folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.45 5.11L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.45-6.89A2 2 0 0016.76 4H7.24a2 2 0 00-1.79 1.11z"/></svg>
          Inbox <span class="folder-badge" id="badge-inbox" style="display:none">0</span>
        </div>
        <div class="msg-folder" id="folder-sent" onclick="loadFolder('sent')">
          <svg class="folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22,2 15,22 11,13 2,9"/></svg>
          Sent
        </div>
        <div class="msg-folder" id="folder-draft" onclick="loadFolder('draft')">
          <svg class="folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          Drafts <span id="badge-draft" style="font-size:11px;color:var(--text3);margin-left:auto"></span>
        </div>
        <div class="msg-folder" id="folder-trash" onclick="loadFolder('trash')">
          <svg class="folder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3,6 5,6 21,6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
          Trash
        </div>
      </div>
    </div>

    <!-- Message list -->
    <div class="msg-main">
      <div class="msg-toolbar">
        <input type="text" id="msgSearch" placeholder="Search messages…" oninput="searchMessages(this.value)">
        <button class="btn btn-secondary btn-sm" onclick="selectAll()" id="btnSelectAll">Select All</button>
        <button class="btn btn-danger btn-sm" id="btnBulkDelete" style="display:none" onclick="bulkDelete()">🗑 Delete</button>
        <button class="btn btn-secondary btn-sm" id="btnMarkRead" style="display:none" onclick="bulkMarkRead()">✓ Mark Read</button>
        <span style="margin-left:auto;font-size:12px;color:var(--text3)" id="msgCount"></span>
      </div>
      <div class="msg-list" id="msgList">
        <div style="text-align:center;padding:60px;color:var(--text3)">Loading…</div>
      </div>
    </div>
  </div>
</div>

<!-- Message reader -->
<div class="msg-reader" id="msgReader" onclick="if(event.target===this)closeReader()">
  <div class="msg-reader-inner">
    <div class="msg-reader-hdr">
      <h3 id="readerSubject">Subject</h3>
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary btn-sm" onclick="replyMessage()">↩ Reply</button>
        <button class="btn btn-danger btn-sm" onclick="deleteCurrentMessage()">🗑</button>
        <button onclick="closeReader()" style="font-size:18px;color:var(--text3);padding:4px 8px">✕</button>
      </div>
    </div>
    <div class="msg-reader-meta" id="readerMeta"></div>
    <div class="msg-reader-body" id="readerBody"></div>
  </div>
</div>

<!-- Compose modal -->
<div class="compose-overlay" id="composeOverlay" onclick="if(event.target===this)closeCompose()">
  <div class="compose-modal">
    <div class="compose-hdr">
      <h3 id="composeTitleLabel">✏ New Email</h3>
      <button onclick="closeCompose()" style="font-size:18px;color:var(--text3);padding:4px 8px">✕</button>
    </div>
    <div class="compose-fields">
      <div class="compose-row">
        <label>To</label>
        <input type="email" id="cTo" placeholder="recipient@example.com">
      </div>
      <div class="compose-row">
        <label>CC</label>
        <input type="email" id="cCc" placeholder="cc@example.com (optional)">
      </div>
      <div class="compose-row">
        <label>Subject</label>
        <input type="text" id="cSubject" placeholder="Subject…">
      </div>
      <div class="compose-row">
        <label>Template</label>
        <select id="cTemplate" onchange="applyTemplate(this.value)" style="flex:1;font-size:12px">
          <option value="">— Choose a template (optional) —</option>
        </select>
      </div>
    </div>
    <div class="compose-body-wrap">
      <div class="compose-toolbar">
        <span style="font-size:12px;font-weight:600;color:var(--text2)">Insert token:</span>
        <div style="position:relative">
          <button class="token-btn" onclick="toggleTokenDropdown(event)">⬡ Tokens ▾</button>
          <div class="token-dropdown" id="tokenDropdown"></div>
        </div>
      </div>
      <textarea class="compose-textarea" id="cBody" placeholder="Write your message here…"></textarea>
    </div>
    <div class="compose-footer">
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary btn-sm" onclick="saveDraft()">💾 Save Draft</button>
      </div>
      <div style="display:flex;gap:8px">
        <button class="btn btn-secondary" onclick="closeCompose()">Cancel</button>
        <button class="btn btn-primary" onclick="sendMessage()">📤 Send</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
var _currentFolder = 'inbox';
var _messages      = [];
var _currentMsg    = null;
var _editDraftId   = null;
var _templates     = [];
var _tokens        = [];
var _selectedIds   = new Set();

document.addEventListener('DOMContentLoaded', function(){
    loadFolder('inbox');
    loadTemplates();
    loadTokens();
});

/* ── Folder navigation ─────────────────────────────────────── */
function loadFolder(folder){
    _currentFolder  = folder;
    _selectedIds.clear();
    document.querySelectorAll('.msg-folder').forEach(function(f){ f.classList.remove('active'); });
    var el = document.getElementById('folder-'+folder);
    if(el) el.classList.add('active');
    loading('msgList');
    doLoadMessages();
}

async function doLoadMessages(search){
    var url = '/api/messages?folder='+_currentFolder+(search?'&search='+encodeURIComponent(search):'');
    var r   = await apiFetch(url);
    if(!r) return;
    var data = await r.json();
    _messages = (data.messages && data.messages.data) || data.messages || [];

    // Unread badge
    var ub = document.getElementById('badge-inbox');
    if(ub){
        var uc = data.unread_count || 0;
        ub.textContent = uc;
        ub.style.display = uc > 0 ? 'inline' : 'none';
    }
    var mc = document.getElementById('msgCount');
    if(mc) mc.textContent = _messages.length + ' message' + (_messages.length===1?'':'s');

    renderMessages();
    updateBulkBar();
}

function renderMessages(){
    var el = document.getElementById('msgList');
    if(!_messages.length){
        empty('msgList','No messages in '+_currentFolder+'.');
        return;
    }
    var colors=['#5b4cdb','#e5508c','#00875a','#ff991f','#0065ff','#ff8b00'];
    el.innerHTML = _messages.map(function(m, i){
        var from  = m.folder==='sent' ? (m.to||'—') : (m.from||m.to||'—');
        var subj  = m.subject || '(No Subject)';
        var prev  = (m.body||'').replace(/\n/g,' ').substring(0,80);
        var dt    = m.sent_at || m.created_at;
        var initials = from.substring(0,2).toUpperCase();
        var bg    = colors[i%colors.length];
        var unreadCls = (!m.is_read && m.folder==='inbox') ? ' unread' : '';
        var selCls = _selectedIds.has(m.id) ? ' selected' : '';
        return '<div class="msg-item'+unreadCls+selCls+'" data-id="'+m.id+'">'
          +'<input type="checkbox" class="msg-check" '+ (_selectedIds.has(m.id)?'checked':'')+' onclick="toggleSelect(event,'+m.id+')">'
          +'<div class="msg-avatar" style="background:'+bg+'">'+esc(initials)+'</div>'
          +'<div class="msg-meta" onclick="openMessage('+m.id+')">'
            +'<div class="msg-from">'+esc(from)+'</div>'
            +'<div class="msg-subj">'+esc(subj)+'</div>'
            +'<div class="msg-preview">'+esc(prev)+'</div>'
          +'</div>'
          +'<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">'
            +'<span class="msg-time">'+fDate(dt)+'</span>'
            +(_currentFolder==='draft'?'<button class="btn btn-secondary btn-sm" style="font-size:10px" onclick="event.stopPropagation();editDraft('+m.id+')">Edit</button>':'')
          +'</div>'
        +'</div>';
    }).join('');
}

function searchMessages(val){
    clearTimeout(window._searchTm);
    window._searchTm = setTimeout(function(){ doLoadMessages(val); }, 350);
}

/* ── Selection ─────────────────────────────────────────────── */
function toggleSelect(e, id){
    e.stopPropagation();
    if(_selectedIds.has(id)) _selectedIds.delete(id);
    else _selectedIds.add(id);
    renderMessages();
    updateBulkBar();
}

function selectAll(){
    if(_selectedIds.size === _messages.length){
        _selectedIds.clear();
    } else {
        _messages.forEach(function(m){ _selectedIds.add(m.id); });
    }
    renderMessages();
    updateBulkBar();
}

function updateBulkBar(){
    var del  = document.getElementById('btnBulkDelete');
    var read = document.getElementById('btnMarkRead');
    var show = _selectedIds.size > 0;
    if(del) del.style.display = show?'inline-flex':'none';
    if(read) read.style.display = show?'inline-flex':'none';
}

async function bulkDelete(){
    if(!confirm('Delete '+_selectedIds.size+' message(s)?')) return;
    var ids = Array.from(_selectedIds);
    for(var i=0;i<ids.length;i++){
        await apiFetch('/api/messages/'+ids[i],{method:'DELETE'});
    }
    _selectedIds.clear();
    doLoadMessages();
    toast('Deleted.');
}

async function bulkMarkRead(){
    var ids = Array.from(_selectedIds);
    for(var i=0;i<ids.length;i++){
        await apiFetch('/api/messages/'+ids[i]+'/read',{method:'PATCH',body:JSON.stringify({read:true})});
    }
    _selectedIds.clear();
    doLoadMessages();
    toast('Marked as read.');
}

/* ── Message reader ─────────────────────────────────────────── */
async function openMessage(id){
    var msg = _messages.find(function(m){ return m.id===id; });
    if(!msg) return;
    _currentMsg = msg;

    var r = await apiFetch('/api/messages/'+id);
    if(!r) return;
    var data = await r.json();

    document.getElementById('readerSubject').textContent = data.subject || '(No Subject)';
    document.getElementById('readerMeta').innerHTML =
        '<span><b>From:</b> '+esc(data.from||'—')+'</span>'
        +'<span><b>To:</b> '+esc(data.to||'—')+'</span>'
        +(data.cc?'<span><b>CC:</b> '+esc(data.cc)+'</span>':'')
        +'<span><b>Date:</b> '+fD(data.sent_at||data.created_at)+'</span>'
        +(data.candidate?'<span><b>Candidate:</b> '+esc(data.candidate.first_name+' '+data.candidate.last_name)+'</span>':'');

    var bodyEl = document.getElementById('readerBody');
    if(data.is_html){
        bodyEl.innerHTML = data.body;
    } else {
        bodyEl.innerHTML = '';
        bodyEl.textContent = data.body;
    }

    document.getElementById('msgReader').classList.add('open');

    // Refresh list to update unread indicator
    doLoadMessages();
}

function closeReader(){ document.getElementById('msgReader').classList.remove('open'); _currentMsg=null; }

function replyMessage(){
    if(!_currentMsg) return;
    openCompose({
        to: _currentMsg.from || _currentMsg.to,
        subject: 'Re: '+(_currentMsg.subject||''),
        body: '\n\n---\nOn '+fDate(_currentMsg.sent_at||_currentMsg.created_at)+', wrote:\n'+(_currentMsg.body||'')
    });
    closeReader();
}

async function deleteCurrentMessage(){
    if(!_currentMsg) return;
    await apiFetch('/api/messages/'+_currentMsg.id,{method:'DELETE'});
    closeReader();
    doLoadMessages();
    toast('Moved to trash.');
}

/* ── Compose ───────────────────────────────────────────────── */
function openCompose(prefill){
    _editDraftId = null;
    document.getElementById('cTo').value      = (prefill&&prefill.to)||'';
    document.getElementById('cCc').value      = (prefill&&prefill.cc)||'';
    document.getElementById('cSubject').value = (prefill&&prefill.subject)||'';
    document.getElementById('cBody').value    = (prefill&&prefill.body)||'';
    document.getElementById('cTemplate').value= '';
    document.getElementById('composeTitleLabel').textContent = '✏ New Email';
    document.getElementById('composeOverlay').classList.add('open');
}

function editDraft(id){
    var msg = _messages.find(function(m){ return m.id===id; });
    if(!msg) return;
    _editDraftId = id;
    document.getElementById('cTo').value      = msg.to||'';
    document.getElementById('cCc').value      = msg.cc||'';
    document.getElementById('cSubject').value = msg.subject||'';
    document.getElementById('cBody').value    = msg.body||'';
    document.getElementById('cTemplate').value= '';
    document.getElementById('composeTitleLabel').textContent = '📝 Edit Draft';
    document.getElementById('composeOverlay').classList.add('open');
}

function closeCompose(){ document.getElementById('composeOverlay').classList.remove('open'); }

async function sendMessage(){
    var to      = document.getElementById('cTo').value.trim();
    var subject = document.getElementById('cSubject').value.trim();
    var body    = document.getElementById('cBody').value.trim();
    if(!to){ toast('Recipient is required.','error'); return; }
    if(!subject){ toast('Subject is required.','error'); return; }
    if(!body){ toast('Body is required.','error'); return; }

    var payload = { to:to, cc:document.getElementById('cCc').value, subject:subject, body:body, send_now:true };

    var url    = _editDraftId ? '/api/messages/'+_editDraftId+'/send' : '/api/messages';
    var method = 'POST';
    if(_editDraftId){
        // update draft body first
        await apiFetch('/api/messages/'+_editDraftId,{method:'PATCH',body:JSON.stringify({to,subject,body})});
        var r = await apiFetch('/api/messages/'+_editDraftId+'/send',{method:'POST'});
    } else {
        var r = await apiFetch(url,{method,body:JSON.stringify(payload)});
    }
    if(!r) return;
    closeCompose();
    toast('Email sent!');
    loadFolder('sent');
}

async function saveDraft(){
    var body    = document.getElementById('cBody').value.trim();
    if(!body){ toast('Cannot save empty draft.','error'); return; }
    var payload = {
        to:      document.getElementById('cTo').value,
        cc:      document.getElementById('cCc').value,
        subject: document.getElementById('cSubject').value,
        body:    body,
        send_now:false
    };
    var r;
    if(_editDraftId){
        r = await apiFetch('/api/messages/'+_editDraftId,{method:'PATCH',body:JSON.stringify(payload)});
    } else {
        r = await apiFetch('/api/messages',{method:'POST',body:JSON.stringify(payload)});
    }
    if(!r) return;
    closeCompose();
    toast('Draft saved.');
    loadFolder('draft');
}

/* ── Templates ─────────────────────────────────────────────── */
async function loadTemplates(){
    var r = await apiFetch('/api/settings/email-templates');
    if(!r) return;
    _templates = await r.json();
    var sel = document.getElementById('cTemplate');
    sel.innerHTML = '<option value="">— Choose a template (optional) —</option>'
        + _templates.map(function(t){
            return '<option value="'+t.id+'">'+esc(t.name)+'</option>';
        }).join('');
}

async function applyTemplate(id){
    if(!id) return;
    var tpl = _templates.find(function(t){ return t.id==id; });
    if(!tpl) return;
    document.getElementById('cSubject').value = tpl.subject||'';
    document.getElementById('cBody').value    = tpl.body||'';
}

/* ── Token picker ──────────────────────────────────────────── */
async function loadTokens(){
    var r = await apiFetch('/api/settings/email-tokens');
    if(!r) return;
    _tokens = await r.json();
    renderTokenDropdown();
}

function renderTokenDropdown(){
    var groups = {};
    _tokens.forEach(function(t){
        if(!groups[t.group]) groups[t.group]=[];
        groups[t.group].push(t);
    });
    var html='';
    Object.keys(groups).forEach(function(g){
        html += '<div class="token-group-hdr">'+esc(g)+'</div>';
        groups[g].forEach(function(t){
            html += '<div class="token-opt" onclick="insertToken(\''+esc(t.token)+'\')">'
                  + '<code>'+esc(t.token)+'</code> '+esc(t.label)+'</div>';
        });
    });
    document.getElementById('tokenDropdown').innerHTML = html;
}

function toggleTokenDropdown(e){
    e.stopPropagation();
    document.getElementById('tokenDropdown').classList.toggle('open');
}

document.addEventListener('click', function(e){
    var dd = document.getElementById('tokenDropdown');
    if(dd && !dd.contains(e.target)) dd.classList.remove('open');
});

function insertToken(token){
    var ta = document.getElementById('cBody');
    var start = ta.selectionStart;
    var end   = ta.selectionEnd;
    ta.value  = ta.value.substring(0,start) + token + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = start + token.length;
    ta.focus();
    document.getElementById('tokenDropdown').classList.remove('open');
}
</script>
@endpush
@endsection
