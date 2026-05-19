<header class="topbar">
  <a href="{{ route('hris.dashboard') }}" class="topbar-logo" aria-label="Home">
    <img src="https://login.mccrorycenter.com/?entryPoint=LogoImage&id=68136eae0e1afd0b7" alt="McCrory Center logo">
  </a>

  <nav class="topbar-nav">
    <a href="{{ route('hris.dashboard') }}" class="topnav-item {{ request()->routeIs('hris.dashboard') ? 'active' : '' }}" data-page="dashboard">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      <span>Dashboard</span>
    </a>
    <a href="{{ route('hris.staff-portals') }}" class="topnav-item {{ request()->routeIs('hris.staff-portals') ? 'active' : '' }}" data-page="staff-portals">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="9" y1="20" x2="9" y2="10"/></svg>
      <span>Staff Portals</span>
    </a>
    <a href="{{ route('hris.employees') }}" class="topnav-item {{ request()->routeIs('hris.employees') ? 'active' : '' }}" data-page="employees">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
      <span>Employees</span>
    </a>
    <a href="{{ route('hris.automations') }}" class="topnav-item {{ request()->routeIs('hris.automations') ? 'active' : '' }}" data-page="automations">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13,2 3,14 12,14 11,22 21,10 12,10"/></svg>
      <span>Automations</span>
    </a>
    <a href="{{ route('hris.settings') }}" class="topnav-item {{ request()->routeIs('hris.settings') ? 'active' : '' }}" data-page="settings">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
      <span>Settings</span>
    </a>
  </nav>

  <div class="topbar-right">
    <div class="topbar-actions">
      <button class="icon-btn" id="notifBtn" title="Notifications">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
        <span class="dot"></span>
      </button>
    </div>
    <div class="topbar-user">
      <div class="avatar" id="sidebarUserAvatar" style="background:linear-gradient(135deg,var(--accent),var(--accent2))">HR</div>
      <div class="topbar-user-meta">
        <div class="name" id="sidebarUserName">Loading...</div>
        <div class="role" id="sidebarUserRole">HR Staff</div>
      </div>
      <button onclick="logout()" title="Sign out" class="topbar-logout">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </div>
  </div>
</header>
