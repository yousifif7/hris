<div class="notif-panel" id="notifPanel">
    <div class="header" style="flex-direction:column;align-items:flex-start">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px">
            <div style="display:flex;gap:6px;align-items:center">
                <h3>Notifications</h3>
            </div>
            <div>
                <button onclick="toggleNotif()" style="color:var(--text3);padding:4px 6px;display:flex;align-items:center;justify-content:center;width:32px;height:28px;border-radius:6px">✕</button>
            </div>
        </div>
        <div style="margin-top:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
            <button onclick="toggleNotifSelectMode()" class="btn btn-sm btn-secondary" id="notifSelectBtn" style="font-size:11px">Select</button>
            <button onclick="readAllNotifs()" class="btn btn-sm btn-secondary" style="font-size:11px">Mark all read</button>
            <button onclick="deleteAllNotifs()" class="btn btn-sm btn-danger" style="font-size:11px">Clear all</button>
        </div>
    </div>
    <!-- Bulk action bar (shown in select mode) -->
    <div id="notifBulkBar"
        style="display:none;padding:8px 16px;background:var(--surface2);border-bottom:1px solid var(--border);display:none;align-items:center;gap:8px;flex-wrap:wrap">
        <label style="font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
            <input type="checkbox" id="notifSelectAll" onchange="toggleSelectAllNotifs(this.checked)"
                style="width:auto"> Select all
        </label>
        <span id="notifSelCount" style="font-size:12px;color:var(--text3);flex:1">0 selected</span>
        <button onclick="bulkReadNotifs()" class="btn btn-sm btn-secondary" style="font-size:11px">✓ Mark read</button>
        <button onclick="bulkDeleteNotifs()" class="btn btn-sm btn-danger" style="font-size:11px">🗑 Delete</button>
    </div>
    <div id="notifList">
        <div style="padding:24px;text-align:center;color:var(--text3);font-size:13px">Loading...</div>
    </div>
</div>
