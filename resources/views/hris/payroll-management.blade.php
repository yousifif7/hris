@extends('layouts.app')
@section('title','McCrory Center — Financial and Payroll Information')
@section('content')
<div class="animate-in">

  <!-- Payroll Records Section -->
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:8px">
    <div class="section-title" style="margin:0">Payroll Records</div>
    <button class="btn btn-primary btn-sm" onclick="openGeneratePayroll()">+ Generate Payroll</button>
  </div>
  <p style="color:var(--text2);margin-bottom:16px;font-size:13px">Create, view, and manage payroll records for each pay period.</p>

  <div class="table-wrap" style="margin-bottom:40px">
    <table>
      <thead>
        <tr>
          <th>Employee</th>
          <th>Period</th>
          <th>Frequency</th>
          <th>Regular Pay</th>
          <th>OT Pay</th>
          <th>Gross</th>
          <th>Net</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="payrollRecordsTbody">
        <tr><td colspan="9" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Employee Pay Setup Section -->
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:8px">
    <div class="section-title" style="margin:0">Employee Pay Setup</div>
  </div>
  <p style="color:var(--text2);margin-bottom:16px;font-size:13px">Set employment type, pay rate, pay type, start date, and work location for employees.</p>

  <div class="table-wrap">
    <table>
      <thead>
        <tr><th>Employee</th><th>Role</th><th>Type</th><th>Pay</th><th>Start Date</th><th>Location</th><th>Actions</th></tr>
      </thead>
      <tbody id="payrollTbody">
        <tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Generate Payroll Modal -->
<div class="modal-overlay" id="modal-generatePayroll" onclick="if(event.target===this)closeModal('generatePayroll')">
  <div class="modal" style="max-width:580px">
    <div class="modal-header"><h3>Generate Payroll</h3><button onclick="closeModal('generatePayroll')">✕</button></div>
    <div class="modal-body">
      <div class="form-group">
        <label>Employee <span style="color:var(--red)">*</span></label>
        <select id="gpEmployee" onchange="gpUpdatePreview()">
          <option value="">— Select Employee —</option>
        </select>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Pay Frequency <span style="color:var(--red)">*</span></label>
          <select id="gpFreq" onchange="gpUpdatePreview()">
            <option value="weekly">Weekly</option>
            <option value="biweekly" selected>Bi-Weekly</option>
            <option value="semi_monthly">Semi-Monthly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
        <div class="form-group">
          <label>Status</label>
          <select id="gpStatus">
            <option value="draft">Draft</option>
            <option value="finalized">Finalized</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Period Start <span style="color:var(--red)">*</span></label><input type="date" id="gpStart"></div>
        <div class="form-group"><label>Period End <span style="color:var(--red)">*</span></label><input type="date" id="gpEnd"></div>
      </div>
      <div class="form-group"><label>Pay Date</label><input type="date" id="gpPayDate"></div>
      <div class="form-row">
        <div class="form-group"><label>Regular Hours</label><input type="number" id="gpRegHours" step="0.01" min="0" placeholder="e.g. 80" oninput="gpUpdatePreview()"></div>
        <div class="form-group"><label>Overtime Hours</label><input type="number" id="gpOtHours" step="0.01" min="0" placeholder="0" oninput="gpUpdatePreview()"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Bonus ($)</label><input type="number" id="gpBonus" step="0.01" min="0" placeholder="0.00" oninput="gpUpdatePreview()"></div>
        <div class="form-group"><label>Deductions ($)</label><input type="number" id="gpDeductions" step="0.01" min="0" placeholder="0.00" oninput="gpUpdatePreview()"></div>
      </div>
      <div class="form-group"><label>Notes</label><textarea id="gpNotes" rows="2"></textarea></div>
      <div id="gpPreview" style="display:none;background:var(--bg2);border-radius:8px;padding:14px;margin-top:8px;font-size:13px">
        <div style="font-weight:600;margin-bottom:8px;color:var(--text1)">Pay Preview</div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px">
          <div><span style="color:var(--text3)">Regular</span><br><strong id="gpPrevReg">$0.00</strong></div>
          <div><span style="color:var(--text3)">Overtime</span><br><strong id="gpPrevOt">$0.00</strong></div>
          <div><span style="color:var(--text3)">Bonus</span><br><strong id="gpPrevBonus">$0.00</strong></div>
          <div><span style="color:var(--text3)">Gross</span><br><strong id="gpPrevGross">$0.00</strong></div>
          <div><span style="color:var(--text3)">Deductions</span><br><strong id="gpPrevDeduct">$0.00</strong></div>
          <div><span style="color:var(--text3)">Net Pay</span><br><strong id="gpPrevNet" style="color:var(--green);font-size:15px">$0.00</strong></div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('generatePayroll')">Cancel</button>
      <button class="btn btn-primary" onclick="saveGeneratePayroll()">Generate</button>
    </div>
  </div>
