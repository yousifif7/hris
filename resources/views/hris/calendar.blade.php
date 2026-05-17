@extends('layouts.app')
@section('title','McCrory Center — Interview Calendar')
@section('content')

@push('styles')
<style>
/* ── Full-page calendar ───────────────────────────────── */
.fc-wrap{display:flex;flex-direction:column;height:calc(100vh - 140px);min-height:500px}
.fc-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.fc-toolbar h2{font-size:18px;font-weight:700;color:var(--text);flex:1}
.fc-toolbar-btns{display:flex;gap:6px}
.fc-view-btn{padding:5px 14px;font-size:12px;font-weight:600;border-radius:var(--radius);border:1px solid var(--border);background:var(--surface);color:var(--text2);cursor:pointer;transition:all .15s}
.fc-view-btn.active,
.fc-view-btn:hover{background:var(--accent);color:#fff;border-color:var(--accent)}

/* Month grid */
.fc-month{flex:1;display:flex;flex-direction:column;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.fc-dow-row{display:grid;grid-template-columns:repeat(7,1fr);background:var(--surface2);border-bottom:1px solid var(--border)}
.fc-dow{text-align:center;padding:8px 4px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--text3)}
.fc-body{flex:1;display:grid;grid-template-rows:repeat(6,1fr);overflow:hidden}
.fc-week{display:grid;grid-template-columns:repeat(7,1fr);border-bottom:1px solid var(--border)}
.fc-week:last-child{border-bottom:none}
.fc-cell{border-right:1px solid var(--border);padding:4px;overflow:hidden;cursor:pointer;transition:background .15s;position:relative;min-height:0}
.fc-cell:last-child{border-right:none}
.fc-cell:hover{background:var(--surface2)}
.fc-cell.today{background:var(--accent-glow)}
.fc-cell.other-month .fc-day-num{color:var(--text3);opacity:.4}
.fc-cell.sel{background:rgba(90,198,204,.1)}
.fc-day-num{font-size:11px;font-weight:600;color:var(--text2);margin-bottom:2px;display:flex;align-items:center;justify-content:space-between}
.fc-day-num .today-dot{width:20px;height:20px;border-radius:50%;background:var(--accent);color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700}
.fc-evt{font-size:10px;font-weight:600;padding:2px 5px;border-radius:4px;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;cursor:pointer;background:var(--accent-glow);color:var(--accent);border-left:2px solid var(--accent);transition:opacity .15s}
.fc-evt:hover{opacity:.75}
.fc-evt.status-completed{background:var(--green-bg);color:var(--green);border-left-color:var(--green)}
.fc-evt.status-cancelled{background:var(--red-bg);color:var(--red);border-left-color:var(--red)}
.fc-evt.status-no_show{background:var(--yellow-bg);color:var(--yellow);border-left-color:var(--yellow)}
.fc-more{font-size:10px;color:var(--text3);font-weight:600;cursor:pointer;padding:1px 4px}
.fc-more:hover{color:var(--accent)}

/* Week view */
.fc-week-view{flex:1;display:flex;flex-direction:column;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.fc-week-hdr{display:grid;grid-template-columns:50px repeat(7,1fr);border-bottom:1px solid var(--border);background:var(--surface2)}
.fc-wh-gutter{border-right:1px solid var(--border)}
.fc-wh-day{text-align:center;padding:8px 4px;font-size:11px;font-weight:700;color:var(--text2);border-right:1px solid var(--border)}
.fc-wh-day.today{color:var(--accent)}
.fc-wh-day .wh-dn{font-size:9px;text-transform:uppercase;letter-spacing:.5px;color:var(--text3)}
.fc-wh-day .wh-dd{font-size:16px;font-weight:700}
.fc-week-body{flex:1;overflow-y:auto;display:grid;grid-template-columns:50px repeat(7,1fr);position:relative}
.fc-time-col{border-right:1px solid var(--border)}
.fc-time-slot{height:48px;border-bottom:1px solid var(--border);display:flex;align-items:flex-start;justify-content:flex-end;padding:2px 6px 0}
.fc-time-lbl{font-size:9px;color:var(--text3);font-weight:600;white-space:nowrap}
.fc-day-col{border-right:1px solid var(--border);position:relative}
.fc-day-col:last-child{border-right:none}
.fc-hour-line{height:48px;border-bottom:1px solid var(--border)}
.fc-week-evt{position:absolute;left:2px;right:2px;border-radius:4px;padding:3px 5px;font-size:10px;font-weight:600;cursor:pointer;overflow:hidden;background:var(--accent-glow);color:var(--accent);border-left:2px solid var(--accent);z-index:1;transition:opacity .15s}
.fc-week-evt:hover{opacity:.8}
.fc-week-evt.status-completed{background:var(--green-bg);color:var(--green);border-left-color:var(--green)}
.fc-week-evt.status-cancelled{background:var(--red-bg);color:var(--red);border-left-color:var(--red)}
.fc-week-evt.status-no_show{background:var(--yellow-bg);color:var(--yellow);border-left-color:var(--yellow)}

/* Day view */
.fc-day-view{flex:1;display:flex;flex-direction:column;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden}
.fc-day-hdr{padding:12px 16px;border-bottom:1px solid var(--border);background:var(--surface2);display:flex;align-items:center;gap:12px}
.fc-day-hdr h3{font-size:15px;font-weight:700;color:var(--text)}
.fc-day-body{flex:1;overflow-y:auto;display:grid;grid-template-columns:50px 1fr}
.fc-day-col-main{position:relative}

/* Event popover */
.fc-popover{position:fixed;z-index:3000;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);box-shadow:0 8px 32px rgba(0,0,0,.14);width:280px;padding:16px;display:none}
.fc-popover.open{display:block}
.fc-popover h4{font-size:14px;font-weight:700;margin-bottom:10px;color:var(--text)}
.fc-popover-row{display:flex;gap:8px;align-items:flex-start;margin-bottom:6px;font-size:12px}
.fc-popover-lbl{color:var(--text3);min-width:60px;font-weight:600}
.fc-popover-val{color:var(--text)}
.fc-popover-close{position:absolute;top:10px;right:12px;font-size:16px;color:var(--text3);cursor:pointer;border:none;background:none}
.fc-popover-close:hover{color:var(--text)}

/* Legend */
.fc-legend{display:flex;gap:14px;align-items:center;margin-bottom:12px;flex-wrap:wrap}
.fc-leg-item{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--text2)}
.fc-leg-dot{width:10px;height:10px;border-radius:3px}
</style>
@endpush

