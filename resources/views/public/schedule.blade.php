<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Schedule Interview - {{ $company }}</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:14px}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#f0f4ff 0%,#f4f5f7 100%);min-height:100vh;display:flex;align-items:flex-start;justify-content:center;padding:40px 16px}
input,select,button{font-family:inherit;font-size:inherit}
button{cursor:pointer;border:none}
:root{
  --accent:#5ac6cc;--accent2:#4fbfc7;
  --text:#172b4d;--text2:#5e6c84;--text3:#97a0af;
  --border:#dfe1e6;--surface:#ffffff;--surface2:#f4f5f7;
  --green:#00875a;--radius:8px;--radius-lg:14px;
  --shadow:0 4px 24px rgba(0,0,0,.1);
}
.card{background:var(--surface);border-radius:var(--radius-lg);box-shadow:var(--shadow);width:100%;max-width:640px;overflow:hidden}
.card-top{background:linear-gradient(135deg,var(--accent),var(--accent2));padding:32px 36px;color:#fff}
.card-top h1{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:6px}
.card-top p{opacity:.88;font-size:14px}
.card-body{padding:32px 36px}
.info-box{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:14px 18px;margin-bottom:26px;display:flex;gap:24px;flex-wrap:wrap}
.info-box .info-item{display:flex;flex-direction:column;gap:2px}
.info-box .info-item span:first-child{font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:var(--text3);font-weight:600}
.info-box .info-item span:last-child{font-size:14px;font-weight:600;color:var(--text)}
/* Calendar */
.cal-wrap{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:20px}
.cal-nav{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:var(--surface2);border-bottom:1px solid var(--border)}
.cal-nav-btn{background:none;padding:4px 10px;border-radius:6px;color:var(--text2);font-size:16px;line-height:1;transition:.12s}
.cal-nav-btn:hover{background:var(--border);color:var(--text)}
.cal-nav-month{font-weight:700;font-size:15px;color:var(--text)}
.cal-dow{display:grid;grid-template-columns:repeat(7,1fr);background:var(--surface2);border-bottom:1px solid var(--border)}
.cal-dow span{text-align:center;padding:7px 0;font-size:11px;font-weight:700;letter-spacing:.6px;color:var(--text3);text-transform:uppercase}
.cal-days{display:grid;grid-template-columns:repeat(7,1fr);background:var(--surface)}
.cal-day{position:relative;aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:500;color:var(--text3);border:1px solid transparent}
.cal-day.other-month{color:var(--border)}
.cal-day.today .day-num{font-weight:700;color:var(--text2)}
.cal-day.has-slots{cursor:pointer;color:var(--text)}
.cal-day.has-slots .day-num{width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:rgba(90,198,204,.12);color:var(--accent);font-weight:700;transition:.15s}
.cal-day.has-slots:hover .day-num{background:rgba(90,198,204,.25)}
.cal-day.has-slots.selected .day-num{background:var(--accent);color:#fff}
.cal-day .slot-dot{position:absolute;bottom:5px;left:50%;transform:translateX(-50%);width:5px;height:5px;border-radius:50%;background:var(--accent);opacity:.7}
.cal-day.selected .slot-dot{background:#fff;opacity:.9}
/* Time picker */
.time-panel{display:none;margin-bottom:20px}
.time-panel.open{display:block}
.time-panel-header{font-size:12px;text-transform:uppercase;letter-spacing:1px;color:var(--text3);font-weight:600;margin-bottom:10px}
.time-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(112px,1fr));gap:8px}
.time-btn{padding:10px 8px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--surface);color:var(--text2);text-align:center;cursor:pointer;transition:all .15s;font-size:13px;font-weight:500}
.time-btn:hover{border-color:var(--accent);color:var(--accent)}
.time-btn.selected{border-color:var(--accent);background:rgba(90,198,204,.1);color:var(--accent);font-weight:700}
/* Summary + confirm */
.summary-box{display:none;background:rgba(90,198,204,.07);border:1.5px solid rgba(90,198,204,.3);border-radius:var(--radius);padding:14px 18px;margin-bottom:18px;font-size:13px;color:var(--text2)}
.summary-box strong{color:var(--text)}
.btn-confirm{width:100%;padding:15px;border-radius:var(--radius);font-weight:700;font-size:15px;background:var(--accent);color:#fff;transition:all .15s;opacity:.35;pointer-events:none}
.btn-confirm.active{opacity:1;pointer-events:all}
.btn-confirm.active:hover{background:var(--accent2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(90,198,204,.4)}
/* Already booked */
.already-booked{background:#f0fff8;border:1.5px solid rgba(0,135,90,.25);border-radius:var(--radius);padding:24px;text-align:center;color:var(--green)}
.already-booked h3{font-size:16px;font-weight:700;margin-bottom:6px}
.already-booked p{font-size:13px;opacity:.8}
.error-msg{background:rgba(222,53,11,.06);border:1px solid rgba(222,53,11,.2);border-radius:var(--radius);padding:12px 16px;color:#de350b;font-size:13px;margin-bottom:16px}
.no-slots-msg{text-align:center;padding:28px;color:var(--text3);font-size:13px}
</style>
</head>
<body>
<div class="card">
  <div class="card-top">
    <p style="font-size:12px;opacity:.7;margin-bottom:8px;text-transform:uppercase;letter-spacing:1px">Interview Scheduling</p>
    <h1>{{ $company }}</h1>
    <p>Hi {{ $candidate->first_name }}, please select a time for your interview.</p>
  </div>

  <div class="card-body">

    @if(session('error'))
      <div class="error-msg">{{ session('error') }}</div>
    @endif

    @if($existing)
      <div class="already-booked">
        <h3>Interview Already Scheduled</h3>
        <p>
          You have an interview on
          <strong>{{ $existingLocal?->format('l, F j, Y \a\t g:i A') }} ({{ $existingLocal?->format('T') }})</strong>.
          <br>We'll be in touch shortly with details.
        </p>
      </div>
    @else

      <div class="info-box">
        <div class="info-item">
          <span>Position</span>
          <span>{{ $candidate->category?->name ?? 'Open Position' }}</span>
        </div>
        <div class="info-item">
          <span>Duration</span>
          <span>{{ \App\Models\Setting::get('interview_duration', 45) }} minutes</span>
        </div>
        <div class="info-item">
          <span>Format</span>
          <span>{{ ucfirst(str_replace('_', ' ', \App\Models\Setting::get('default_interview_type', 'zoom'))) }}</span>
        </div>
      </div>

      @if($slots->isEmpty())
        <div class="no-slots-msg">No availability has been published yet. Please check back soon.</div>
      @else
      <form method="POST" action="/schedule/{{ $token }}/book" id="bookForm">
        @csrf
        <input type="hidden" name="slot_id" id="slotId">

        <!-- Calendar -->
        <div class="cal-wrap">
          <div class="cal-nav">
            <button type="button" class="cal-nav-btn" id="calPrev">&#8249;</button>
            <span class="cal-nav-month" id="calMonthLabel"></span>
            <button type="button" class="cal-nav-btn" id="calNext">&#8250;</button>
          </div>
          <div class="cal-dow">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
          </div>
          <div class="cal-days" id="calDays"></div>
        </div>

        <!-- Time picker (shown when a date is selected) -->
        <div class="time-panel" id="timePanel">
          <div class="time-panel-header" id="timePanelLabel">Available times</div>
          <div class="time-grid" id="timeGrid"></div>
        </div>

        <!-- Summary -->
        <div class="summary-box" id="summaryBox">
          Your interview: <strong id="summaryText"></strong>
        </div>

        <button type="submit" class="btn-confirm" id="btnConfirm">Confirm Interview</button>
      </form>
      @endif

    @endif

    <p style="text-align:center;font-size:11px;color:var(--text3);margin-top:24px">{{ $company }} - Powered by HRPortal</p>
  </div>
</div>

<script>
(function(){
  var systemTz = @json($timezone ?? 'America/New_York');

  var rawSlots = [
    @foreach($slots as $slot)
      {id:{{ $slot->id }},s:"{{ $slot->starts_at->toIso8601String() }}",e:"{{ $slot->ends_at->toIso8601String() }}"}@if(!$loop->last),@endif
    @endforeach
  ];

  if (!rawSlots.length) return;

  // Build groupedByDate using SYSTEM timezone from settings.
  var grouped = {}; // 'YYYY-MM-DD' -> [{id,s,e}, ...]

  function dateKeyInSystemTz(input) {
    var d = new Date(input);
    var parts = new Intl.DateTimeFormat('en-CA', {
      timeZone: systemTz,
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    }).formatToParts(d);
    var year = parts.find(function(p){ return p.type === 'year'; }).value;
    var month = parts.find(function(p){ return p.type === 'month'; }).value;
    var day = parts.find(function(p){ return p.type === 'day'; }).value;
    return year + '-' + month + '-' + day;
  }

  function dateFromKey(key) {
    var y = parseInt(key.slice(0, 4), 10);
    var m = parseInt(key.slice(5, 7), 10) - 1;
    var d = parseInt(key.slice(8, 10), 10);
    return new Date(Date.UTC(y, m, d, 12, 0, 0));
  }

  function fmtTimeInSystemTz(input) {
    return new Intl.DateTimeFormat('en-US', {
      timeZone: systemTz,
      hour: 'numeric',
      minute: '2-digit',
      hour12: true
    }).format(new Date(input));
  }

  function fmtDateLabelInSystemTz(key) {
    return new Intl.DateTimeFormat('en-US', {
      timeZone: systemTz,
      weekday: 'long',
      month: 'long',
      day: 'numeric'
    }).format(dateFromKey(key));
  }

  function monthLabelInSystemTz(y, mZeroBased) {
    return new Intl.DateTimeFormat('en-US', {
      timeZone: systemTz,
      month: 'long',
      year: 'numeric'
    }).format(new Date(Date.UTC(y, mZeroBased, 1, 12, 0, 0)));
  }

  function weekdayShortInSystemTz(y, mZeroBased, d) {
    return new Intl.DateTimeFormat('en-US', {
      timeZone: systemTz,
      weekday: 'short'
    }).format(new Date(Date.UTC(y, mZeroBased, d, 12, 0, 0)));
  }

  rawSlots.forEach(function(slot) {
    var key = dateKeyInSystemTz(slot.s);
    if (!grouped[key]) grouped[key] = [];
    grouped[key].push(slot);
  });

  var MONTHS = ['January','February','March','April','May','June',
                'July','August','September','October','November','December'];

  // Start calendar on the month of the first available slot
  var firstKey   = Object.keys(grouped).sort()[0];
  var viewYear   = parseInt(firstKey.slice(0, 4));
  var viewMonth  = parseInt(firstKey.slice(5, 7)) - 1; // 0-based

  var selectedDateKey  = null;
  var selectedSlotId   = null;

  function localDateKey(y, m, d) {
    return y + '-' + String(m + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
  }

  function renderCalendar() {
    document.getElementById('calMonthLabel').textContent = monthLabelInSystemTz(viewYear, viewMonth);

    var grid    = document.getElementById('calDays');
    grid.innerHTML = '';

    var todayKey  = dateKeyInSystemTz(new Date().toISOString());
    var firstDay  = new Date(viewYear, viewMonth, 1).getDay(); // 0=Sun
    var daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

    // Leading empty cells
    for (var i = 0; i < firstDay; i++) {
      var emp = document.createElement('div');
      emp.className = 'cal-day other-month';
      grid.appendChild(emp);
    }

    for (var d = 1; d <= daysInMonth; d++) {
      var key   = localDateKey(viewYear, viewMonth, d);
      var cell  = document.createElement('div');
      cell.className = 'cal-day';

      var numSpan = document.createElement('span');
      numSpan.className = 'day-num';
      numSpan.textContent = d;
      cell.appendChild(numSpan);

      if (key === todayKey) cell.classList.add('today');

      if (grouped[key]) {
        cell.classList.add('has-slots');
        cell.dataset.date = key;

        var dot = document.createElement('span');
        dot.className = 'slot-dot';
        cell.appendChild(dot);

        if (key === selectedDateKey) cell.classList.add('selected');

        cell.addEventListener('click', function() { selectDate(this.dataset.date); });
      }

      grid.appendChild(cell);
    }
  }

  function selectDate(key) {
    selectedDateKey = key;
    selectedSlotId  = null;
    document.getElementById('slotId').value = '';
    document.getElementById('summaryBox').style.display = 'none';
    document.getElementById('btnConfirm').classList.remove('active');

    renderCalendar(); // re-render to reflect selection

    // Populate time panel
    document.getElementById('timePanelLabel').textContent =
      fmtDateLabelInSystemTz(key) + ' (' + systemTz + ')';

    var timeGrid = document.getElementById('timeGrid');
    timeGrid.innerHTML = '';

    (grouped[key] || [])
      .slice()
      .sort(function(a, b) { return new Date(a.s) - new Date(b.s); })
      .forEach(function(slot) {
        var btn      = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'time-btn';
        btn.textContent = fmtTimeInSystemTz(slot.s) + ' - ' + fmtTimeInSystemTz(slot.e);
        btn.dataset.slotId = slot.id;
        btn.addEventListener('click', function() { selectTime(this, slot.s, slot.e, key); });
        timeGrid.appendChild(btn);
      });

    var panel = document.getElementById('timePanel');
    panel.classList.add('open');
    // Scroll time panel into view smoothly
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function selectTime(btn, startsAtIso, endsAtIso, dateKey) {
    document.querySelectorAll('.time-btn').forEach(function(b) { b.classList.remove('selected'); });
    btn.classList.add('selected');
    selectedSlotId = btn.dataset.slotId;
    document.getElementById('slotId').value = selectedSlotId;

    document.getElementById('summaryText').textContent =
      fmtDateLabelInSystemTz(dateKey) + ' at '
      + fmtTimeInSystemTz(startsAtIso) + ' - ' + fmtTimeInSystemTz(endsAtIso)
      + ' (' + systemTz + ')';

    document.getElementById('summaryBox').style.display = 'block';
    document.getElementById('btnConfirm').classList.add('active');
  }

  // Month navigation
  document.getElementById('calPrev').addEventListener('click', function() {
    viewMonth--;
    if (viewMonth < 0) { viewMonth = 11; viewYear--; }
    renderCalendar();
  });
  document.getElementById('calNext').addEventListener('click', function() {
    viewMonth++;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    renderCalendar();
  });

  // Initial render
  renderCalendar();
})();
</script>
</body>
</html>