</div>

<!-- Edit Payroll Modal -->
<div class="modal-overlay" id="modal-editPayroll" onclick="if(event.target===this)closeModal('editPayroll')">
  <div class="modal" style="max-width:540px">
    <div class="modal-header"><h3>Edit Payroll Record</h3><button onclick="closeModal('editPayroll')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="epId">
      <input type="hidden" id="epEmpId">
      <div class="form-row">
        <div class="form-group"><label>Pay Frequency</label>
          <select id="epFreq">
            <option value="weekly">Weekly</option>
            <option value="biweekly">Bi-Weekly</option>
            <option value="semi_monthly">Semi-Monthly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>
        <div class="form-group"><label>Status</label>
          <select id="epStatus">
            <option value="draft">Draft</option>
            <option value="finalized">Finalized</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Period Start</label><input type="date" id="epStart"></div>
        <div class="form-group"><label>Period End</label><input type="date" id="epEnd"></div>
      </div>
      <div class="form-group"><label>Pay Date</label><input type="date" id="epPayDate"></div>
      <div class="form-row">
        <div class="form-group"><label>Regular Hours</label><input type="number" id="epRegHours" step="0.01" min="0"></div>
        <div class="form-group"><label>Overtime Hours</label><input type="number" id="epOtHours" step="0.01" min="0"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Bonus ($)</label><input type="number" id="epBonus" step="0.01" min="0"></div>
        <div class="form-group"><label>Deductions ($)</label><input type="number" id="epDeductions" step="0.01" min="0"></div>
      </div>
      <div class="form-group"><label>Notes</label><textarea id="epNotes" rows="2"></textarea></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('editPayroll')">Cancel</button>
      <button class="btn btn-primary" onclick="savePayrollEdit()">Save Changes</button>
    </div>
  </div>
</div>

<!-- Edit Employee Pay Setup Modal -->
<div class="modal-overlay" id="modal-payrollEdit" onclick="if(event.target===this)closeModal('payrollEdit')">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3>Edit Pay Setup</h3><button onclick="closeModal('payrollEdit')">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="payrollEmpId">
      <div class="form-row">
        <div class="form-group"><label>Employment Type</label>
          <select id="payrollEmpType">
            <option value="full_time">Full-Time</option>
            <option value="part_time">Part-Time</option>
            <option value="contract">Contract / 1099</option>
          </select>
        </div>
        <div class="form-group"><label>Start Date</label><input type="date" id="payrollStartDate"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Pay Rate</label><input type="number" id="payrollRate" step="0.01" placeholder="e.g. 20.00 or 45000"></div>
        <div class="form-group"><label>Pay Type</label>
          <select id="payrollPayType">
            <option value="hourly">Hourly</option>
            <option value="salary">Salary (Annual)</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Location</label><input id="payrollLocation"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('payrollEdit')">Cancel</button>
      <button class="btn btn-primary" onclick="savePayrollSetup()">Save Changes</button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
var _payrollEmployees = [];
var _payrollRecords = [];
var FREQ_DIVISORS = {weekly:52, biweekly:26, semi_monthly:24, monthly:12};