<div class="fc-wrap animate-in">

  <!-- Toolbar -->
  <div class="fc-toolbar">
    <h2 id="fcTitle">Interview Calendar</h2>
    <div class="fc-legend">
      <span class="fc-leg-item"><span class="fc-leg-dot" style="background:var(--accent)"></span>Scheduled</span>
      <span class="fc-leg-item"><span class="fc-leg-dot" style="background:var(--green)"></span>Completed</span>
      <span class="fc-leg-item"><span class="fc-leg-dot" style="background:var(--red)"></span>Cancelled</span>
      <span class="fc-leg-item"><span class="fc-leg-dot" style="background:var(--yellow)"></span>No Show</span>
    </div>
    <span style="flex:1"></span>
    <div class="fc-toolbar-btns">
      <button class="fc-view-btn active" id="btnMonth" onclick="setView('month')">Month</button>
      <button class="fc-view-btn" id="btnWeek" onclick="setView('week')">Week</button>
      <button class="fc-view-btn" id="btnDay" onclick="setView('day')">Day</button>
    </div>
    <div class="fc-toolbar-btns">
      <button class="btn btn-secondary btn-sm" onclick="navPrev()">&#8249;</button>
      <button class="btn btn-secondary btn-sm" onclick="navToday()">Today</button>
      <button class="btn btn-secondary btn-sm" onclick="navNext()">&#8250;</button>
    </div>
    <button class="btn btn-primary btn-sm" onclick="openScheduleInterviewPick()">+ Schedule</button>
  </div>

  <div id="fcCanvas" style="flex:1;display:flex;flex-direction:column;min-height:0"></div>
