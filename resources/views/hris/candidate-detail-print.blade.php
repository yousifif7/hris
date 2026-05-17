<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $candidate->first_name }} {{ $candidate->last_name }} — Staff Portal</title>
  <style>
    @page { margin: 18mm; }
    body{font-family:Arial,sans-serif;color:#172b4d;font-size:12px;line-height:1.45;margin:0}
    h1{font-size:20px;margin:0 0 4px 0}
    h2{font-size:14px;margin:18px 0 8px 0;border-bottom:1px solid #ccc;padding-bottom:4px;color:#0a5b62}
    table{width:100%;border-collapse:collapse;margin-bottom:6px}
    td{padding:4px 6px;vertical-align:top}
    td.label{width:32%;color:#5e6c84;font-size:11px;text-transform:uppercase;letter-spacing:.4px}
    .meta{color:#5e6c84;font-size:11px;margin-bottom:14px}
    .print-actions{margin:0 0 18px 0}
    .print-actions button{padding:6px 14px;border:1px solid #ccc;border-radius:4px;background:#f5f5f5;cursor:pointer}
    @media print { .print-actions { display:none } }
    .empty{color:#97a0af}
    .checklist{margin:0;padding-left:18px;font-size:12px}
  </style>
</head>
<body>
  <div class="print-actions">
    <button onclick="window.print()">Print / Save as PDF</button>
    <button onclick="window.close()">Close</button>
  </div>

  <h1>{{ $candidate->first_name }} {{ $candidate->last_name }}</h1>
  <div class="meta">
    Staff Portal · Status: {{ $candidate->status?->label() ?? '—' }} · Created {{ $candidate->created_at->format('M j, Y h:i a') }}
  </div>

  @php
    $row = function(string $label, $value) {
        $blank = $value === null || $value === '' || (is_array($value) && empty($value));
        if ($blank) {
            return '<tr><td class="label">'.e($label).'</td><td class="empty">None</td></tr>';
        }
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        return '<tr><td class="label">'.e($label).'</td><td>'.nl2br(e((string)$value)).'</td></tr>';
    };
    $date = fn($v) => $v ? $v->format('M j, Y') : null;
  @endphp

  <h2>Pre-Screening</h2>
  <table>
    {!! $row('Candidate For', $candidate->candidate_for) !!}
    {!! $row('Email', $candidate->email) !!}
    {!! $row('Phone', $candidate->phone) !!}
    {!! $row('Resume w/ Applicable Experience', $candidate->resume_w_applicable_experience) !!}
    {!! $row('Pre-Screen Note', $candidate->pre_screen_note) !!}
    {!! $row('Pre-Screening Status', $candidate->pre_screening_status) !!}
  </table>

  <h2>Pre-Interview Questions</h2>
  <table>
    {!! $row('Full or Part Time', $candidate->full_or_part_time) !!}
    {!! $row('Ideal Schedule', $candidate->ideal_schedule) !!}
    {!! $row('Description', $candidate->description) !!}
    {!! $row('Days Available', $candidate->days_available) !!}
    {!! $row('Years Experience', $candidate->years_experience) !!}
    {!! $row('Position', $candidate->position) !!}
    {!! $row('Clinical Position Type', $candidate->clinical_position_type) !!}
    {!! $row('Staff Type', $candidate->staff_type) !!}
  </table>

  <h2>Verification, References &amp; Background Check</h2>
  <table>
    {!! $row('Background Check Status', $candidate->background_check_status) !!}
    {!! $row('Background Check Expiration', $date($candidate->background_check_expires_at)) !!}
    {!! $row('Identification Expiration', $date($candidate->identification_expires_at)) !!}
    {!! $row('i9 Verification', $candidate->i9_verification) !!}
    {!! $row('Reference # 1', trim(($candidate->reference_1_name ?? '').' '.($candidate->reference_1_phone ? '· '.$candidate->reference_1_phone : '').' '.($candidate->reference_1_association ? '· '.$candidate->reference_1_association : ''))) !!}
    {!! $row('Reference # 2', trim(($candidate->reference_2_name ?? '').' '.($candidate->reference_2_phone ? '· '.$candidate->reference_2_phone : '').' '.($candidate->reference_2_association ? '· '.$candidate->reference_2_association : ''))) !!}
  </table>
  @if (is_array($candidate->onboarding_documents_checklist) && count($candidate->onboarding_documents_checklist))
    <strong>Onboarding Documents Checked:</strong>
    <ul class="checklist">
      @foreach ($candidate->onboarding_documents_checklist as $item)
        <li>{{ $item }}</li>
      @endforeach
    </ul>
  @endif

  <h2>Offer Letter</h2>
  <table>
    {!! $row('Date', $date($candidate->offer_date)) !!}
    {!! $row('McCrory Center', $candidate->offer_mccrory_center) !!}
    {!! $row('Operations Manager', $candidate->operations_manager) !!}
    {!! $row('Clinical Supervisor', $candidate->clinical_supervisor) !!}
    {!! $row('Anticipated Start Date', $date($candidate->earliest_start_date)) !!}
    {!! $row('Amount', $candidate->offer_amount !== null ? '$'.number_format((float)$candidate->offer_amount, 2) : null) !!}
    {!! $row('Payment Frequency', $candidate->payment_frequency) !!}
    {!! $row('Company Representative', $candidate->company_representative) !!}
    {!! $row('Deadline Date For Acceptances', $date($candidate->offer_deadline_date)) !!}
  </table>

  <h2>Pre-Onboard Documents</h2>
  <table>
    {!! $row('College Degree', $candidate->college_degree) !!}
    {!! $row('College Transcripts', $candidate->college_transcripts) !!}
    {!! $row('CPR Certification', $candidate->cpr_certification) !!}
    {!! $row('CPR Certification Expiration', $date($candidate->cpr_certification_expires_at)) !!}
    {!! $row('Child Registry Clearance', $candidate->child_registry_clearance) !!}
    {!! $row('Child Registry Clearance Expiration', $date($candidate->child_registry_clearance_expires_at)) !!}
    {!! $row('TB Expiration', $date($candidate->tb_expires_at)) !!}
    {!! $row('TB Test Results', $candidate->tb_test_results) !!}
    {!! $row('DWIHN Transcripts', $candidate->dwihn_transcripts) !!}
    {!! $row('i9', $candidate->i9_document) !!}
  </table>

  <h2>Compliance Agreements</h2>
  <table>
    {!! $row('BAA Agreement', $candidate->baa_agreement) !!}
    {!! $row('Non-Disclosure Agreement (HIPAA)', $candidate->nda_hipaa) !!}
    {!! $row('Acknowledgement Of Review Handbook', $candidate->acknowledgement_handbook ? 'Yes' : null) !!}
  </table>

  <h2>Clinical Staff Documents</h2>
  <table>
    {!! $row('Professional General Liability Insurance', $candidate->professional_general_liability_insurance) !!}
    {!! $row('PGL Insurance Expiration', $date($candidate->pgl_insurance_expires_at)) !!}
    {!! $row('Clinical Licenses', $candidate->clinical_licenses) !!}
    {!! $row('Clinical Licenses Expiration Date', $date($candidate->clinical_license_expires_at)) !!}
    {!! $row('Medversant Application Confirmation', $candidate->medversant_application_confirmation) !!}
    {!! $row('Writing Sample', $candidate->writing_sample) !!}
  </table>

  <h2>Emergency Contact</h2>
  <table>
    {!! $row('Emergency Contact #1 Name', $candidate->emergency_contact_1_name) !!}
    {!! $row('Emergency Contact #1 Phone', $candidate->emergency_contact_1_phone) !!}
    {!! $row('Emergency Contact #2 Name', $candidate->emergency_contact_2_name) !!}
    {!! $row('Emergency Contact #2 Phone', $candidate->emergency_contact_2_phone) !!}
  </table>

  <h2>Training and Development</h2>
  <table>
    {!! $row('Recipient Rights Training', $candidate->recipient_rights_training_name) !!}
    {!! $row('Recipient Rights Training Expiration', $date($candidate->recipient_rights_training_expires_at)) !!}
    {!! $row('Handbook', $candidate->handbook) !!}
    {!! $row('Annual CEUs', $candidate->annual_ceus_name) !!}
  </table>
</body>
</html>
