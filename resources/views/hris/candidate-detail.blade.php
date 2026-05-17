@extends('layouts.app')
@section('title','McCrory Center — '.$candidate->first_name.' '.$candidate->last_name)
@section('content')
<style>
  .cd-wrap{display:grid;grid-template-columns:minmax(0,1fr) 320px;gap:18px;align-items:start}
  @media(max-width:1100px){ .cd-wrap{grid-template-columns:1fr} }

  .cd-toolbar{display:flex;align-items:center;gap:10px;margin-bottom:14px}
  .cd-toolbar .crumbs a{color:var(--text3);text-decoration:none;font-size:13px}
  .cd-toolbar .crumbs a:hover{color:var(--accent)}
  .cd-toolbar .crumbs .sep{color:var(--text3);margin:0 6px}
  .cd-toolbar h2{font-size:18px;font-weight:600;color:var(--text);display:flex;align-items:center;gap:8px}

  .cd-tabs{display:flex;flex-wrap:wrap;gap:0;border-bottom:1px solid var(--border);margin-bottom:18px}
  .cd-tab{padding:10px 14px;font-size:13px;color:var(--text2);background:transparent;border:none;cursor:pointer;border-bottom:2px solid transparent;white-space:nowrap}
  .cd-tab.active{color:var(--accent);border-bottom-color:var(--accent);font-weight:600}
  .cd-tab:hover{color:var(--text)}

  .cd-panel{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:18px;margin-bottom:18px;display:none}
  .cd-panel.active{display:block}
  .cd-panel h3{font-size:14px;font-weight:600;color:var(--accent);margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border)}
  .cd-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 28px}
  @media(max-width:720px){ .cd-grid{grid-template-columns:1fr} }
  .cd-field{display:flex;flex-direction:column;gap:4px;font-size:13px}
  .cd-field label{font-size:11px;color:var(--text3);text-transform:none;letter-spacing:0}
  .cd-field .value{color:var(--text);padding:4px 0;min-height:22px}
  .cd-field .value.empty{color:var(--text3)}
  .cd-field input, .cd-field textarea, .cd-field select{font-size:13px;padding:6px 8px;border:1px solid var(--border);border-radius:6px;background:var(--surface);color:var(--text);width:100%}
  .cd-field textarea{resize:vertical;min-height:80px;font-family:inherit}
  .cd-field .pencil{font-size:11px;color:var(--text3);cursor:pointer;margin-left:6px}
  .cd-field .pencil:hover{color:var(--accent)}
  .cd-checklist{display:flex;flex-direction:column;gap:4px;font-size:13px}
  .cd-checklist label{display:flex;align-items:center;gap:8px;color:var(--text);cursor:pointer}

  .cd-desc{white-space:pre-wrap;line-height:1.7;font-size:13px;color:var(--text);max-height:340px;overflow:hidden;position:relative}
  .cd-desc.open{max-height:none}
  .cd-desc-fade{position:absolute;bottom:0;left:0;right:0;height:60px;background:linear-gradient(to bottom, transparent, var(--surface));pointer-events:none}
  .cd-desc.open + .cd-desc-fade{display:none}
  .cd-more{margin-top:10px;color:var(--accent);font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:4px;font-weight:500}

  .cd-right{display:flex;flex-direction:column;gap:14px;position:sticky;top:16px}
  .cd-aside{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:14px;display:flex;flex-direction:column;gap:14px;font-size:13px}
  .cd-side-card{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:12px 14px;font-size:13px}
  .cd-side-card .cd-side-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
  .cd-side-card .cd-side-head h4{font-size:13px;font-weight:600;color:var(--accent);margin:0}
  .cd-side-card .cd-side-icons{display:flex;gap:6px;color:var(--text3)}
  .cd-side-card .cd-side-icons button{background:none;border:none;padding:2px;color:var(--text3);cursor:pointer;border-radius:4px;display:inline-flex;align-items:center;justify-content:center}
  .cd-side-card .cd-side-icons button:hover{color:var(--accent);background:var(--accent-glow)}
  .cd-side-card .cd-side-empty{color:var(--text3);font-size:12px;padding:6px 0}
  .cd-side-card .cd-side-list{display:flex;flex-direction:column;gap:6px}
  .cd-side-item{display:flex;gap:8px;align-items:flex-start;font-size:12px;color:var(--text2);padding:6px 0;border-top:1px solid var(--border)}
  .cd-side-item:first-child{border-top:none}
  .cd-side-item .cd-side-icon{flex-shrink:0;color:var(--text3);width:18px;text-align:center}
  .cd-side-item .cd-side-meta{color:var(--text3);font-size:11px;margin-top:2px}
  .cd-task-row{display:flex;align-items:center;gap:8px;padding:5px 0;border-top:1px solid var(--border);font-size:12px;color:var(--text2)}
  .cd-task-row:first-child{border-top:none}
  .cd-task-row input[type=checkbox]{margin:0}
  .cd-task-row.done label{text-decoration:line-through;color:var(--text3)}
  .cd-task-row .cd-task-delete{margin-left:auto;background:none;border:none;color:var(--text3);cursor:pointer;font-size:14px;padding:0 4px}
  .cd-task-row .cd-task-delete:hover{color:#e35454}
  .cd-aside-row label{font-size:11px;color:var(--text3);display:block;margin-bottom:3px}
  .cd-aside-row .value{color:var(--text);min-height:20px}
  .cd-aside-row .value.empty{color:var(--text3)}
  .cd-aside-row input, .cd-aside-row select{font-size:12px;padding:5px 7px;border:1px solid var(--border);border-radius:5px;background:var(--surface);color:var(--text);width:100%}

  .cd-section{background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:14px;margin-bottom:14px}
  .cd-section h3{font-size:13px;font-weight:600;color:var(--accent);margin-bottom:10px}
  .cd-stream-item{display:flex;gap:10px;padding:10px 0;border-top:1px solid var(--border);font-size:13px;color:var(--text2)}
  .cd-stream-item:first-of-type{border-top:none}
  .cd-stream-avatar{width:30px;height:30px;border-radius:50%;background:var(--accent-glow);color:var(--accent);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;flex-shrink:0}
  .cd-comment{border:1px solid var(--border);border-radius:6px;padding:8px;background:var(--surface)}
  .cd-comment input{border:none;outline:none;width:100%;background:transparent;color:var(--text);font-size:13px}
</style>

<div class="cd-toolbar">
  <div class="crumbs">
    <a href="{{ route('hris.staff-portals') }}">Staff Portals</a>
    <span class="sep">›</span>
  </div>
  <h2><span class="dot" style="width:8px;height:8px;background:var(--accent);border-radius:2px;display:inline-block"></span>{{ $candidate->first_name }} {{ $candidate->last_name }}</h2>
  <span style="flex:1"></span>
  <button class="btn btn-secondary" onclick="cdToggleEdit()">✏ <span id="cdEditLabel">Edit</span></button>
  <div style="position:relative">
    <button class="btn btn-secondary" title="More" onclick="cdToggleMore(event)">⋯</button>
    <div id="cdMoreMenu" style="display:none;position:absolute;right:0;top:100%;margin-top:4px;background:var(--surface);border:1px solid var(--border);border-radius:6px;box-shadow:0 6px 20px rgba(0,0,0,.12);min-width:200px;z-index:30;padding:6px;font-size:13px">
      <a href="javascript:cdRemoveCandidate()"     class="cd-more-item">Remove</a>
      <a href="javascript:cdDuplicateCandidate()"  class="cd-more-item">Duplicate</a>
      <div style="height:1px;background:var(--border);margin:5px 0"></div>
      <a href="javascript:cdViewPersonalData()"    class="cd-more-item">View Personal Data</a>
      <a href="javascript:cdViewFollowers()"       class="cd-more-item">View Followers</a>
      <a href="javascript:cdViewAuditLog()"        class="cd-more-item">View Audit Log</a>
      <a href="javascript:cdViewUserAccess()"      class="cd-more-item">View User Access</a>
      <div style="height:1px;background:var(--border);margin:5px 0"></div>
      <a href="{{ route('hris.staff-portal.print', $candidate) }}" target="_blank" rel="noopener" class="cd-more-item">Print to PDF</a>
    </div>
  </div>
</div>

<style>
  .cd-more-item{display:block;padding:7px 10px;border-radius:4px;color:var(--text);text-decoration:none;cursor:pointer}
  .cd-more-item:hover{background:var(--surface2);color:var(--accent)}
  .cd-more-modal-body dt{font-size:11px;color:var(--text3);text-transform:uppercase;letter-spacing:.4px;margin-top:8px}
  .cd-more-modal-body dd{margin:2px 0 0;color:var(--text);font-size:13px}
  .cd-audit-row{padding:8px 0;border-top:1px solid var(--border);font-size:12px}
  .cd-audit-row:first-of-type{border-top:none}
  .cd-audit-meta{color:var(--text3);font-size:11px}
</style>

<div class="modal-overlay" id="modal-cdMoreModal" onclick="if(event.target===this)closeModal('cdMoreModal')">
  <div class="modal" style="max-width:560px">
    <div class="modal-header"><h3 id="cdMoreModalTitle"></h3><button onclick="closeModal('cdMoreModal')">✕</button></div>
    <div class="modal-body cd-more-modal-body" id="cdMoreModalBody"></div>
    <div class="modal-footer"><button class="btn btn-secondary" onclick="closeModal('cdMoreModal')">Close</button></div>
  </div>
</div>

<div class="cd-tabs" id="cdTabs">
  <button class="cd-tab active" data-tab="hiring">Hiring</button>
  <button class="cd-tab" data-tab="pre-screening">Pre-Screening</button>
  <button class="cd-tab" data-tab="pre-interview">Pre-Interview Questions</button>
  <button class="cd-tab" data-tab="verification">Verification and Review</button>
  <button class="cd-tab" data-tab="offer-letter">Offer Letter</button>
  <button class="cd-tab" data-tab="pre-onboard">Pre-Onboard Documents</button>
  <button class="cd-tab" data-tab="compliance">Compliance Agreements</button>
  <button class="cd-tab" data-tab="clinical-staff">Clinical Staff Documents</button>
  <button class="cd-tab" data-tab="emergency">Emergency Contact</button>
  <button class="cd-tab" data-tab="training">Training and Development</button>
  <button class="cd-tab" data-tab="financial">Financial and Payroll Information</button>
  <button class="cd-tab" data-tab="post-offer">Post-Offer Documents</button>
  <button class="cd-tab" data-tab="dwc">DWC Trainings</button>
  <button class="cd-tab" data-tab="additional">Additional</button>
  <button class="cd-tab" data-tab="job-description">Job Description Letter</button>
</div>

<div class="cd-wrap">
  <div>
    {{-- ───────── Hiring ───────── --}}
    <div class="cd-panel active" data-tab-panel="hiring">
      <h3>Hiring</h3>
      <label class="cd-field" style="margin-bottom:4px"><label style="font-size:11px;color:var(--text3)">Position Description</label></label>
      <div class="cd-desc" id="cdDesc">{{ $positionDescription }}</div>
      <div class="cd-desc-fade" id="cdDescFade"></div>
      <div class="cd-more" onclick="cdToggleDesc()" id="cdMore">▼ See more</div>

      <div class="cd-edit-only" style="margin-top:14px;display:none">
        <label style="font-size:11px;color:var(--text3);display:block;margin-bottom:4px">Edit Position Description (saved globally)</label>
        <textarea id="cdPositionDescription" rows="14" style="width:100%;font-size:13px;padding:8px;border:1px solid var(--border);border-radius:6px;font-family:inherit">{{ $positionDescription }}</textarea>
        <button class="btn btn-primary btn-sm" style="margin-top:8px" onclick="cdSaveDescription()">Save Position Description</button>
      </div>
    </div>

    {{-- ───────── Pre-Screening ───────── --}}
    <div class="cd-panel" data-tab-panel="pre-screening">
      <h3>Pre-Screening</h3>
      <div class="cd-field" style="margin-bottom:14px">
        <label>Candidate For</label>
        <span class="cd-view value {{ $candidate->candidate_for ? '' : 'empty' }}">{{ $candidate->candidate_for ?: 'None' }}</span>
        <input class="cd-edit" data-field="candidate_for" type="text" value="{{ $candidate->candidate_for }}" style="display:none">
      </div>
      <div class="cd-grid">
        <div class="cd-field">
          <label>First Name</label>
          <span class="cd-view value">{{ $candidate->first_name }}</span>
          <input class="cd-edit" data-field="first_name" type="text" value="{{ $candidate->first_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Last Name</label>
          <span class="cd-view value">{{ $candidate->last_name }}</span>
          <input class="cd-edit" data-field="last_name" type="text" value="{{ $candidate->last_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Email</label>
          <span class="cd-view value {{ $candidate->email ? '' : 'empty' }}">
            @if($candidate->email)<a href="mailto:{{ $candidate->email }}" style="color:var(--accent)">{{ $candidate->email }}</a>@else None @endif
          </span>
          <input class="cd-edit" data-field="email" type="email" value="{{ $candidate->email }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Phone</label>
          <span class="cd-view value {{ $candidate->phone ? '' : 'empty' }}">
            @if($candidate->phone)<a href="tel:{{ $candidate->phone }}" style="color:var(--accent)">{{ $candidate->phone }}</a> <span style="color:var(--text3);font-size:11px">Mobile</span>@else None @endif
          </span>
          <input class="cd-edit" data-field="phone" type="tel" value="{{ $candidate->phone }}" style="display:none">
        </div>
      </div>
      <div class="cd-field" style="margin-top:14px">
        <label>Resume w/ Applicable Experience</label>
        <span class="cd-view value {{ $candidate->resume_w_applicable_experience ? '' : 'empty' }}" style="white-space:pre-wrap">{{ $candidate->resume_w_applicable_experience ?: 'None' }}</span>
        <textarea class="cd-edit" data-field="resume_w_applicable_experience" rows="4" style="display:none">{{ $candidate->resume_w_applicable_experience }}</textarea>
      </div>
      <div class="cd-field" style="margin-top:14px">
        <label>Pre-Screen Note</label>
        <span class="cd-view value {{ $candidate->pre_screen_note ? '' : 'empty' }}">{{ $candidate->pre_screen_note ?: 'None' }}</span>
        <textarea class="cd-edit" data-field="pre_screen_note" rows="3" style="display:none">{{ $candidate->pre_screen_note }}</textarea>
      </div>
      <div class="cd-field" style="margin-top:14px">
        <label>Pre-Screening Status</label>
        <span class="cd-view value {{ $candidate->pre_screening_status ? '' : 'empty' }}">{{ $candidate->pre_screening_status ?: 'None' }}</span>
        <select class="cd-edit" data-field="pre_screening_status" style="display:none">
          <option value="">Select…</option>
          @foreach (['Added to Database','Reviewed','Passed','Failed','On Hold'] as $opt)
            <option value="{{ $opt }}" @if($candidate->pre_screening_status===$opt) selected @endif>{{ $opt }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- ───────── Pre-Interview Questions ───────── --}}
    <div class="cd-panel" data-tab-panel="pre-interview">
      <h3>Pre-Interview Questions</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>Are you looking for full or part time work?</label>
          <span class="cd-view value {{ $candidate->full_or_part_time ? '' : 'empty' }}">{{ $candidate->full_or_part_time ?: 'None' }}</span>
          <select class="cd-edit" data-field="full_or_part_time" style="display:none">
            <option value="">Select…</option>
            @foreach (['Full Time','Part Time','Either'] as $opt)
              <option value="{{ $opt }}" @if($candidate->full_or_part_time===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>What is your ideal schedule or between the hours of?</label>
          @php
            $scheduleOpts = ['8:30am-4:00pm (Full Time)','10:00am-6:00pm (Full Time)','12:00pm-8:00pm (Full Time)','9:30am-2:30pm (Part Time)'];
            $scheduleSel = is_array($candidate->ideal_schedule) ? $candidate->ideal_schedule : [];
          @endphp
          <div class="cd-checklist">
            @foreach ($scheduleOpts as $opt)
              <label>
                <input type="checkbox" data-multi-field="ideal_schedule" value="{{ $opt }}" @if(in_array($opt, $scheduleSel)) checked @endif disabled>
                {{ $opt }}
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="cd-field" style="margin-top:14px">
        <label>Description</label>
        <span class="cd-view value {{ $candidate->description ? '' : 'empty' }}" style="white-space:pre-wrap">{{ $candidate->description ?: 'None' }}</span>
        <textarea class="cd-edit" data-field="description" rows="3" style="display:none">{{ $candidate->description }}</textarea>
      </div>

      <div class="cd-grid" style="margin-top:14px">
        <div class="cd-field">
          <label>Days Available</label>
          @php
            $daysOpts = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            $daysSel  = is_array($candidate->days_available) ? $candidate->days_available : [];
          @endphp
          <div class="cd-checklist">
            @foreach ($daysOpts as $day)
              <label>
                <input type="checkbox" data-multi-field="days_available" value="{{ $day }}" @if(in_array($day, $daysSel)) checked @endif disabled>
                {{ $day }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cd-field">
          <label>Number Of Years Experience</label>
          <span class="cd-view value {{ $candidate->years_experience !== null ? '' : 'empty' }}">{{ $candidate->years_experience !== null ? $candidate->years_experience : 'None' }}</span>
          <input class="cd-edit" data-field="years_experience" type="number" min="0" max="60" value="{{ $candidate->years_experience }}" style="display:none">
        </div>
      </div>

      <div class="cd-grid" style="margin-top:14px">
        <div class="cd-field">
          <label>position</label>
          <span class="cd-view value {{ $candidate->position ? '' : 'empty' }}">{{ $candidate->position ?: '' }}</span>
          <input class="cd-edit" data-field="position" type="text" value="{{ $candidate->position }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Clinical Position Type</label>
          <span class="cd-view value {{ $candidate->clinical_position_type ? '' : 'empty' }}">{{ $candidate->clinical_position_type ?: 'None' }}</span>
          <input class="cd-edit" data-field="clinical_position_type" type="text" value="{{ $candidate->clinical_position_type }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>positionType</label>
          <span class="cd-view value {{ $candidate->position_type ? '' : 'empty' }}">{{ $candidate->position_type ?: '' }}</span>
          <input class="cd-edit" data-field="position_type" type="text" value="{{ $candidate->position_type }}" style="display:none">
        </div>
      </div>

      <div class="cd-field" style="margin-top:14px">
        <label>Address</label>
        @php
          $addr = collect([$candidate->street_address, $candidate->city, $candidate->state, $candidate->postal_code])->filter()->implode(', ');
        @endphp
        <span class="cd-view value {{ $addr ? '' : 'empty' }}">{{ $addr ?: 'None' }}</span>
      </div>

      <div class="cd-grid" style="margin-top:14px">
        <div class="cd-field">
          <label>Applicant Status</label>
          <span class="cd-view value">{{ $candidate->status?->label() ?? 'None' }}</span>
        </div>
        <div class="cd-field">
          <label>Staff Type</label>
          <span class="cd-view value {{ $candidate->staff_type ? '' : 'empty' }}">{{ $candidate->staff_type ?: 'None' }}</span>
          <input class="cd-edit" data-field="staff_type" type="text" value="{{ $candidate->staff_type }}" style="display:none">
        </div>
      </div>

      <div class="cd-grid" style="margin-top:14px">
        <div class="cd-field">
          <label>Signed Application</label>
          @if ($candidate->signed_application_path)
            <a class="cd-view value" href="/{{ $candidate->signed_application_path }}" target="_blank" style="color:var(--accent)">📎 {{ $candidate->signed_application_name ?: 'Document' }}</a>
          @else
            <span class="cd-view value empty">None</span>
          @endif
        </div>
        <div class="cd-field">
          <label>Authorization Background Check</label>
          @if ($candidate->authorization_background_check_path)
            <a class="cd-view value" href="/{{ $candidate->authorization_background_check_path }}" target="_blank" style="color:var(--accent)">📎 {{ $candidate->authorization_background_check_name ?: 'Document' }}</a>
          @else
            <span class="cd-view value empty">None</span>
          @endif
        </div>
      </div>
    </div>

    {{-- ───────── Verification and Review ───────── --}}
    <div class="cd-panel" data-tab-panel="verification">
      <h3>Verification, References &amp; Background Check</h3>

      <div class="cd-grid">
        <div class="cd-field">
          <label>Background Check</label>
          <span class="cd-view value {{ $candidate->background_check_status ? '' : 'empty' }}">{{ $candidate->background_check_status ?: 'None' }}</span>
          <select class="cd-edit" data-field="background_check_status" style="display:none">
            <option value="">Select…</option>
            @foreach (['Not Started','In Progress','Complete','Failed','Expired'] as $opt)
              <option value="{{ $opt }}" @if($candidate->background_check_status===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>Background Check Expiration Date</label>
          <span class="cd-view value {{ $candidate->background_check_expires_at ? '' : 'empty' }}">{{ $candidate->background_check_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="background_check_expires_at" value="{{ optional($candidate->background_check_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
      </div>

      <div class="cd-grid" style="margin-top:14px">
        <div class="cd-field">
          <label>i9 Verification</label>
          @php
            $i9Opts = ['State ID','Passport','SSN','EIN'];
            $i9Sel  = is_array($candidate->i9_verification) ? $candidate->i9_verification : [];
          @endphp
          <div class="cd-checklist">
            @foreach ($i9Opts as $opt)
              <label>
                <input type="checkbox" data-multi-field="i9_verification" value="{{ $opt }}" @if(in_array($opt, $i9Sel)) checked @endif disabled>
                {{ $opt }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="cd-field">
          <label>Identification Expiration</label>
          <span class="cd-view value {{ $candidate->identification_expires_at ? '' : 'empty' }}">{{ $candidate->identification_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="identification_expires_at" value="{{ optional($candidate->identification_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
      </div>

      <div class="cd-field" style="margin-top:18px">
        <label>Onboarding Documents Checklist</label>
        @php
          $docOpts = [
            'Signed Employment Application',
            'Resume - ensure resume includes all relevant experience, training and education',
            'Copy of degree and college transcripts',
            'Copies of any relevant training to autism evaluations/diagnosis (CEUs, etc)',
            'Copy of clinical licenses',
            'W9 or W4',
            'I-9',
            'BAA Agreement',
            'Attestation Training Form',
            'Submit child abuse and neglect registry check',
            'Copy of professional general liability insurance',
            'Direct Deposit Form',
            'Non-Disclosure Agreement (HIPPA)',
            'Writing Sample',
            'Calendar Availability',
            'Copy of ID',
            'Copy of SSN Card',
            'Emergency Contact Form',
            'Acknowledgement of review of the Best Practice Evaluation Guidelines',
            'Acknowledgement of review of McCrory Center Handbook/Policy and Procedures',
            'Complete DWIHN required trainings: https://www.dwctraining.com/Home.id.2.htm',
            'Complete Medversant application https://modahealth.providersource.com/',
            'Confirmation from Admin added to Workers Comp policy/Insurance',
          ];
          $docSel = is_array($candidate->onboarding_documents_checklist) ? $candidate->onboarding_documents_checklist : [];
        @endphp
        <div class="cd-checklist" style="gap:6px">
          @foreach ($docOpts as $opt)
            <label>
              <input type="checkbox" data-multi-field="onboarding_documents_checklist" value="{{ $opt }}" @if(in_array($opt, $docSel)) checked @endif disabled>
              <span>{{ $opt }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="cd-grid" style="margin-top:18px;grid-template-columns:1fr 1fr 1fr">
        <div class="cd-field">
          <label>Reference # 1 Name</label>
          <span class="cd-view value {{ $candidate->reference_1_name ? '' : 'empty' }}">{{ $candidate->reference_1_name ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_1_name" type="text" value="{{ $candidate->reference_1_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Reference # 1 Phone #</label>
          <span class="cd-view value {{ $candidate->reference_1_phone ? '' : 'empty' }}">{{ $candidate->reference_1_phone ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_1_phone" type="tel" value="{{ $candidate->reference_1_phone }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Association # 1</label>
          <span class="cd-view value {{ $candidate->reference_1_association ? '' : 'empty' }}">{{ $candidate->reference_1_association ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_1_association" type="text" value="{{ $candidate->reference_1_association }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Reference # 2 Name</label>
          <span class="cd-view value {{ $candidate->reference_2_name ? '' : 'empty' }}">{{ $candidate->reference_2_name ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_2_name" type="text" value="{{ $candidate->reference_2_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Reference # 2 Phone</label>
          <span class="cd-view value {{ $candidate->reference_2_phone ? '' : 'empty' }}">{{ $candidate->reference_2_phone ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_2_phone" type="tel" value="{{ $candidate->reference_2_phone }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Association # 2</label>
          <span class="cd-view value {{ $candidate->reference_2_association ? '' : 'empty' }}">{{ $candidate->reference_2_association ?: 'None' }}</span>
          <input class="cd-edit" data-field="reference_2_association" type="text" value="{{ $candidate->reference_2_association }}" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Offer Letter ───────── --}}
    <div class="cd-panel" data-tab-panel="offer-letter">
      <h3>Offer Letter</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>Date</label>
          <span class="cd-view value {{ $candidate->offer_date ? '' : 'empty' }}">{{ $candidate->offer_date?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="offer_date" value="{{ optional($candidate->offer_date)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>McCrory Center</label>
          <span class="cd-view value {{ $candidate->offer_mccrory_center ? '' : 'empty' }}">{{ $candidate->offer_mccrory_center ?: 'None' }}</span>
          <input class="cd-edit" data-field="offer_mccrory_center" type="text" value="{{ $candidate->offer_mccrory_center }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Operations Manager</label>
          <span class="cd-view value {{ $candidate->operations_manager ? '' : 'empty' }}">{{ $candidate->operations_manager ?: 'None' }}</span>
          <input class="cd-edit" data-field="operations_manager" type="text" value="{{ $candidate->operations_manager }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Clinical Supervisor</label>
          <span class="cd-view value {{ $candidate->clinical_supervisor ? '' : 'empty' }}">{{ $candidate->clinical_supervisor ?: 'None' }}</span>
          <input class="cd-edit" data-field="clinical_supervisor" type="text" value="{{ $candidate->clinical_supervisor }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Anticipated Start Date</label>
          <span class="cd-view value {{ $candidate->earliest_start_date ? '' : 'empty' }}">{{ $candidate->earliest_start_date?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="earliest_start_date" value="{{ optional($candidate->earliest_start_date)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field"><!-- spacer to keep grid aligned --></div>
        <div class="cd-field">
          <label>Amount</label>
          <span class="cd-view value {{ $candidate->offer_amount !== null ? '' : 'empty' }}">{{ $candidate->offer_amount !== null ? '$'.number_format((float)$candidate->offer_amount, 2) : 'None' }}</span>
          <input class="cd-edit" type="number" step="0.01" min="0" data-field="offer_amount" value="{{ $candidate->offer_amount }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Payment Frequency</label>
          <span class="cd-view value {{ $candidate->payment_frequency ? '' : 'empty' }}">{{ $candidate->payment_frequency ?: 'None' }}</span>
          <select class="cd-edit" data-field="payment_frequency" style="display:none">
            <option value="">Select…</option>
            @foreach (['Hourly','Weekly','Bi-Weekly','Semi-Monthly','Monthly','Annually','Per Session'] as $opt)
              <option value="{{ $opt }}" @if($candidate->payment_frequency===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>Company Representative</label>
          <span class="cd-view value {{ $candidate->company_representative ? '' : 'empty' }}">{{ $candidate->company_representative ?: 'None' }}</span>
          <input class="cd-edit" data-field="company_representative" type="text" value="{{ $candidate->company_representative }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Deadline Date For Acceptances</label>
          <span class="cd-view value {{ $candidate->offer_deadline_date ? '' : 'empty' }}">{{ $candidate->offer_deadline_date?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="offer_deadline_date" value="{{ optional($candidate->offer_deadline_date)->format('Y-m-d') }}" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Pre-Onboard Documents ───────── --}}
    <div class="cd-panel" data-tab-panel="pre-onboard">
      <h3>Pre-Onboard Documents</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>College Degree</label>
          <span class="cd-view value {{ $candidate->college_degree ? '' : 'empty' }}">{{ $candidate->college_degree ?: 'None' }}</span>
          <input class="cd-edit" data-field="college_degree" type="text" value="{{ $candidate->college_degree }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>College Transcripts</label>
          <span class="cd-view value {{ $candidate->college_transcripts ? '' : 'empty' }}">{{ $candidate->college_transcripts ?: 'None' }}</span>
          <input class="cd-edit" data-field="college_transcripts" type="text" value="{{ $candidate->college_transcripts }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>CPR certification</label>
          <span class="cd-view value {{ $candidate->cpr_certification ? '' : 'empty' }}">{{ $candidate->cpr_certification ?: 'None' }}</span>
          <input class="cd-edit" data-field="cpr_certification" type="text" value="{{ $candidate->cpr_certification }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>CPR Certification Expiration</label>
          <span class="cd-view value {{ $candidate->cpr_certification_expires_at ? '' : 'empty' }}">{{ $candidate->cpr_certification_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="cpr_certification_expires_at" value="{{ optional($candidate->cpr_certification_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Child Registry Clearance</label>
          <span class="cd-view value {{ $candidate->child_registry_clearance ? '' : 'empty' }}">{{ $candidate->child_registry_clearance ?: 'None' }}</span>
          <input class="cd-edit" data-field="child_registry_clearance" type="text" value="{{ $candidate->child_registry_clearance }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Child Registry Clearance Expiration</label>
          <span class="cd-view value {{ $candidate->child_registry_clearance_expires_at ? '' : 'empty' }}">{{ $candidate->child_registry_clearance_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="child_registry_clearance_expires_at" value="{{ optional($candidate->child_registry_clearance_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>TB Expiration</label>
          <span class="cd-view value {{ $candidate->tb_expires_at ? '' : 'empty' }}">{{ $candidate->tb_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="tb_expires_at" value="{{ optional($candidate->tb_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>TB Test Results</label>
          <span class="cd-view value {{ $candidate->tb_test_results ? '' : 'empty' }}">{{ $candidate->tb_test_results ?: 'None' }}</span>
          <input class="cd-edit" data-field="tb_test_results" type="text" value="{{ $candidate->tb_test_results }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>DWIHN Transcripts</label>
          <span class="cd-view value {{ $candidate->dwihn_transcripts ? '' : 'empty' }}">{{ $candidate->dwihn_transcripts ?: 'None' }}</span>
          <input class="cd-edit" data-field="dwihn_transcripts" type="text" value="{{ $candidate->dwihn_transcripts }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>i9</label>
          <span class="cd-view value {{ $candidate->i9_document ? '' : 'empty' }}">{{ $candidate->i9_document ?: 'None' }}</span>
          <input class="cd-edit" data-field="i9_document" type="text" value="{{ $candidate->i9_document }}" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Compliance Agreements ───────── --}}
    <div class="cd-panel" data-tab-panel="compliance">
      <h3>Compliance Agreements</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>BAA Agreement</label>
          <span class="cd-view value {{ $candidate->baa_agreement ? '' : 'empty' }}">{{ $candidate->baa_agreement ?: 'None' }}</span>
          <select class="cd-edit" data-field="baa_agreement" style="display:none">
            <option value="">Select…</option>
            @foreach (['Not Sent','Sent','Signed','Declined'] as $opt)
              <option value="{{ $opt }}" @if($candidate->baa_agreement===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>Non-Disclosure Agreement (HIPAA)</label>
          <span class="cd-view value {{ $candidate->nda_hipaa ? '' : 'empty' }}">{{ $candidate->nda_hipaa ?: 'None' }}</span>
          <select class="cd-edit" data-field="nda_hipaa" style="display:none">
            <option value="">Select…</option>
            @foreach (['Not Sent','Sent','Signed','Declined'] as $opt)
              <option value="{{ $opt }}" @if($candidate->nda_hipaa===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>Acknowledgement Of Review Handbook</label>
          <label style="display:inline-flex;align-items:center;gap:8px;color:var(--text);cursor:pointer">
            <input type="checkbox" data-bool-field="acknowledgement_handbook" @if($candidate->acknowledgement_handbook) checked @endif disabled>
            <span>Yes</span>
          </label>
        </div>
      </div>
    </div>

    {{-- ───────── Clinical Staff Documents ───────── --}}
    <div class="cd-panel" data-tab-panel="clinical-staff">
      <h3>Clinical Staff Documents</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>Professional General Liability Insurance</label>
          <span class="cd-view value {{ $candidate->professional_general_liability_insurance ? '' : 'empty' }}">{{ $candidate->professional_general_liability_insurance ?: 'None' }}</span>
          <input class="cd-edit" data-field="professional_general_liability_insurance" type="text" value="{{ $candidate->professional_general_liability_insurance }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>PGL Insurance Expiration</label>
          <span class="cd-view value {{ $candidate->pgl_insurance_expires_at ? '' : 'empty' }}">{{ $candidate->pgl_insurance_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="pgl_insurance_expires_at" value="{{ optional($candidate->pgl_insurance_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Clinical Licenses</label>
          <span class="cd-view value {{ $candidate->clinical_licenses ? '' : 'empty' }}">{{ $candidate->clinical_licenses ?: 'None' }}</span>
          <input class="cd-edit" data-field="clinical_licenses" type="text" value="{{ $candidate->clinical_licenses }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Clinical Licenses Expiration Date</label>
          <span class="cd-view value {{ $candidate->clinical_license_expires_at ? '' : 'empty' }}">{{ $candidate->clinical_license_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="clinical_license_expires_at" value="{{ optional($candidate->clinical_license_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Medversant Application Confirmation</label>
          <span class="cd-view value {{ $candidate->medversant_application_confirmation ? '' : 'empty' }}">{{ $candidate->medversant_application_confirmation ?: 'None' }}</span>
          <input class="cd-edit" data-field="medversant_application_confirmation" type="text" value="{{ $candidate->medversant_application_confirmation }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Writing Sample</label>
          <span class="cd-view value {{ $candidate->writing_sample ? '' : 'empty' }}">{{ $candidate->writing_sample ?: 'None' }}</span>
          <input class="cd-edit" data-field="writing_sample" type="text" value="{{ $candidate->writing_sample }}" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Emergency Contact ───────── --}}
    <div class="cd-panel" data-tab-panel="emergency">
      <h3>Emergency Contact</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>Emergency Contact #1 Name</label>
          <span class="cd-view value {{ $candidate->emergency_contact_1_name ? '' : 'empty' }}">{{ $candidate->emergency_contact_1_name ?: 'None' }}</span>
          <input class="cd-edit" data-field="emergency_contact_1_name" type="text" value="{{ $candidate->emergency_contact_1_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Emergency Contact 1 Phone #</label>
          <span class="cd-view value {{ $candidate->emergency_contact_1_phone ? '' : 'empty' }}">{{ $candidate->emergency_contact_1_phone ?: 'None' }}</span>
          <input class="cd-edit" data-field="emergency_contact_1_phone" type="tel" value="{{ $candidate->emergency_contact_1_phone }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Emergency # 2 Name</label>
          <span class="cd-view value {{ $candidate->emergency_contact_2_name ? '' : 'empty' }}">{{ $candidate->emergency_contact_2_name ?: 'None' }}</span>
          <input class="cd-edit" data-field="emergency_contact_2_name" type="text" value="{{ $candidate->emergency_contact_2_name }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Emergency Contact 2 Phone #</label>
          <span class="cd-view value {{ $candidate->emergency_contact_2_phone ? '' : 'empty' }}">{{ $candidate->emergency_contact_2_phone ?: 'None' }}</span>
          <input class="cd-edit" data-field="emergency_contact_2_phone" type="tel" value="{{ $candidate->emergency_contact_2_phone }}" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Training and Development ───────── --}}
    <div class="cd-panel" data-tab-panel="training">
      <h3>Training and Development</h3>
      <div class="cd-grid">
        <div class="cd-field">
          <label>Recipient Rights Training</label>
          @if ($candidate->recipient_rights_training_path)
            <a class="cd-view value" href="/storage/{{ $candidate->recipient_rights_training_path }}" target="_blank" style="color:var(--accent)">📎 {{ $candidate->recipient_rights_training_name ?: 'Document' }}</a>
          @else
            <span class="cd-view value empty">None</span>
          @endif
          <input class="cd-edit" type="file" data-upload-field="recipient_rights_training" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none">
        </div>
        <div class="cd-field">
          <label>Recipient Rights Training Expiration</label>
          <span class="cd-view value {{ $candidate->recipient_rights_training_expires_at ? '' : 'empty' }}">{{ $candidate->recipient_rights_training_expires_at?->format('M j, Y') ?? 'None' }}</span>
          <input class="cd-edit" type="date" data-field="recipient_rights_training_expires_at" value="{{ optional($candidate->recipient_rights_training_expires_at)->format('Y-m-d') }}" style="display:none">
        </div>
        <div class="cd-field">
          <label>Handbook</label>
          <span class="cd-view value {{ $candidate->handbook ? '' : 'empty' }}">{{ $candidate->handbook ?: 'None' }}</span>
          <select class="cd-edit" data-field="handbook" style="display:none">
            <option value="">Select…</option>
            @foreach (['Not Sent','Sent','Acknowledged'] as $opt)
              <option value="{{ $opt }}" @if($candidate->handbook===$opt) selected @endif>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="cd-field">
          <label>Annual CEUs</label>
          @if ($candidate->annual_ceus_path)
            <a class="cd-view value" href="/storage/{{ $candidate->annual_ceus_path }}" target="_blank" style="color:var(--accent)">📎 {{ $candidate->annual_ceus_name ?: 'Document' }}</a>
          @else
            <span class="cd-view value empty">None</span>
          @endif
          <input class="cd-edit" type="file" data-upload-field="annual_ceus" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display:none">
        </div>
      </div>
    </div>

    {{-- ───────── Financial and Payroll Information ───────── --}}
    <div class="cd-panel" data-tab-panel="financial">
      <h3>Financial and Payroll Information</h3>
      <div style="min-height:60px"></div>
    </div>

    {{-- ───────── Post-Offer Documents ───────── --}}
    <div class="cd-panel" data-tab-panel="post-offer">
      <h3>Post-Offer Documents</h3>
      <div style="min-height:60px"></div>
    </div>

    {{-- ───────── DWC Trainings ───────── --}}
    @php
      $dwcTrainings = [
        'abuse_neglect'          => 'Abuse & Neglect',
        'anti_harassment'        => 'Anti-Harassment & Non-Discrimination',
        'cultural_competence'    => 'Cultural Competence/Diversity',
        'emergency_preparedness' => 'Emergency Preparedness',
        'grievances'             => 'Grievances, Appeals and State Fair Hearings',
        'hipaa_basics'           => 'HIPAA (Basics)',
        'sex_trafficking'        => 'Human Sex Trafficking',
        'infection_prevention'   => 'Infection Prevention and Control Practices',
        'lep'                    => 'Limited English Proficiency (LEP)',
        'medicare_compliance'    => 'Medicare & Medicaid Compliance Training',
        'medicare_fraud'         => 'Medicare Fraud & Abuse',
        'person_centered'        => 'Person-Centered Planning',
        'recipient_rights'       => 'Recipient Rights',
      ];
      $dwcStatusOptions = ['Not Started','In Progress','Completed','Expired'];
    @endphp
    <div class="cd-panel" data-tab-panel="dwc">
      <h3>DWC Trainings</h3>
      <div class="cd-grid">
        <div class="cd-field" style="grid-column:1 / -1">
          <label>DWC Transcript</label>
          <span class="cd-view value {{ $candidate->dwc_transcript ? '' : 'empty' }}">{{ $candidate->dwc_transcript ?: 'None' }}</span>
          <input class="cd-edit" data-field="dwc_transcript" type="text" value="{{ $candidate->dwc_transcript }}" style="display:none">
        </div>
        @foreach ($dwcTrainings as $key => $label)
          @php
            $statusCol  = "dwc_{$key}_status";
            $expiresCol = "dwc_{$key}_expires_at";
            $statusVal  = $candidate->{$statusCol};
            $expiresVal = $candidate->{$expiresCol};
          @endphp
          <div class="cd-field">
            <label>{{ $label }} <span style="color:var(--text3);font-size:11px" title="DWC required training">ⓘ</span></label>
            <span class="cd-view value {{ $statusVal ? '' : 'empty' }}">{{ $statusVal ?: 'None' }}</span>
            <select class="cd-edit" data-field="{{ $statusCol }}" style="display:none">
              <option value="">Select…</option>
              @foreach ($dwcStatusOptions as $opt)
                <option value="{{ $opt }}" @if($statusVal===$opt) selected @endif>{{ $opt }}</option>
              @endforeach
            </select>
          </div>
          <div class="cd-field">
            <label>Expiration: {{ $label }} <span style="color:var(--text3);font-size:11px" title="Training expiration date">ⓘ</span></label>
            <span class="cd-view value {{ $expiresVal ? '' : 'empty' }}">{{ $expiresVal?->format('M j, Y') ?? 'None' }}</span>
            <input class="cd-edit" type="date" data-field="{{ $expiresCol }}" value="{{ optional($expiresVal)->format('Y-m-d') }}" style="display:none">
          </div>
        @endforeach
      </div>
    </div>

    {{-- ───────── Additional ───────── --}}
    <div class="cd-panel" data-tab-panel="additional">
      <h3>Additional</h3>
      <div class="cd-grid">
        <div class="cd-field" style="grid-column:1 / -1">
          <label>Notes</label>
          <span class="cd-view value {{ $candidate->notes ? '' : 'empty' }}" style="white-space:pre-wrap">{{ $candidate->notes ?: 'None' }}</span>
          <textarea class="cd-edit" data-field="notes" rows="6" style="display:none;width:100%;resize:vertical">{{ $candidate->notes }}</textarea>
        </div>
      </div>
    </div>

    {{-- ───────── Job Description Letter (reserved/empty) ───────── --}}
    <div class="cd-panel" data-tab-panel="job-description">
      <h3>Job Description Letter</h3>
      <div style="min-height:60px"></div>
    </div>

    {{-- ───────── Stream ───────── --}}
    <div class="cd-section">
      <h3>Stream</h3>
      <form class="cd-comment" id="cdCommentForm" style="margin-bottom:10px;display:flex;gap:8px;align-items:center" onsubmit="cdAddComment(event)">
        <input type="text" id="cdCommentInput" placeholder="Write your comment here" style="flex:1;border:none;outline:none;background:transparent;color:var(--text);font-size:13px;padding:4px 6px">
        <button type="submit" class="btn btn-primary btn-sm">Post</button>
      </form>
      <div id="cdStream">
        <div class="cd-stream-item">
          <div class="cd-stream-avatar">SY</div>
          <div>
            <div><strong style="color:var(--text)">System</strong> created this staff portal</div>
            <div style="color:var(--text3);font-size:11px;margin-top:2px">{{ $candidate->created_at->format('M j') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ───────── Right column (metadata + activities + tasks) ───────── --}}
  <div class="cd-right">
  <aside class="cd-aside">
    <div class="cd-aside-row">
      <label>Assigned Users</label>
      <div class="value {{ $candidate->assignedTo ? '' : 'empty' }}">{{ $candidate->assignedTo?->full_name ?? 'None' }}</div>
    </div>
    <div class="cd-aside-row">
      <label>Teams</label>
      <div class="value empty">None</div>
    </div>
    <div class="cd-aside-row">
      <label>Collaborators</label>
      <div class="value empty">None</div>
    </div>
    <div class="cd-aside-row">
      <label>Applicant Status</label>
      <div class="value">{{ $candidate->status?->label() ?? 'None' }}</div>
    </div>
    <div class="cd-aside-row">
      <label>Background Check Expiration Date</label>
      <div class="value cd-view {{ $candidate->background_check_expires_at ? '' : 'empty' }}">{{ $candidate->background_check_expires_at?->format('M j, Y') ?? 'None' }}</div>
      <input class="cd-edit" type="date" data-field="background_check_expires_at" value="{{ optional($candidate->background_check_expires_at)->format('Y-m-d') }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>Clinical Licenses Expiration Date</label>
      <div class="value cd-view {{ $candidate->clinical_license_expires_at ? '' : 'empty' }}">{{ $candidate->clinical_license_expires_at?->format('M j, Y') ?? 'None' }}</div>
      <input class="cd-edit" type="date" data-field="clinical_license_expires_at" value="{{ optional($candidate->clinical_license_expires_at)->format('Y-m-d') }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>CPR Certification Expiration</label>
      <div class="value cd-view {{ $candidate->cpr_certification_expires_at ? '' : 'empty' }}">{{ $candidate->cpr_certification_expires_at?->format('M j, Y') ?? 'None' }}</div>
      <input class="cd-edit" type="date" data-field="cpr_certification_expires_at" value="{{ optional($candidate->cpr_certification_expires_at)->format('Y-m-d') }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>TB Expiration</label>
      <div class="value cd-view {{ $candidate->tb_expires_at ? '' : 'empty' }}">{{ $candidate->tb_expires_at?->format('M j, Y') ?? 'None' }}</div>
      <input class="cd-edit" type="date" data-field="tb_expires_at" value="{{ optional($candidate->tb_expires_at)->format('Y-m-d') }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>PGL Insurance Expiration</label>
      <div class="value cd-view {{ $candidate->pgl_insurance_expires_at ? '' : 'empty' }}">{{ $candidate->pgl_insurance_expires_at?->format('M j, Y') ?? 'None' }}</div>
      <input class="cd-edit" type="date" data-field="pgl_insurance_expires_at" value="{{ optional($candidate->pgl_insurance_expires_at)->format('Y-m-d') }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>CMHP Hours (Current Year)</label>
      <div class="value cd-view {{ $candidate->cmhp_hours_current_year !== null ? '' : 'empty' }}">{{ $candidate->cmhp_hours_current_year !== null ? rtrim(rtrim(number_format((float)$candidate->cmhp_hours_current_year, 2, '.', ''), '0'), '.') : 'None' }}</div>
      <input class="cd-edit" type="number" step="0.25" min="0" data-field="cmhp_hours_current_year" value="{{ $candidate->cmhp_hours_current_year }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>DWC Training Progress</label>
      <div class="value cd-view">{{ (int) $candidate->dwc_training_progress }}</div>
      <input class="cd-edit" type="number" min="0" max="100" data-field="dwc_training_progress" value="{{ (int) $candidate->dwc_training_progress }}" style="display:none">
    </div>
    <div class="cd-aside-row">
      <label>Created</label>
      <div class="value">{{ $candidate->created_at->format('M d h:i a') }} · System</div>
    </div>
    @if ($candidate->updated_at && $candidate->updated_at->ne($candidate->created_at))
      <div class="cd-aside-row">
        <label>Modified</label>
        @php
          $modifiedAt = $candidate->updated_at->isToday()
              ? 'Today '.$candidate->updated_at->format('h:i a')
              : $candidate->updated_at->format('M d h:i a');
          $modifier = $candidate->lastModifiedBy
              ? trim($candidate->lastModifiedBy->first_name.' '.$candidate->lastModifiedBy->last_name)
              : 'System';
        @endphp
        <div class="value">{{ $modifiedAt }} · {{ $modifier }}</div>
      </div>
    @endif
  </aside>

  {{-- ───────── Activities ───────── --}}
  <div class="cd-side-card">
    <div class="cd-side-head">
      <h4>Activities</h4>
      <div class="cd-side-icons">
        <button type="button" title="Log Email" onclick="cdAddActivity('email')" aria-label="Log Email">&#9993;</button>
        <button type="button" title="Log Meeting" onclick="cdAddActivity('meeting')" aria-label="Log Meeting">&#128197;</button>
        <button type="button" title="Log Phone Call" onclick="cdAddActivity('phone')" aria-label="Log Phone Call">&#9990;</button>
        <button type="button" title="Log Note" onclick="cdAddActivity('note')" aria-label="Log Note">&hellip;</button>
      </div>
    </div>
    <div id="cdActivitiesList" class="cd-side-list">
      <div class="cd-side-empty">No Data</div>
    </div>
  </div>

  {{-- ───────── Tasks ───────── --}}
  <div class="cd-side-card">
    <div class="cd-side-head">
      <h4>Tasks</h4>
      <div class="cd-side-icons">
        <button type="button" title="Add Task" onclick="cdAddTaskPrompt()" aria-label="Add Task">&#43;</button>
        <button type="button" title="More" aria-label="More">&hellip;</button>
      </div>
    </div>
    <div id="cdTasksList" class="cd-side-list">
      <div class="cd-side-empty">No Data</div>
    </div>
  </div>
  </div>
</div>

@push('scripts')
<script>
var CD_CANDIDATE_ID = @json($candidate->id);
var _cdEditing = false;

/* ── Tabs ── */
document.querySelectorAll('#cdTabs .cd-tab').forEach(function(btn){
    btn.addEventListener('click', function(){
        document.querySelectorAll('#cdTabs .cd-tab').forEach(function(b){ b.classList.toggle('active', b===btn); });
        var key = btn.dataset.tab;
        document.querySelectorAll('.cd-panel').forEach(function(p){
            p.classList.toggle('active', p.dataset.tabPanel === key);
        });
    });
});

/* ── Hiring description show-more ── */
function cdToggleDesc(){
    var d = document.getElementById('cdDesc');
    var f = document.getElementById('cdDescFade');
    var m = document.getElementById('cdMore');
    var open = d.classList.toggle('open');
    if(open){ f.style.display='none'; m.textContent='▲ See less'; }
    else    { f.style.display=''; m.textContent='▼ See more'; }
}

/* ── Edit toggle (per-candidate fields + sidebar) ── */
function cdToggleEdit(){
    _cdEditing = !_cdEditing;
    document.getElementById('cdEditLabel').textContent = _cdEditing ? 'Save' : 'Edit';

    document.querySelectorAll('.cd-view').forEach(function(el){ el.style.display = _cdEditing ? 'none' : ''; });
    document.querySelectorAll('.cd-edit').forEach(function(el){ el.style.display = _cdEditing ? '' : 'none'; });
    document.querySelectorAll('input[data-multi-field], input[data-bool-field]').forEach(function(el){ el.disabled = !_cdEditing; });
    document.querySelectorAll('.cd-edit-only').forEach(function(el){ el.style.display = _cdEditing ? '' : 'none'; });

    if(!_cdEditing) cdSaveAll();
}

async function cdSaveAll(){
    var payload = {};
    document.querySelectorAll('.cd-edit[data-field]').forEach(function(el){
        var name = el.dataset.field;
        var v;
        if(el.type === 'number'){
            v = el.value === '' ? null : Number(el.value);
        } else if(el.type === 'date'){
            v = el.value || null;
        } else {
            v = (el.value || '').trim();
            if(v === '') v = null;
        }
        payload[name] = v;
    });

    // multi-checkbox fields (ideal_schedule, days_available, i9_verification, etc.)
    var multiFields = {};
    document.querySelectorAll('input[data-multi-field]').forEach(function(el){
        var name = el.dataset.multiField;
        if(!multiFields[name]) multiFields[name] = [];
        if(el.checked) multiFields[name].push(el.value);
    });
    Object.assign(payload, multiFields);

    // single-boolean checkbox fields (acknowledgement_handbook)
    document.querySelectorAll('input[data-bool-field]').forEach(function(el){
        payload[el.dataset.boolField] = !!el.checked;
    });

    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID, {method:'PATCH', body:JSON.stringify(payload)});
    if(!r) return;
    if(!r.ok){
        var e = await r.json().catch(function(){ return {}; });
        toast(e.message || 'Save failed','error');
        return;
    }
    toast('✓ Saved');

    // File uploads for Training tab (and any future data-upload-field inputs)
    var uploads = document.querySelectorAll('input[type=file][data-upload-field]');
    for (var i = 0; i < uploads.length; i++) {
        var fInput = uploads[i];
        if (!fInput.files || !fInput.files.length) continue;
        var fd = new FormData();
        fd.append('field', fInput.dataset.uploadField);
        fd.append('file',  fInput.files[0]);
        var ur = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/upload', { method:'POST', body: fd });
        if (!ur || !ur.ok) { toast('Upload failed for '+fInput.dataset.uploadField,'error'); continue; }
        var info = await ur.json();
        var viewEl = fInput.parentElement.querySelector('.cd-view');
        if (viewEl) {
            viewEl.outerHTML = '<a class="cd-view value" href="'+info.url+'" target="_blank" style="color:var(--accent)">📎 '+info.name+'</a>';
        }
        fInput.value = '';
        toast('✓ Uploaded '+info.name);
    }

    // Refresh `.cd-view` text from the edited input value so the user sees changes immediately
    document.querySelectorAll('.cd-edit[data-field]').forEach(function(el){
        var view = el.parentElement.querySelector('.cd-view');
        if(!view) return;
        var v = el.value;
        if(el.type === 'date' && v){
            try {
                var d = new Date(v + 'T00:00:00');
                view.textContent = d.toLocaleDateString('en-US', {year:'numeric', month:'short', day:'numeric'});
            } catch(_){ view.textContent = v; }
        } else if(v && v !== ''){
            view.textContent = v;
            view.classList.remove('empty');
        } else {
            view.textContent = 'None';
            view.classList.add('empty');
        }
    });
}

async function cdSaveDescription(){
    var val = document.getElementById('cdPositionDescription').value;
    var r = await apiFetch('/api/settings', {method:'PUT', body:JSON.stringify({hiring_position_description: val})});
    if(!r) return;
    if(!r.ok){ toast('Failed to save description','error'); return; }
    document.getElementById('cdDesc').textContent = val;
    toast('✓ Position description saved');
}

/* Auto-show "See more" only when content overflows */
document.addEventListener('DOMContentLoaded', function(){
    var d = document.getElementById('cdDesc');
    var m = document.getElementById('cdMore');
    var f = document.getElementById('cdDescFade');
    if(d && d.scrollHeight <= d.clientHeight + 4){
        m.style.display = 'none';
        f.style.display = 'none';
    }
    cdLoadComments();
    cdLoadActivities();
    cdLoadTasks();
});

/* ── Stream comments ─────────────────────────────────────── */
function _cdFmtDate(iso){
    if(!iso) return '';
    var d = new Date(iso);
    if(isNaN(d.getTime())) return iso;
    var now = new Date();
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var s = months[d.getMonth()]+' '+d.getDate();
    if(d.getFullYear() !== now.getFullYear()) s += ', '+d.getFullYear();
    return s;
}

function _cdInitials(first, last){
    return ((first||'?').charAt(0) + (last||'').charAt(0)).toUpperCase();
}

function _cdRenderComment(c){
    var who = c.user ? (c.user.first_name+' '+c.user.last_name) : 'System';
    var initials = c.user ? _cdInitials(c.user.first_name, c.user.last_name) : 'SY';
    var body = (c.description || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>');
    return '<div class="cd-stream-item">'
        +'<div class="cd-stream-avatar">'+initials+'</div>'
        +'<div style="flex:1">'
            +'<div><strong style="color:var(--text)">'+who+'</strong> commented</div>'
            +'<div style="margin-top:4px;color:var(--text);font-size:13px;line-height:1.5">'+body+'</div>'
            +'<div style="color:var(--text3);font-size:11px;margin-top:4px">'+_cdFmtDate(c.created_at)+'</div>'
        +'</div>'
    +'</div>';
}

async function cdLoadComments(){
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/comments');
    if(!r || !r.ok) return;
    var comments = await r.json();
    var stream = document.getElementById('cdStream');
    var systemRow = stream.innerHTML; // keep the original "created this staff portal" row
    var html = comments.map(_cdRenderComment).join('');
    stream.innerHTML = html + systemRow;
}

async function cdAddComment(e){
    e.preventDefault();
    var input = document.getElementById('cdCommentInput');
    var body = (input.value || '').trim();
    if(!body) return;

    var btn = document.querySelector('#cdCommentForm button[type=submit]');
    if(btn) btn.disabled = true;

    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/comments', {
        method: 'POST',
        body: JSON.stringify({body: body})
    });

    if(btn) btn.disabled = false;
    if(!r || !r.ok){
        var err = r ? await r.json().catch(function(){return {};}) : {};
        toast(err.message || 'Failed to post comment','error');
        return;
    }
    var comment = await r.json();
    input.value = '';
    var stream = document.getElementById('cdStream');
    stream.insertAdjacentHTML('afterbegin', _cdRenderComment(comment));
    toast('✓ Comment posted');
}

/* ── Sidebar: Activities ────────────────────────────────── */
var CD_ACTIVITY_ICONS = {email:'✉', meeting:'📅', phone:'☎', note:'✎', status_change:'↻', interview_scheduled:'📅', offer_sent:'📝'};
var CD_ACTIVITY_LABELS = {email:'Email', meeting:'Meeting', phone:'Phone Call', note:'Note', status_change:'Status changed', interview_scheduled:'Interview scheduled', offer_sent:'Offer sent'};

function _cdEscape(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function _cdRenderActivity(a){
    var icon = CD_ACTIVITY_ICONS[a.action] || '•';
    var label = CD_ACTIVITY_LABELS[a.action] || a.action;
    var who = a.user ? (a.user.first_name+' '+(a.user.last_name||'')).trim() : 'System';
    var body = _cdEscape(a.description || '').replace(/\n/g,'<br>');
    return '<div class="cd-side-item">'
        +'<div class="cd-side-icon">'+icon+'</div>'
        +'<div style="flex:1;min-width:0">'
            +'<div><strong style="color:var(--text)">'+_cdEscape(label)+'</strong></div>'
            +(body ? '<div style="color:var(--text2);margin-top:2px;word-wrap:break-word">'+body+'</div>' : '')
            +'<div class="cd-side-meta">'+_cdFmtDate(a.created_at)+' · '+_cdEscape(who)+'</div>'
        +'</div>'
    +'</div>';
}

async function cdLoadActivities(){
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/activities');
    var list = document.getElementById('cdActivitiesList');
    if(!r || !r.ok){ list.innerHTML = '<div class="cd-side-empty">No Data</div>'; return; }
    var rows = await r.json();
    if(!rows.length){ list.innerHTML = '<div class="cd-side-empty">No Data</div>'; return; }
    list.innerHTML = rows.map(_cdRenderActivity).join('');
}

async function cdAddActivity(type){
    var label = CD_ACTIVITY_LABELS[type] || type;
    var description = prompt('Log '+label+' — description:');
    if(description === null) return;
    description = description.trim();
    if(!description){ toast('Description is required','error'); return; }

    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/activities', {
        method:'POST',
        body: JSON.stringify({type: type, description: description})
    });
    if(!r || !r.ok){
        var err = r ? await r.json().catch(function(){return {};}) : {};
        toast(err.message || 'Failed to log activity','error');
        return;
    }
    var row = await r.json();
    var list = document.getElementById('cdActivitiesList');
    if(list.querySelector('.cd-side-empty')) list.innerHTML = '';
    list.insertAdjacentHTML('afterbegin', _cdRenderActivity(row));
    toast('✓ '+label+' logged');
}

/* ── Sidebar: Tasks ─────────────────────────────────────── */
function _cdRenderTask(t){
    var done = t.is_completed ? 'done' : '';
    var checked = t.is_completed ? 'checked' : '';
    return '<div class="cd-task-row '+done+'" data-task-id="'+t.id+'">'
        +'<input type="checkbox" '+checked+' onchange="cdToggleTask('+t.id+', this)">'
        +'<label style="flex:1;cursor:pointer">'+_cdEscape(t.task_name)+'</label>'
        +'<button type="button" class="cd-task-delete" title="Remove" onclick="cdDeleteTask('+t.id+')">&times;</button>'
    +'</div>';
}

async function cdLoadTasks(){
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/tasks');
    var list = document.getElementById('cdTasksList');
    if(!r || !r.ok){ list.innerHTML = '<div class="cd-side-empty">No Data</div>'; return; }
    var rows = await r.json();
    if(!rows.length){ list.innerHTML = '<div class="cd-side-empty">No Data</div>'; return; }
    list.innerHTML = rows.map(_cdRenderTask).join('');
}

async function cdAddTaskPrompt(){
    var name = prompt('New task:');
    if(name === null) return;
    name = name.trim();
    if(!name){ toast('Task name is required','error'); return; }

    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/tasks', {
        method:'POST',
        body: JSON.stringify({task_name: name})
    });
    if(!r || !r.ok){
        var err = r ? await r.json().catch(function(){return {};}) : {};
        toast(err.message || 'Failed to add task','error');
        return;
    }
    var task = await r.json();
    var list = document.getElementById('cdTasksList');
    if(list.querySelector('.cd-side-empty')) list.innerHTML = '';
    list.insertAdjacentHTML('beforeend', _cdRenderTask(task));
    toast('✓ Task added');
}

async function cdToggleTask(id, cb){
    var r = await apiFetch('/api/onboarding-tasks/'+id+'/toggle', {method:'PATCH'});
    if(!r || !r.ok){ toast('Failed to toggle task','error'); cb.checked = !cb.checked; return; }
    var row = document.querySelector('.cd-task-row[data-task-id="'+id+'"]');
    if(row) row.classList.toggle('done', cb.checked);
}

async function cdDeleteTask(id){
    if(!confirm('Remove this task?')) return;
    var r = await apiFetch('/api/onboarding-tasks/'+id, {method:'DELETE'});
    if(!r || !r.ok){ toast('Failed to remove task','error'); return; }
    var row = document.querySelector('.cd-task-row[data-task-id="'+id+'"]');
    if(row) row.remove();
    var list = document.getElementById('cdTasksList');
    if(!list.children.length) list.innerHTML = '<div class="cd-side-empty">No Data</div>';
}

/* ── 3-dot menu ─────────────────────────────────────────── */
function cdToggleMore(e){
    e && e.stopPropagation();
    var m = document.getElementById('cdMoreMenu');
    m.style.display = (m.style.display === 'block') ? 'none' : 'block';
}
document.addEventListener('click', function(e){
    var m = document.getElementById('cdMoreMenu');
    if(!m) return;
    if(!e.target.closest('#cdMoreMenu') && !e.target.closest('button[title="More"]')){
        m.style.display = 'none';
    }
});

function _cdOpenMoreModal(title, html){
    document.getElementById('cdMoreModalTitle').textContent = title;
    document.getElementById('cdMoreModalBody').innerHTML = html;
    openModal('cdMoreModal');
    cdToggleMore();
}

async function cdRemoveCandidate(){
    cdToggleMore();
    if(!confirm('Remove this candidate? They will be soft-deleted and removed from active lists.')) return;
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID, {method:'DELETE'});
    if(!r || !r.ok){ toast('Failed to remove candidate','error'); return; }
    toast('✓ Candidate removed');
    window.location.href = '{{ route("hris.staff-portals") }}';
}

async function cdDuplicateCandidate(){
    cdToggleMore();
    if(!confirm('Create a duplicate of this candidate?')) return;
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/duplicate', {method:'POST'});
    if(!r || !r.ok){ toast('Failed to duplicate candidate','error'); return; }
    var copy = await r.json();
    toast('✓ Duplicated');
    window.location.href = '/hris/staff-portals/'+copy.id;
}

async function cdViewPersonalData(){
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID);
    if(!r || !r.ok){ toast('Failed to load personal data','error'); return; }
    var data = await r.json();
    var c = data.candidate || data;
    var pairs = [
        ['First Name', c.first_name],
        ['Last Name',  c.last_name],
        ['Email',      c.email],
        ['Phone',      c.phone],
        ['Street Address', c.street_address],
        ['City',       c.city],
        ['State',      c.state],
        ['Postal Code', c.postal_code],
        ['LinkedIn',   c.linkedin_url],
        ['Years Experience', c.years_experience],
        ['Education Level',  c.education_level],
        ['Desired Pay', c.desired_pay],
        ['Earliest Start Date', c.earliest_start_date],
        ['Authorized to Work',  c.is_authorized_to_work === null ? null : (c.is_authorized_to_work ? 'Yes' : 'No')],
    ];
    var html = '<dl style="margin:0">' + pairs.map(function(p){
        var v = (p[1] === null || p[1] === '' || p[1] === undefined) ? '<span style="color:var(--text3)">None</span>' : esc(String(p[1]));
        return '<dt>'+esc(p[0])+'</dt><dd>'+v+'</dd>';
    }).join('') + '</dl>';
    _cdOpenMoreModal('Personal Data — '+esc(c.first_name+' '+c.last_name), html);
}

function cdViewFollowers(){
    var html = '<p style="color:var(--text3);font-size:13px;margin:0">No followers yet.</p>'
      + '<p style="color:var(--text3);font-size:12px;margin-top:8px">A follow/share workflow is planned — currently nobody is following this candidate.</p>';
    _cdOpenMoreModal('Followers', html);
}

async function cdViewAuditLog(){
    cdToggleMore();
    _cdOpenMoreModal('Audit Log', '<p style="color:var(--text3);font-size:13px;margin:0">Loading…</p>');
    var r = await apiFetch('/api/candidates/'+CD_CANDIDATE_ID+'/audit-log');
    if(!r || !r.ok){ document.getElementById('cdMoreModalBody').innerHTML = '<p style="color:var(--red)">Failed to load audit log.</p>'; return; }
    var logs = await r.json();
    if(!logs.length){
        document.getElementById('cdMoreModalBody').innerHTML = '<p style="color:var(--text3);font-size:13px;margin:0">No activity yet.</p>';
        return;
    }
    var html = logs.map(function(l){
        var who = l.user ? esc(l.user.first_name+' '+l.user.last_name) : 'System';
        var act = esc(l.action || 'event').replace(/_/g, ' ');
        var desc = l.description ? '<div style="margin-top:3px">'+esc(l.description)+'</div>' : '';
        return '<div class="cd-audit-row">'
            +'<div><strong>'+who+'</strong> · '+act+'</div>'
            + desc
            +'<div class="cd-audit-meta">'+_cdFmtDate(l.created_at)+'</div>'
        +'</div>';
    }).join('');
    document.getElementById('cdMoreModalBody').innerHTML = html;
}

async function cdViewUserAccess(){
    cdToggleMore();
    _cdOpenMoreModal('User Access', '<p style="color:var(--text3);font-size:13px;margin:0">Loading…</p>');
    var r = await apiFetch('/api/settings/hr-team');
    if(!r || !r.ok){
        document.getElementById('cdMoreModalBody').innerHTML = '<p style="color:var(--text3);font-size:13px;margin:0">All HR users have access to this candidate.</p>';
        return;
    }
    var users = await r.json();
    if(!users.length){
        document.getElementById('cdMoreModalBody').innerHTML = '<p style="color:var(--text3);font-size:13px;margin:0">No HR users found.</p>';
        return;
    }
    var html = '<p style="color:var(--text3);font-size:12px;margin:0 0 10px">The following HR users can view and edit this staff portal.</p>'
      + '<ul style="margin:0;padding-left:18px">'
      + users.map(function(u){
          return '<li style="margin-bottom:4px">'+esc((u.first_name||'')+' '+(u.last_name||''))+(u.email?' <span style="color:var(--text3);font-size:11px">— '+esc(u.email)+'</span>':'')+'</li>';
        }).join('')
      + '</ul>';
    document.getElementById('cdMoreModalBody').innerHTML = html;
}
</script>
@endpush
@endsection