</div>

<!-- Event popover -->
<div class="fc-popover" id="fcPopover">
  <button class="fc-popover-close" onclick="closePopover()">&#x2715;</button>
  <h4 id="popName"></h4>
  <div class="fc-popover-row"><span class="fc-popover-lbl">Time</span><span class="fc-popover-val" id="popTime"></span></div>
  <div class="fc-popover-row"><span class="fc-popover-lbl">Type</span><span class="fc-popover-val" id="popType"></span></div>
  <div class="fc-popover-row"><span class="fc-popover-lbl">Duration</span><span class="fc-popover-val" id="popDur"></span></div>
  <div class="fc-popover-row"><span class="fc-popover-lbl">Status</span><span class="fc-popover-val" id="popStatus"></span></div>
  <div class="fc-popover-row" id="popLinkRow" style="display:none"><span class="fc-popover-lbl">Link</span><a id="popLink" class="fc-popover-val" target="_blank" rel="noopener" style="color:var(--accent)">Join</a></div>
  <div class="fc-popover-row" id="popNoteRow" style="display:none"><span class="fc-popover-lbl">Notes</span><span class="fc-popover-val" id="popNote" style="white-space:pre-wrap"></span></div>
  <div style="margin-top:12px;display:flex;gap:8px">
    <button class="btn btn-primary btn-sm" id="popViewBtn" onclick="">View Profile</button>
  </div>
</div>
@endsection

@push('scripts')
<script>
async function pageRefresh(){ await loadFcInterviews(); }

var _fcAll    = [];
var _fcView   = 'month';
var _fcDate   = new Date();
var _fcTz     = @json(config('app.timezone', 'UTC'));

/* ── Load data ──────────────────────────────────────────── */
async function loadFcInterviews(){
    var r = await apiFetch('/api/interviews?per_page=500');
    if(!r) return;
    var data = await r.json();
    _fcAll = (data.data || []).map(function(i){
        var raw = String(i.scheduled_at || '').trim().replace(' ', 'T');
        if(raw && !/(Z|[+-]\d{2}:?\d{2})$/.test(raw)) raw += 'Z';
        i._dt = new Date(raw);
        return i;
    });
    renderFc();
}

/* ── View switching ─────────────────────────────────────── */
function setView(v){
    _fcView = v;
    ['month','week','day'].forEach(function(n){
        var b = document.getElementById('btn'+n.charAt(0).toUpperCase()+n.slice(1));
        if(b) b.classList.toggle('active', n===v);
    });
    renderFc();
}

function navPrev()  { navShift(-1); }
function navNext()  { navShift(+1); }
function navToday() { _fcDate = new Date(); renderFc(); }

function navShift(dir){
    var d = new Date(_fcDate);
    if(_fcView==='month') d.setMonth(d.getMonth()+dir);
    else if(_fcView==='week') d.setDate(d.getDate()+dir*7);
    else d.setDate(d.getDate()+dir);
    _fcDate = d;
    renderFc();
}

function renderFc(){
    closePopover();
    if(_fcView==='month') renderMonth();
    else if(_fcView==='week') renderWeek();
    else renderDay();
}

/* ── Helpers ────────────────────────────────────────────── */
function ymd(d){ return d.getFullYear()+'-'+pad(d.getMonth()+1)+'-'+pad(d.getDate()); }
function pad(n){ return String(n).padStart(2,'0'); }
function tzParts(d){
    var parts = new Intl.DateTimeFormat('en-US', {
        timeZone: _fcTz,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false,
        weekday: 'short'
    }).formatToParts(d);
    var out = {};
    parts.forEach(function(p){ out[p.type] = p.value; });
    return out;
}
function ymdTz(d){ var p = tzParts(d); return p.year+'-'+p.month+'-'+p.day; }
function hourFracTz(d){ var p = tzParts(d); return parseInt(p.hour,10) + (parseInt(p.minute,10) / 60); }
function fmt12(d){
    return new Intl.DateTimeFormat('en-US', {
        timeZone: _fcTz,
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    }).format(d);
}
function fmtDateTz(d, opts){
    var base = { timeZone: _fcTz };
    for (var k in opts) base[k] = opts[k];
    return new Intl.DateTimeFormat('en-US', base).format(d);
}
function evtStatusClass(i){ return 'status-'+(i.status||'scheduled'); }

