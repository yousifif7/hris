@extends('layouts.app')
@section('title','McCrory Center — Financial and Payroll Information')
@section('content')
<div class="animate-in">
  <div class="section-title">Financial and Payroll Information</div>
  <p style="color:var(--text2);margin-bottom:20px;font-size:13px">Use this page to set employment type, pay rate, pay type, start date, and work location for employees.</p>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Employee</th><th>Role</th><th>Type</th><th>Pay</th><th>Start Date</th><th>Location</th><th>Actions</th></tr></thead>
      <tbody id="payrollTbody"><tr><td colspan="7" style="text-align:center;padding:24px;color:var(--text3)">⏳ Loading...</td></tr></tbody>
    </table>
  </div>
</div>

<div class="modal-overlay" id="modal-payrollEdit" onclick="if(event.target===this)closeModal('payrollEdit')">
  <div class="modal" style="max-width:520px">
    <div class="modal-header"><h3>Edit Payroll Setup</h3><button onclick="closeModal('payrollEdit')">✕</button></div>
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
        <div class="form-group"><label>Pay Rate</label><input type="number" id="payrollRate" step="0.01"></div>
        <div class="form-group"><label>Pay Type</label>
          <select id="payrollPayType">
            <option value="hourly">Hourly</option>
            <option value="salary">Salary</option>
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

async function pageRefresh(){ await loadPayrollEmployees(); }

document.addEventListener('DOMContentLoaded', loadPayrollEmployees);

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
    if(!r || !r.ok){ var e=r?await r.json():{}; toast(e.message||'Update failed','error'); return; }
    closeModal('payrollEdit');
    toast('Payroll information updated!');
    loadPayrollEmployees();
}
</script>
@endpush