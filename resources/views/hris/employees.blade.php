@extends('layouts.app')
@section('title','TalentFlow — Employees')
@section('content')
<div class="animate-in">
  <div style="display:flex;gap:8px;margin-bottom:20px;align-items:center;flex-wrap:wrap">
    <input id="empSearch" placeholder="Search employees…" style="width:220px" oninput="filterEmps()">
    <select id="empDept" onchange="filterEmps()" style="width:180px"><option value="">All departments</option></select>
    <select id="empType" onchange="filterEmps()" style="width:160px">
      <option value="">All types</option>
      <option value="full_time">Full-time</option>
      <option value="part_time">Part-time</option>
      <option value="contract">Contract</option>
    </select>
    <span style="flex:1"></span>
    <span id="empCount" style="font-size:13px;color:var(--text3)"></span>
  </div>
  <div class="emp-grid" id="empGrid">
    <div style="text-align:center;padding:60px;color:var(--text3);grid-column:1/-1">⏳ Loading…</div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _allEmps = [];

async function pageRefresh(){ await loadEmployees(); }

async function loadEmployees(){
    var r = await apiFetch('/api/employees?per_page=100');
    if(!r) return;
    var data = await r.json();
    _allEmps = data.data || [];
    // Build department filter
    var depts = {};
    _allEmps.forEach(function(e){ if(e.department) depts[e.department]=true; });
    var sel = document.getElementById('empDept');
    sel.innerHTML = '<option value="">All departments</option>';
    Object.keys(depts).sort().forEach(function(d){
        var o = document.createElement('option');
        o.value = d; o.textContent = d;
        sel.appendChild(o);
    });
    renderEmps(_allEmps);
}

function filterEmps(){
    var q    = (document.getElementById('empSearch').value||'').toLowerCase();
    var dept = document.getElementById('empDept').value;
    var type = document.getElementById('empType').value;
    var filtered = _allEmps.filter(function(e){
        var nm = (e.first_name+' '+e.last_name).toLowerCase();
        return (!q    || nm.includes(q) || (e.email||'').toLowerCase().includes(q) || (e.role||'').toLowerCase().includes(q))
            && (!dept || e.department===dept)
            && (!type || e.employment_type===type);
    });
    renderEmps(filtered);
}

function renderEmps(emps){
    document.getElementById('empCount').textContent = emps.length+' employee'+(emps.length!==1?'s':'');
    var grid = document.getElementById('empGrid');
    if(!emps.length){
        grid.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3);grid-column:1/-1">No employees found.</div>';
        return;
    }
    grid.innerHTML = emps.map(function(e){
        var trainings    = e.trainings || [];
        var pendingTrain = trainings.filter(function(t){ return !t.is_completed; }).length;
        return '<div class="emp-card" onclick="viewEmployee('+e.id+')" style="cursor:pointer">'
          +'<div class="header">'
            +'<div class="avatar" style="background:'+Cl(e.id)+'">'+In(e.first_name,e.last_name)+'</div>'
            +'<div class="info"><h4>'+esc(e.first_name+' '+e.last_name)+'</h4><p>'+esc(e.role||'—')+'</p></div>'
          +'</div>'
          +'<div class="details">'
            +(e.employment_type?'<span class="detail-tag">'+esc(e.employment_type.replace('_',' '))+'</span>':'')
            +(e.department?'<span class="detail-tag">'+esc(e.department)+'</span>':'')
            +(e.start_date?'<span class="detail-tag">Since '+esc(e.start_date)+'</span>':'')
            +(pendingTrain?'<span class="detail-tag" style="background:var(--yellow-bg,#fef9e7);color:var(--yellow,#d4ac0d)">⚠ '+pendingTrain+' training due</span>':'')
          +'</div>'
          +'<div style="margin-top:10px;font-size:12px;color:var(--text3)">'+esc(e.email||'')+'</div>'
        +'</div>';
    }).join('');
}

async function viewEmployee(id){
    var r = await apiFetch('/api/employees/'+id);
    if(!r) return;
    var e = await r.json();
    document.getElementById('detailName').textContent = e.first_name+' '+e.last_name;
    var trainHtml = (e.trainings||[]).map(function(t){
        var col = t.is_completed?'var(--green)':'var(--yellow,#d4ac0d)';
        return '<div class="bg-check-row">'
          +'<span>'+esc(t.name)+'</span>'
          +'<span style="color:'+col+';font-weight:600">'+(t.is_completed?'Complete ✓':'Due '+fDate(t.due_date))+'</span>'
        +'</div>';
    }).join('');
    document.getElementById('detailBody').innerHTML =
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;margin-bottom:14px">'
        +'<div><span style="color:var(--text3)">Email</span><br>'+esc(e.email||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Phone</span><br>'+esc(e.phone||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Role</span><br>'+esc(e.role||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Type</span><br>'+esc((e.employment_type||'—').replace('_',' '))+'</div>'
        +'<div><span style="color:var(--text3)">Department</span><br>'+esc(e.department||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Start Date</span><br>'+esc(e.start_date||'—')+'</div>'
        +'<div><span style="color:var(--text3)">Pay</span><br>'+esc(e.pay_rate?'$'+parseFloat(e.pay_rate).toFixed(2)+(e.pay_type==='hourly'?'/hr':'/yr'):'—')+'</div>'
        +'<div><span style="color:var(--text3)">Location</span><br>'+esc(e.location||'—')+'</div>'
      +'</div>'
      +(trainHtml?'<div class="section-title" style="font-size:13px;margin-bottom:8px">📚 Trainings & Certs</div><div class="bg-check-grid">'+trainHtml+'</div>':'');
    document.getElementById('detailFooter').innerHTML='<button class="btn btn-secondary" onclick="closeModal(\'candidateDetail\')">Close</button>';
    openModal('candidateDetail');
}

document.addEventListener('DOMContentLoaded', loadEmployees);
</script>
@endpush