function evtsForDay(dateStr){
    return _fcAll.filter(function(i){ return ymdTz(i._dt)===dateStr; })
                 .sort(function(a,b){ return a._dt-b._dt; });
}

/* ── Month view ─────────────────────────────────────────── */
function renderMonth(){
    var y = _fcDate.getFullYear(), m = _fcDate.getMonth();
    var today = new Date();
    document.getElementById('fcTitle').textContent =
        fmtDateTz(new Date(y, m, 1, 12, 0, 0), {month:'long',year:'numeric'}) + ' (' + _fcTz + ')';

    var firstDow  = new Date(y,m,1).getDay();
    var daysInMo  = new Date(y,m+1,0).getDate();
    var prevDays  = new Date(y,m,0).getDate();

    // Build 6-week grid
    var cells = [];
    for(var p=firstDow-1;p>=0;p--)
        cells.push({date:new Date(y,m-1,prevDays-p),cur:false});
    for(var d=1;d<=daysInMo;d++)
        cells.push({date:new Date(y,m,d),cur:true});
    var trailing = 42-cells.length;
    for(var n=1;n<=trailing;n++)
        cells.push({date:new Date(y,m+1,n),cur:false});

    var weeks = [];
    for(var w=0;w<6;w++) weeks.push(cells.slice(w*7,w*7+7));

    var DAYS_SHORT = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var dowHtml = DAYS_SHORT.map(function(d){ return '<div class="fc-dow">'+d+'</div>'; }).join('');

    var bodyHtml = weeks.map(function(week){
        return '<div class="fc-week">'+week.map(function(cell){
            var ds     = ymd(cell.date);
            var isToday = ymdTz(cell.date)===ymdTz(today);
            var evts   = evtsForDay(ds);
            var cls    = 'fc-cell'+(cell.cur?'':' other-month')+(isToday?' today':'');
            var numHtml = '<div class="fc-day-num">'
                +(isToday
                    ? '<span class="today-dot">'+cell.date.getDate()+'</span>'
                    : '<span>'+cell.date.getDate()+'</span>')
                +(evts.length ? '<span style="font-size:9px;color:var(--text3)">'+evts.length+'</span>' : '')
                +'</div>';
            var MAX = 3;
            var evtHtml = evts.slice(0,MAX).map(function(i){
                var c = i.candidate||{};
                var nm = esc((c.first_name||'')+(c.last_name?' '+c.last_name:''));
                return '<div class="fc-evt '+evtStatusClass(i)+'" onclick="openEvtPopover(event,'+i.id+')" title="'+nm+' — '+fmt12(i._dt)+'">'+fmt12(i._dt)+' '+nm+'</div>';
            }).join('');
            if(evts.length>MAX){
                evtHtml+='<div class="fc-more" onclick="jumpDay(\''+ds+'\')">+' +(evts.length-MAX)+' more</div>';
            }
            return '<div class="'+cls+'" onclick="handleCellClick(event,\''+ds+'\')">' +numHtml+evtHtml+'</div>';
        }).join('')+'</div>';
    }).join('');

    document.getElementById('fcCanvas').innerHTML =
        '<div class="fc-month">'
        +'<div class="fc-dow-row">'+dowHtml+'</div>'
        +'<div class="fc-body">'+bodyHtml+'</div>'
        +'</div>';
}

function handleCellClick(e, ds){
    if(e.target.classList.contains('fc-evt')  ||
       e.target.classList.contains('fc-more') ||
       e.target.closest('.fc-evt')) return;
    jumpDay(ds);
}

function jumpDay(ds){
    var parts = ds.split('-');
    _fcDate = new Date(+parts[0], +parts[1]-1, +parts[2]);
    setView('day');
}

