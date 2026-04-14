<style>
   a{
        text-decoration:none !important;
   } 
</style>
<aside class="sidebar">
  <div class="sidebar-logo"><div class="icon">TF</div><h1>TalentFlow</h1></div>
  <nav class="sidebar-nav">
    <div class="nav-section"><div class="nav-section-title">Overview</div>
      <a href="<?php echo e(route('hris.dashboard')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.dashboard') ? 'active' : ''); ?>" data-page="dashboard"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>Dashboard</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">Recruitment</div>
      <a href="<?php echo e(route('hris.intake')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.intake') ? 'active' : ''); ?>" data-page="intake"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>Resume Intake</a>
      <a href="<?php echo e(route('hris.review')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.review') ? 'active' : ''); ?>" data-page="review"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>Review Queue <span class="nav-badge" id="reviewBadge">0</span></a>
      <a href="<?php echo e(route('hris.pipeline')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.pipeline') ? 'active' : ''); ?>" data-page="pipeline"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22,12 18,12 15,21 9,3 6,12 2,12"/></svg>Pipeline</a>
      <a href="<?php echo e(route('hris.interviews')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.interviews') ? 'active' : ''); ?>" data-page="interviews"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Interviews</a>
      <a href="<?php echo e(route('hris.screening')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.screening') ? 'active' : ''); ?>" data-page="screening"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22,4 12,14.01 9,11.01"/></svg>Screening & Checks</a>
      <a href="<?php echo e(route('hris.offers')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.offers') ? 'active' : ''); ?>" data-page="offers"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>Offers</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">People</div>
      <a href="<?php echo e(route('hris.onboarding')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.onboarding') ? 'active' : ''); ?>" data-page="onboarding"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><polyline points="17,11 19,13 23,9"/></svg>Onboarding</a>
      <a href="<?php echo e(route('hris.employees')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.employees') ? 'active' : ''); ?>" data-page="employees"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>Users</a>
      <a href="<?php echo e(route('portal')); ?>" class="nav-item <?php echo e(request()->routeIs('portal') ? 'active' : ''); ?>" data-page="portal"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>Employee Portal</a>
      <a href="<?php echo e(route('hris.timeoff')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.timeoff') ? 'active' : ''); ?>" data-page="timeoff"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/></svg>Time Off</a>
    </div>
    <div class="nav-section"><div class="nav-section-title">System</div>
      <a href="<?php echo e(route('hris.automations')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.automations') ? 'active' : ''); ?>" data-page="automations"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13,2 3,14 12,14 11,22 21,10 12,10"/></svg>Automations</a>
      <a href="<?php echo e(route('hris.settings')); ?>" class="nav-item <?php echo e(request()->routeIs('hris.settings') ? 'active' : ''); ?>" data-page="settings"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Settings</a>
    </div>
  </nav>
  <div class="sidebar-user">
    <div class="avatar" id="sidebarUserAvatar" style="background:linear-gradient(135deg,var(--accent),var(--pink))">HR</div>
    <div>
      <div class="name" id="sidebarUserName">Loading…</div>
      <div class="role" id="sidebarUserRole">HR Staff</div>
    </div>
    <button onclick="logout()" title="Sign out" style="margin-left:auto;color:var(--text3);width:28px;height:28px;border-radius:6px;display:flex;align-items:center;justify-content:center" onmouseover="this.style.background='var(--surface2)'" onmouseout="this.style.background='none'">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </button>
  </div>
</aside>
<?php /**PATH F:\laravel projects\hrportal\resources\views\partials\sidebar.blade.php ENDPATH**/ ?>