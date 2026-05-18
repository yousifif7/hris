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
      {{-- <a href="{{ route('hris.intake') }}" class="nav-item {{ request()->routeIs('hris.intake') ? 'active' : '' }}" data-page="intake"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Resume Intake</a> --}}
    </div>
    <div class="nav-section"><div class="nav-section-title">People</div>
      <a href="{{ route('hris.staff-portals') }}" class="nav-item {{ request()->routeIs('hris.staff-portals') ? 'active' : '' }}" data-page="staff-portals"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="20" x2="9" y2="10"/></svg>Staff Portals</a>
      <a href="{{ route('hris.employees') }}" class="nav-item {{ request()->routeIs('hris.employees') ? 'active' : '' }}" data-page="employees"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Employees</a>
      {{-- <a href="{{ route('portal') }}" class="nav-item {{ request()->routeIs('portal') ? 'active' : '' }}" data-page="portal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>Employee Portal</a>
      <a href="{{ route('hris.timeoff') }}" class="nav-item {{ request()->routeIs('hris.timeoff') ? 'active' : '' }}" data-page="timeoff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>Time Off</a>
    --}}
    </div> 
    <div class="nav-section"><div class="nav-section-title">System</div>
      <a href="{{ route('hris.automations') }}" class="nav-item {{ request()->routeIs('hris.automations') ? 'active' : '' }}" data-page="automations"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13,2 3,14 12,14 11,22 21,10 12,10"/></svg>Automations</a>
      <a href="{{ route('hris.settings') }}" class="nav-item {{ request()->routeIs('hris.settings') ? 'active' : '' }}" data-page="settings"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Settings</a>
    </div>
  </nav>
  <!-- Interviews Calendar -->
  {{-- <div class="sidebar-cal" id="sidebarCal">
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
  </div> --}}

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