/* ── Week view ──────────────────────────────────────────── */
function renderWeek(){
    var today = new Date();
    var dow   = _fcDate.getDay();
    var weekStart = new Date(_fcDate);
    weekStart.setDate(_fcDate.getDate()-dow);

    var days = [];
    for(var i=0;i<7;i++){
        var d=new Date(weekStart);
        d.setDate(weekStart.getDate()+i);
        days.push(d);
    }

    var range = fmtDateTz(days[0], {month:'short',day:'numeric'})+
                ' - '+fmtDateTz(days[6], {month:'short',day:'numeric',year:'numeric'});
    document.getElementById('fcTitle').textContent = range + ' (' + _fcTz + ')';

    var DN = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var hdrHtml = '<div class="fc-wh-gutter"></div>'
        +days.map(function(d,i){
            var isToday = ymdTz(d)===ymdTz(today);
            return '<div class="fc-wh-day'+(isToday?' today':'')+'"><div class="wh-dn">'+DN[i]+'</div><div class="wh-dd">'+fmtDateTz(d,{day:'numeric'})+'</div></div>';
        }).join('');

    // Hours rows (0-23)
    var timeColHtml = '';
    for(var h=0;h<24;h++){
        var lbl = h===0?'12 AM':h<12?h+' AM':h===12?'12 PM':(h-12)+' PM';
        timeColHtml += '<div class="fc-time-slot"><span class="fc-time-lbl">'+lbl+'</span></div>';
    }

    var dayCols = days.map(function(d){
        var ds    = ymd(d);
        var evts  = evtsForDay(ds);
        var rows  = '';
        for(var h2=0;h2<24;h2++) rows+='<div class="fc-hour-line"></div>';
        var evtBlocks = evts.map(function(i){
            var hr  = hourFracTz(i._dt);
            var dur = (i.duration_minutes||30)/60;
            var top = hr*48;
            var ht  = Math.max(dur*48,20);
            var c   = i.candidate||{};
            var nm  = esc((c.first_name||'')+(c.last_name?' '+c.last_name:''));
            return '<div class="fc-week-evt '+evtStatusClass(i)+'" style="top:'+top+'px;height:'+ht+'px" onclick="openEvtPopover(event,'+i.id+')" title="'+nm+'">'+fmt12(i._dt)+' '+nm+'</div>';
        }).join('');
        return '<div class="fc-day-col" onclick="handleWeekColClick(event,\''+ds+'\')">'+rows+evtBlocks+'</div>';
    }).join('');

    document.getElementById('fcCanvas').innerHTML =
        '<div class="fc-week-view">'
        +'<div class="fc-week-hdr">'+hdrHtml+'</div>'
        +'<div class="fc-week-body">'
            +'<div class="fc-time-col">'+timeColHtml+'</div>'
            +dayCols
        +'</div></div>';

    // Scroll to 8 AM
    var body = document.querySelector('.fc-week-body');
    if(body) body.scrollTop = 8*48;
}

function handleWeekColClick(e, ds){
    if(e.target.classList.contains('fc-week-evt') || e.target.closest('.fc-week-evt')) return;
    jumpDay(ds);
}