function calcPreview(emp, freq, regHours, otHours, bonus, deductions){
  if(!emp || !emp.pay_rate) return null;
  var rate = parseFloat(emp.pay_rate);
  var payType = emp.pay_type || 'hourly';
  var divisor = FREQ_DIVISORS[freq] || 26;
  var effectiveHourly, regularPay;
  if(payType === 'salary'){
    regularPay = rate / divisor;
    effectiveHourly = rate / 2080;
  } else {
    regularPay = regHours * rate;
    effectiveHourly = rate;
  }
  var otPay = otHours * effectiveHourly * 1.5;
  var gross = regularPay + otPay + bonus;
  var net = Math.max(gross - deductions, 0);
  return {regularPay:regularPay, otPay:otPay, gross:gross, net:net};
}

function fmt(n){ return '$'+parseFloat(n||0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,','); }

async function pageRefresh(){ await Promise.all([loadPayrollRecords(), loadPayrollEmployees()]); }

document.addEventListener('DOMContentLoaded', function(){
  loadPayrollRecords();
  loadPayrollEmployees();
});

// ---- Payroll Records ----
async function loadPayrollRecords(){
  var r = await apiFetch('/api/payrolls?per_page=200');
  if(!r) return;
  var data = await r.json();
  _payrollRecords = data.data || [];
  var tbody = document.getElementById('payrollRecordsTbody');
  if(!_payrollRecords.length){
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:24px;color:var(--text3)">No payroll records yet. Click "Generate Payroll" to create one.</td></tr>';
    return;
  }
  tbody.innerHTML = _payrollRecords.map(function(p){
    var name = esc(p.employee ? p.employee.first_name+' '+p.employee.last_name : '—');
    var period = esc(p.period_start ? p.period_start.substring(0,10) : '—')+' – '+esc(p.period_end ? p.period_end.substring(0,10) : '—');
    var freq = (p.frequency||'').replace('_','-');
    var statusBadge = p.status === 'finalized'
      ? '<span class="badge badge-offer-accepted">Finalized</span>'
      : '<span class="badge badge-pending">Draft</span>';
    return '<tr>'
      +'<td><div class="candidate-name">'+name+'</div></td>'
      +'<td style="font-size:12px">'+period+'</td>'
      +'<td style="text-transform:capitalize">'+esc(freq)+'</td>'
      +'<td>'+fmt(p.regular_pay)+'</td>'
      +'<td>'+fmt(p.overtime_pay)+'</td>'
      +'<td><strong>'+fmt(p.gross_pay)+'</strong></td>'
      +'<td style="color:var(--green);font-weight:700">'+fmt(p.net_pay)+'</td>'
      +'<td>'+statusBadge+'</td>'
      +'<td class="actions-cell" style="white-space:nowrap">'
        +'<button class="btn btn-secondary btn-sm" onclick="window.location.href=\'/hris/payrolls/'+p.id+'\'">View</button> '
        +'<button class="btn btn-warning btn-sm" onclick="openEditPayroll('+p.id+')">✏</button> '
        +'<a href="/hris/payrolls/'+p.id+'/pdf" target="_blank" class="btn btn-secondary btn-sm" style="text-decoration:none">PDF</a> '
        +'<button class="btn btn-danger btn-sm" onclick="deletePayroll('+p.id+')">✕</button>'
      +'</td>'
    +'</tr>';
  }).join('');
}

// ---- Generate Payroll ----
function openGeneratePayroll(){
  var sel = document.getElementById('gpEmployee');
  sel.innerHTML = '<option value="">— Select Employee —</option>'
    + _payrollEmployees.filter(function(e){ return e.pay_rate; }).map(function(e){
        return '<option value="'+e.id+'">'+esc(e.first_name+' '+e.last_name)+' ('+esc(e.pay_type||'hourly')+' $'+parseFloat(e.pay_rate).toFixed(2)+')</option>';
      }).join('');
  ['gpFreq','gpStatus','gpStart','gpEnd','gpPayDate','gpRegHours','gpOtHours','gpBonus','gpDeductions','gpNotes'].forEach(function(id){
    var el = document.getElementById(id);
    if(el.tagName === 'SELECT') return;
    el.value = '';
  });
  document.getElementById('gpFreq').value = 'biweekly';
  document.getElementById('gpStatus').value = 'draft';
  document.getElementById('gpPreview').style.display = 'none';
  openModal('generatePayroll');
}

