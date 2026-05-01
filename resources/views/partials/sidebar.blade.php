<style>
   a{
        text-decoration:none !important;
   }
   .sidebar-logo{ padding:12px 16px; display:flex; align-items:center; }
   .sidebar-logo img{ max-width:180px; height:40px; display:block; }
</style>
<aside class="sidebar">
  <div class="sidebar-logo">
    <a href="{{ route('hris.dashboard') }}" aria-label="Home">
      <img src="https://login.mccrorycenter.com/?entryPoint=LogoImage&id=68136eae0e1afd0b7" alt="McCrory Center logo">
    </a>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section"><div class="nav-section-title">Overview</div>
      <a href="{{ route('hris.dashboard') }}" class="nav-item {{ request()->routeIs('hris.dashboard') ? 'active' : '' }}" data-page="dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">Recruitment Sequence</div>
      <a href="{{ route('hris.intake') }}" class="nav-item {{ request()->routeIs('hris.intake') ? 'active' : '' }}" data-page="intake"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Resume Intake</a>
      <a href="{{ route('hris.new-candidates') }}" class="nav-item {{ request()->routeIs('hris.new-candidates') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>New Candidates</a>
      <a href="{{ route('hris.review-queue') }}" class="nav-item {{ request()->routeIs('hris.review-queue') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Review Queue</a>
      <a href="{{ route('hris.review') }}" class="nav-item {{ request()->routeIs('hris.review') ? 'active' : '' }}" data-page="review"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Invites Sent <span class="nav-badge" id="reviewBadge">0</span></a>
      <a href="{{ route('hris.interviews') }}" class="nav-item {{ request()->routeIs('hris.interviews') ? 'active' : '' }}" data-page="interviews"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Scheduled Candidates</a>
      <a href="{{ route('hris.pipeline') }}" class="nav-item {{ request()->routeIs('hris.pipeline') ? 'active' : '' }}" data-page="pipeline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>Pipeline</a>
      <a href="{{ route('hris.calendar') }}" class="nav-item {{ request()->routeIs('hris.calendar') ? 'active' : '' }}" data-page="calendar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="14" stroke-width="3"/><line x1="12" y1="14" x2="12" y2="14" stroke-width="3"/><line x1="16" y1="14" x2="16" y2="14" stroke-width="3"/><line x1="8" y1="18" x2="8" y2="18" stroke-width="3"/><line x1="12" y1="18" x2="12" y2="18" stroke-width="3"/></svg>Calendar</a>
      <a href="{{ route('hris.screening') }}" class="nav-item {{ request()->routeIs('hris.screening') ? 'active' : '' }}" data-page="screening"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>Pre-Screening</a>
      <a href="{{ route('hris.workflow.pre_interview_questions') }}" class="nav-item {{ request()->routeIs('hris.workflow.pre_interview_questions') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.82 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>Pre-interview Questions</a>
      <a href="{{ route('hris.workflow.verifications_review') }}" class="nav-item {{ request()->routeIs('hris.workflow.verifications_review') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>Verifications and Review</a>
      <a href="{{ route('hris.offers') }}" class="nav-item {{ request()->routeIs('hris.offers') ? 'active' : '' }}" data-page="offers"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>Offer Letter</a>
      <a href="{{ route('hris.onboarding') }}" class="nav-item {{ request()->routeIs('hris.onboarding') ? 'active' : '' }}" data-page="onboarding"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17,11 19,13 23,9"/></svg>Pre-Onboarding Documents</a>
      <a href="{{ route('hris.workflow.compliance_agreements') }}" class="nav-item {{ request()->routeIs('hris.workflow.compliance_agreements') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>Compliance Agreements</a>
      <a href="{{ route('hris.workflow.clinical_staff_document') }}" class="nav-item {{ request()->routeIs('hris.workflow.clinical_staff_document') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>Clinical Staff Document</a>
      <a href="{{ route('hris.workflow.emergency_contact') }}" class="nav-item {{ request()->routeIs('hris.workflow.emergency_contact') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92V19a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.11 3.18 2 2 0 0 1 4.11 1h2.09a2 2 0 0 1 2 1.72c.12.9.32 1.78.59 2.64a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 8 8l1.44-1.25a2 2 0 0 1 2.11-.45c.86.27 1.74.47 2.64.59A2 2 0 0 1 22 16.92z"/></svg>Emergency Contact</a>
      <a href="{{ route('hris.workflow.training_development') }}" class="nav-item {{ request()->routeIs('hris.workflow.training_development') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h20v14H2z"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>Training and Development</a>
      <a href="{{ route('hris.workflow.financial_payroll_information') }}" class="nav-item {{ request()->routeIs('hris.workflow.financial_payroll_information') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>Financial and Payroll Information</a>
      <a href="{{ route('hris.workflow.post_offer_documents') }}" class="nav-item {{ request()->routeIs('hris.workflow.post_offer_documents') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Post-offer Documents</a>
      <a href="{{ route('hris.workflow.dwc_training') }}" class="nav-item {{ request()->routeIs('hris.workflow.dwc_training') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M12 4h9"/><path d="M4 9h16"/><path d="M4 15h16"/></svg>DWC Training</a>
      <a href="{{ route('hris.workflow.additional') }}" class="nav-item {{ request()->routeIs('hris.workflow.additional') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Additional</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">People</div>
      <a href="{{ route('hris.employees') }}" class="nav-item {{ request()->routeIs('hris.employees') ? 'active' : '' }}" data-page="employees"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Employees</a>
      <a href="{{ route('portal') }}" class="nav-item {{ request()->routeIs('portal') ? 'active' : '' }}" data-page="portal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>Employee Portal</a>
      <a href="{{ route('hris.timeoff') }}" class="nav-item {{ request()->routeIs('hris.timeoff') ? 'active' : '' }}" data-page="timeoff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>Time Off</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">System</div>
      <a href="{{ route('hris.automations') }}" class="nav-item {{ request()->routeIs('hris.automations') ? 'active' : '' }}" data-page="automations"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13,2 3,14 12,14 11,22 21,10 12,10"/></svg>Automations</a>
      <a href="{{ route('hris.settings') }}" class="nav-item {{ request()->routeIs('hris.settings') ? 'active' : '' }}" data-page="settings"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Settings</a>
    </div>
  </nav>
  <!-- Interviews Calendar -->
  <div class="sidebar-cal" id="sidebarCal">
    <div class="sidebar-cal-hdr" onclick="toggleSidebarCal()">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      Interviews
      <svg class="cal-chevron" id="sidebarCalChevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6,9 12,15 18,9"/></svg>
    </div>
    <div class="sidebar-cal-body" id="sidebarCalBody">
      <div class="cal-nav">
        <button class="cal-nav-btn" onclick="calPrev()">&#8249;</button>
        <span class="cal-nav-lbl" id="calMonthLabel"></span>
        <button class="cal-nav-btn" onclick="calNext()">&#8250;</button>
      </div>
      <div class="cal-grid" id="calGrid"></div>
      <div id="calDayList"></div>
    </div>
  </div>

  <div class="sidebar-user">
    <div class="avatar" id="sidebarUserAvatar" style="background:linear-gradient(135deg,var(--accent),var(--accent2))">HR</div>
    <div>
        <div class="name" id="sidebarUserName">Loading...</div>
      <div class="role" id="sidebarUserRole">HR Staff</div>
    </div>
    <button onclick="logout()" title="Sign out" style="margin-left:auto;color:var(--text3);width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='none'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </button>
  </div>
</aside>