/* ── Day view ───────────────────────────────────────────── */
function renderDay(){
    var today = new Date();
    var ds    = ymd(_fcDate);
    var evts  = evtsForDay(ds);

    document.getElementById('fcTitle').textContent =
        fmtDateTz(_fcDate,{weekday:'long',month:'long',day:'numeric',year:'numeric'}) + ' (' + _fcTz + ')';

    var timeColHtml = '';
    for(var h=0;h<24;h++){
        var lbl = h===0?'12 AM':h<12?h+' AM':h===12?'12 PM':(h-12)+' PM';
        timeColHtml += '<div class="fc-time-slot"><span class="fc-time-lbl">'+lbl+'</span></div>';
    }

    var rows = '';
    for(var h2=0;h2<24;h2++) rows+='<div class="fc-hour-line"></div>';

    var evtBlocks = evts.map(function(i){
        var hr  = hourFracTz(i._dt);
        var dur = (i.duration_minutes||30)/60;
        var top = hr*56;
        var ht  = Math.max(dur*56, 28);
        var c   = i.candidate||{};
        var nm  = esc((c.first_name||'')+(c.last_name?' '+c.last_name:''));
        return '<div class="fc-week-evt '+evtStatusClass(i)+'" style="top:'+top+'px;height:'+ht+'px;left:4px;right:4px;font-size:12px" onclick="openEvtPopover(event,'+i.id+')" title="'+nm+'">'+fmt12(i._dt)+' — '+nm+'<div style="font-size:10px;opacity:.7;text-transform:capitalize">'+(i.type||'zoom')+(i.duration_minutes?' · '+i.duration_minutes+' min':'')+'</div></div>';
    }).join('');

    var isToday = ds===ymdTz(today);
    var hdrDate = fmtDateTz(_fcDate,{weekday:'long',month:'long',day:'numeric',year:'numeric'});

    document.getElementById('fcCanvas').innerHTML =
        '<div class="fc-day-view">'
        +'<div class="fc-day-hdr">'
            +'<h3>'+(isToday?'<span style="color:var(--accent)">Today</span> — ':'')+hdrDate+'</h3>'
            +(evts.length ? '<span class="badge badge-invite-sent">'+evts.length+' interview'+(evts.length>1?'s':'')+'</span>' : '<span style="font-size:12px;color:var(--text3)">No interviews</span>')
        +'</div>'
        +'<div class="fc-day-body">'
            +'<div class="fc-time-col" style="height:'+24*56+'px">'+timeColHtml+'</div>'
            +'<div class="fc-day-col-main" style="position:relative;height:'+24*56+'px">'+rows+evtBlocks+'</div>'
        +'</div></div>';

    var body = document.querySelector('.fc-day-body');
    if(body){
        var firstEvt = evts[0];
        var scrollHr = firstEvt ? Math.floor(hourFracTz(firstEvt._dt))-1 : 8;
        body.scrollTop = Math.max(scrollHr,0)*56;
    }
}

/* ── Event popover ──────────────────────────────────────── */
function openEvtPopover(e, id){
    e.stopPropagation();
    var i = _fcAll.find(function(x){ return x.id===id; });
    if(!i) return;
    var c = i.candidate||{};
    document.getElementById('popName').textContent    = (c.first_name||'')+(c.last_name?' '+c.last_name:'');
    document.getElementById('popTime').textContent    = fmtDateTz(i._dt,{weekday:'short',month:'short',day:'numeric'})+' '+fmt12(i._dt)+' ('+_fcTz+')';
    document.getElementById('popType').textContent    = (i.type||'zoom').charAt(0).toUpperCase()+(i.type||'zoom').slice(1).replace('_',' ');
    document.getElementById('popDur').textContent     = (i.duration_minutes||30)+' min';
    document.getElementById('popStatus').textContent  = (i.status||'scheduled').charAt(0).toUpperCase()+(i.status||'scheduled').slice(1).replace('_',' ');
    var lr = document.getElementById('popLinkRow');
    if(i.meeting_link){ lr.style.display='flex'; document.getElementById('popLink').href=i.meeting_link; }
    else lr.style.display='none';
    var nr = document.getElementById('popNoteRow');
    if(i.notes){ nr.style.display='flex'; document.getElementById('popNote').textContent=i.notes; }
    else nr.style.display='none';
    document.getElementById('popViewBtn').onclick = function(){ closePopover(); if(c.id) viewCandidate(c.id); };

    var pop = document.getElementById('fcPopover');
    pop.classList.add('open');
    // Position near click
    var x = e.clientX, y = e.clientY;
    var pw = 280, ph = 260;
    if(x+pw+12 > window.innerWidth)  x = x-pw-8;
    else x = x+8;
    if(y+ph+12 > window.innerHeight) y = window.innerHeight-ph-16;
    pop.style.left = x+'px';
    pop.style.top  = y+'px';
}

function closePopover(){
    document.getElementById('fcPopover').classList.remove('open');
}

document.addEventListener('click', function(e){
    var pop = document.getElementById('fcPopover');
    if(pop && pop.classList.contains('open') && !pop.contains(e.target)){
        closePopover();
    }
});

document.addEventListener('DOMContentLoaded', loadFcInterviews);
</script>
@endpush
