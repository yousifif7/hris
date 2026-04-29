<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Schedule Interview — {{ $company }}</title>
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
.card{background:var(--surface);border-radius:var(--radius-lg);box-shadow:var(--shadow);width:100%;max-width:600px;overflow:hidden}
.card-top{background:linear-gradient(135deg,var(--accent),var(--accent2));padding:32px 36px;color:#fff}
.card-top h1{font-family:'Playfair Display',serif;font-size:24px;margin-bottom:6px}
.card-top p{opacity:.88;font-size:14px}
.card-body{padding:36px}
.section-title{font-size:12px;text-transform:uppercase;letter-spacing:1.2px;color:var(--text3);font-weight:600;margin-bottom:14px}
.info-box{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:24px;display:flex;gap:20px;flex-wrap:wrap}
.info-box .info-item{display:flex;flex-direction:column;gap:2px}
.info-box .info-item span:first-child{font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:var(--text3);font-weight:600}
.info-box .info-item span:last-child{font-size:14px;font-weight:600;color:var(--text)}
.step{margin-bottom:28px}
.step h3{font-size:13px;font-weight:700;color:var(--text);margin-bottom:10px}
.date-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(88px,1fr));gap:8px;margin-bottom:4px}
.date-btn{padding:10px 6px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--surface);color:var(--text2);text-align:center;cursor:pointer;transition:all .15s;line-height:1.3;font-size:12px;font-weight:500}
.date-btn:hover{border-color:var(--accent);color:var(--accent)}
.date-btn.selected{border-color:var(--accent);background:rgba(90,198,204,.1);color:var(--accent);font-weight:700}
.date-btn .day-name{font-size:11px;color:inherit;opacity:.7;display:block}
.time-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px}
.time-btn{padding:10px 8px;border:1.5px solid var(--border);border-radius:var(--radius);background:var(--surface);color:var(--text2);text-align:center;cursor:pointer;transition:all .15s;font-size:13px;font-weight:500}
.time-btn:hover{border-color:var(--accent);color:var(--accent)}
.time-btn.selected{border-color:var(--accent);background:rgba(90,198,204,.1);color:var(--accent);font-weight:700}
.step-divider{border:none;border-top:1px solid var(--border);margin:20px 0}
.btn-confirm{width:100%;padding:15px;border-radius:var(--radius);font-weight:700;font-size:15px;background:var(--accent);color:#fff;transition:all .15s;margin-top:8px;opacity:.4;pointer-events:none}
.btn-confirm.active{opacity:1;pointer-events:all}
.btn-confirm.active:hover{background:var(--accent2);transform:translateY(-1px);box-shadow:0 4px 14px rgba(90,198,204,.4)}
.summary-box{display:none;background:rgba(90,198,204,.07);border:1.5px solid rgba(90,198,204,.3);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;font-size:13px;color:var(--text2)}
.summary-box strong{color:var(--text)}
.already-booked{background:#f0fff8;border:1.5px solid rgba(0,135,90,.25);border-radius:var(--radius);padding:24px;text-align:center;color:var(--green)}
.already-booked h3{font-size:16px;font-weight:700;margin-bottom:6px}
.already-booked p{font-size:13px;opacity:.8}
.error-msg{background:rgba(222,53,11,.06);border:1px solid rgba(222,53,11,.2);border-radius:var(--radius);padding:12px 16px;color:#de350b;font-size:13px;margin-bottom:16px}
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
        <h3>✓ Interview Already Scheduled</h3>
        <p>You have an interview on <strong>{{ $existing->scheduled_at->format('l, F j, Y \a\t g:i A') }}</strong>.<br>We'll be in touch with Zoom details.</p>
      </div>
    @else

      <div class="info-box">
        <div class="info-item">
          <span>Position</span>
          <span>{{ $candidate->category?->name ?? 'Open Position' }}</span>
        </div>
        <div class="info-item">
          <span>Duration</span>
          <span>15–20 minutes</span>
        </div>
        <div class="info-item">
          <span>Format</span>
          <span>Zoom</span>
        </div>
      </div>

      <form method="POST" action="/schedule/{{ $token }}/book" id="bookForm">
        @csrf
        <input type="hidden" name="slot_id" id="slotId">

        {{-- Step 1: Pick a date --}}
        <div class="step" id="stepDate">
          <h3>1 — Pick a date</h3>
          <div class="date-grid" id="dateGrid"></div>
          <p id="noSlotsMsg" style="display:none;color:var(--text3);font-size:12px;margin-top:8px">No availability has been published yet. Please check back soon.</p>
        </div>

        <hr class="step-divider">

        {{-- Step 2: Pick a time --}}
        <div class="step" id="stepTime" style="opacity:.4;pointer-events:none">
          <h3>2 — Pick a time</h3>
          <div class="time-grid" id="timeGrid"></div>
        </div>

        <hr class="step-divider">

        {{-- Summary --}}
        <div class="summary-box" id="summaryBox">
          Your interview: <strong id="summaryText"></strong>
        </div>

        <button type="submit" class="btn-confirm" id="btnConfirm">Confirm Interview</button>
      </form>

    @endif

    <p style="text-align:center;font-size:11px;color:var(--text3);margin-top:24px">{{ $company }} · Powered by HRPortal</p>
  </div>
</div>

<script>
(function(){
  var slots = [
    @foreach($slots as $slot)
      {
        id: {{ $slot->id }},
        starts_at: "{{ $slot->starts_at->toIso8601String() }}",
        ends_at: "{{ $slot->ends_at->toIso8601String() }}"
      }@if(! $loop->last),@endif
    @endforeach
  ];

  var selectedDate = null;
  var selectedTime = null;
  var selectedSlotId = null;

  var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var dateGrid = document.getElementById('dateGrid');

  var groupedByDate = {};
  slots.forEach(function(slot){
    var dt = new Date(slot.starts_at);
    var key = dt.toISOString().slice(0, 10);
    if(!groupedByDate[key]) groupedByDate[key] = [];
    groupedByDate[key].push(slot);
  });

  var sortedDates = Object.keys(groupedByDate).sort();
  if(!sortedDates.length){
    document.getElementById('noSlotsMsg').style.display = 'block';
    return;
  }

  sortedDates.forEach(function(dateKey){
    var d = new Date(dateKey + 'T12:00:00');
    var btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'date-btn';
    btn.dataset.date = dateKey;
    btn.innerHTML = '<span class="day-name">'+days[d.getDay()]+'</span>' + months[d.getMonth()] + ' ' + d.getDate();
    btn.addEventListener('click', function(){ selectDate(this); });
    dateGrid.appendChild(btn);
  });

  function fmtTime(dt){
    var h = dt.getHours();
    var m = dt.getMinutes();
    var ampm = h < 12 ? 'AM' : 'PM';
    var h12 = h === 0 ? 12 : (h > 12 ? h - 12 : h);
    return h12 + ':' + String(m).padStart(2, '0') + ' ' + ampm;
  }

  function selectDate(btn){
    document.querySelectorAll('.date-btn').forEach(function(b){ b.classList.remove('selected'); });
    btn.classList.add('selected');
    selectedDate = btn.dataset.date;
    selectedTime = null;
    selectedSlotId = null;

    var timeGrid = document.getElementById('timeGrid');
    timeGrid.innerHTML = '';

    (groupedByDate[selectedDate] || []).sort(function(a,b){
      return new Date(a.starts_at) - new Date(b.starts_at);
    }).forEach(function(slot){
      var startsAt = new Date(slot.starts_at);
      var endsAt = new Date(slot.ends_at);
      var tb = document.createElement('button');
      tb.type = 'button';
      tb.className = 'time-btn';
      tb.textContent = fmtTime(startsAt) + ' - ' + fmtTime(endsAt);
      tb.dataset.slotId = slot.id;
      tb.dataset.time = startsAt.toISOString();
      tb.addEventListener('click', function(){ selectTime(this, startsAt, endsAt); });
      timeGrid.appendChild(tb);
    });

    var stepTime = document.getElementById('stepTime');
    stepTime.style.opacity = '1';
    stepTime.style.pointerEvents = 'auto';
    document.getElementById('summaryBox').style.display = 'none';
    document.getElementById('btnConfirm').classList.remove('active');
  }

  function selectTime(btn, startsAt, endsAt){
    document.querySelectorAll('.time-btn').forEach(function(b){ b.classList.remove('selected'); });
    btn.classList.add('selected');
    selectedTime = btn.dataset.time;
    selectedSlotId = btn.dataset.slotId;

    document.getElementById('slotId').value = selectedSlotId;

    // Build human-readable summary
    var dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    document.getElementById('summaryText').textContent =
      dayNames[startsAt.getDay()] + ', ' + monthNames[startsAt.getMonth()] + ' ' + startsAt.getDate() + ' at '
      + fmtTime(startsAt) + ' - ' + fmtTime(endsAt);

    document.getElementById('summaryBox').style.display = 'block';
    document.getElementById('btnConfirm').classList.add('active');
  }
})();
</script>
</body>
</html>