function gpUpdatePreview(){
  var empId = parseInt(document.getElementById('gpEmployee').value);
  var emp = _payrollEmployees.find(function(e){ return e.id === empId; });
  var freq = document.getElementById('gpFreq').value;
  var regHours = parseFloat(document.getElementById('gpRegHours').value) || 0;
  var otHours = parseFloat(document.getElementById('gpOtHours').value) || 0;
  var bonus = parseFloat(document.getElementById('gpBonus').value) || 0;
  var deductions = parseFloat(document.getElementById('gpDeductions').value) || 0;
  var preview = calcPreview(emp, freq, regHours, otHours, bonus, deductions);
  var pv = document.getElementById('gpPreview');
  if(!preview){ pv.style.display = 'none'; return; }
  pv.style.display = 'block';
  document.getElementById('gpPrevReg').textContent = fmt(preview.regularPay);
  document.getElementById('gpPrevOt').textContent = fmt(preview.otPay);
  document.getElementById('gpPrevBonus').textContent = fmt(bonus);
  document.getElementById('gpPrevGross').textContent = fmt(preview.gross);
  document.getElementById('gpPrevDeduct').textContent = fmt(deductions);
  document.getElementById('gpPrevNet').textContent = fmt(preview.net);
}

async function saveGeneratePayroll(){
  var empId = document.getElementById('gpEmployee').value;
  if(!empId){ toast('Please select an employee.','error'); return; }
  var body = {
    employee_id: parseInt(empId),
    frequency: document.getElementById('gpFreq').value,
    status: document.getElementById('gpStatus').value,
    period_start: document.getElementById('gpStart').value,
    period_end: document.getElementById('gpEnd').value,
    pay_date: document.getElementById('gpPayDate').value || null,
    regular_hours: parseFloat(document.getElementById('gpRegHours').value) || 0,
    overtime_hours: parseFloat(document.getElementById('gpOtHours').value) || 0,
    bonus: parseFloat(document.getElementById('gpBonus').value) || 0,
    deductions: parseFloat(document.getElementById('gpDeductions').value) || 0,
    notes: document.getElementById('gpNotes').value || null,
  };
  var r = await apiFetch('/api/payrolls', {method:'POST', body:JSON.stringify(body)});
  if(!r||!r.ok){ var e=r?await r.json():{};
    var msg = e.message || 'Failed to generate payroll.';
    if(e.errors){ msg = Object.values(e.errors).flat().join('; '); }
    toast(msg,'error'); return;
  }
  toast('Payroll record generated!','success');
  closeModal('generatePayroll');
  loadPayrollRecords();
}

// ---- Edit Payroll ----
function openEditPayroll(id){
  var p = _payrollRecords.find(function(r){ return r.id === id; });
  if(!p) return;
  document.getElementById('epId').value = p.id;
  document.getElementById('epEmpId').value = p.employee_id;
  document.getElementById('epFreq').value = p.frequency;
  document.getElementById('epStatus').value = p.status;
  document.getElementById('epStart').value = p.period_start ? p.period_start.substring(0,10) : '';
  document.getElementById('epEnd').value = p.period_end ? p.period_end.substring(0,10) : '';
  document.getElementById('epPayDate').value = p.pay_date ? p.pay_date.substring(0,10) : '';
  document.getElementById('epRegHours').value = p.regular_hours;
  document.getElementById('epOtHours').value = p.overtime_hours;
  document.getElementById('epBonus').value = p.bonus;
  document.getElementById('epDeductions').value = p.deductions;
  document.getElementById('epNotes').value = p.notes || '';
  openModal('editPayroll');
}

async function savePayrollEdit(){
  var id = document.getElementById('epId').value;
  var body = {
    employee_id: parseInt(document.getElementById('epEmpId').value),
    frequency: document.getElementById('epFreq').value,
    status: document.getElementById('epStatus').value,
    period_start: document.getElementById('epStart').value,
    period_end: document.getElementById('epEnd').value,
    pay_date: document.getElementById('epPayDate').value || null,
    regular_hours: parseFloat(document.getElementById('epRegHours').value) || 0,
    overtime_hours: parseFloat(document.getElementById('epOtHours').value) || 0,
    bonus: parseFloat(document.getElementById('epBonus').value) || 0,
    deductions: parseFloat(document.getElementById('epDeductions').value) || 0,
    notes: document.getElementById('epNotes').value || null,
  };
  var r = await apiFetch('/api/payrolls/'+id, {method:'PATCH', body:JSON.stringify(body)});
  if(!r||!r.ok){ var e=r?await r.json():{};
    var msg = e.message || 'Update failed.';
    if(e.errors){ msg = Object.values(e.errors).flat().join('; '); }
    toast(msg,'error'); return;
  }
  toast('Payroll record updated!','success');
  closeModal('editPayroll');
  loadPayrollRecords();
}

async function deletePayroll(id){
  if(!confirm('Delete this payroll record? This cannot be undone.')) return;
  var r = await apiFetch('/api/payrolls/'+id, {method:'DELETE'});
  if(!r||!r.ok){ toast('Delete failed','error'); return; }
  toast('Deleted.','success');
  loadPayrollRecords();
}

// ---- Employee Pay Setup ----
async function loadPayrollEmployees(){
  var r = await apiFetch('/api/employees?per_page=200');
  if(!r) return;
  var data = await r.json();
  _payrollEmployees = data.data || [];
  var tbody = document.getElementById('payrollTbody');
  if(!_payrollEmployees.length){
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text3)">No employees found.</td></tr>';
    return;
  }
  tbody.innerHTML = _payrollEmployees.map(function(e){
    return '<tr>'
      +'<td><div class="candidate-name">'+esc((e.first_name||'')+' '+(e.last_name||''))+'</div><div class="candidate-sub">'+esc(e.email||'')+'</div></td>'
      +'<td>'+esc(e.role||'—')+'</td>'
      +'<td>'+esc((e.employment_type||'—').replace('_',' '))+'</td>'
      +'<td>'+esc(e.pay_rate ? '$'+parseFloat(e.pay_rate).toFixed(2)+(e.pay_type==='hourly'?'/hr':'/yr') : '—')+'</td>'
      +'<td>'+esc(e.start_date||'—')+'</td>'
      +'<td>'+esc(e.location||'—')+'</td>'
      +'<td class="actions-cell"><button class="btn btn-warning btn-sm" onclick="openPayrollEdit('+e.id+')">✏ Edit</button></td>'
    +'</tr>';
  }).join('');
}

function openPayrollEdit(id){
  var e = _payrollEmployees.find(function(emp){ return emp.id === id; });
  if(!e) return;
  document.getElementById('payrollEmpId').value = e.id;
  document.getElementById('payrollEmpType').value = e.employment_type || 'full_time';
  document.getElementById('payrollStartDate').value = e.start_date || '';
  document.getElementById('payrollRate').value = e.pay_rate || '';
  document.getElementById('payrollPayType').value = e.pay_type || 'hourly';
  document.getElementById('payrollLocation').value = e.location || '';
  openModal('payrollEdit');
}

async function savePayrollSetup(){
  var id = document.getElementById('payrollEmpId').value;
  var body = {
    employment_type: document.getElementById('payrollEmpType').value,
    start_date: document.getElementById('payrollStartDate').value || null,
    pay_rate: document.getElementById('payrollRate').value ? parseFloat(document.getElementById('payrollRate').value) : null,
    pay_type: document.getElementById('payrollPayType').value,
    location: document.getElementById('payrollLocation').value || null,
  };
  var r = await apiFetch('/api/employees/'+id, {method:'PATCH', body:JSON.stringify(body)});
  if(!r || !r.ok){ var e=r?await r.json():{};toast(e.message||'Update failed','error');return; }
  closeModal('payrollEdit');
  toast('Pay setup updated!');
  loadPayrollEmployees();
}
</script>
@endpush