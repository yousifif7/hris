<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','McCrory Center')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
    <link rel="shortcut icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png" type="image/png">
    <link rel="apple-touch-icon" href="https://www.mccrorycenter.com/wp-content/uploads/2025/04/asdasfasf.png">
    @verbatim
    <style>
:root{
    --bg:#f4f5f7;--surface:#ffffff;--surface2:#f0f1f4;--surface3:#e8e9ed;
    --border:#dfe1e6;--border-light:#c1c7d0;
    --text:#172b4d;--text2:#5e6c84;--text3:#97a0af;
    --accent:#5ac6cc;--accent2:#4fbfc7;--accent-glow:rgba(90,198,204,.08);
  --green:#00875a;--green-bg:rgba(0,135,90,.08);
  --yellow:#ff991f;--yellow-bg:rgba(255,153,31,.08);
  --red:#de350b;--red-bg:rgba(222,53,11,.07);
  --blue:#0065ff;--blue-bg:rgba(0,101,255,.07);
  --orange:#ff8b00;--orange-bg:rgba(255,139,0,.07);
  --pink:#e5508c;--pink-bg:rgba(229,80,140,.07);
  --radius:8px;--radius-lg:12px;
  --shadow:0 1px 3px rgba(0,0,0,.08),0 4px 16px rgba(0,0,0,.06);
  --font:'DM Sans',sans-serif;--font-display:'Playfair Display',serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:14px}
body{font-family:var(--font);background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow:hidden}
input,select,textarea,button{font-family:inherit;font-size:inherit}
button{cursor:pointer;border:none;background:none;color:inherit}
input,select,textarea{background:var(--surface);border:1px solid var(--border);color:var(--text);padding:8px 12px;border-radius:var(--radius);outline:none;transition:border .2s}
input:focus,select:focus,textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px var(--accent-glow)}
textarea{resize:vertical;min-height:80px}
select{-webkit-appearance:none;appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%235e6c84' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10z'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px}
::-webkit-scrollbar{width:6px;height:6px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
::-webkit-scrollbar-thumb:hover{background:var(--border-light)}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.animate-in{animation:fadeIn .4s ease both}

/* SIDEBAR */
.sidebar{width:250px;background:var(--surface);border-right:1px solid var(--border);display:flex;flex-direction:column;flex-shrink:0;height:100vh;position:sticky;top:0}
.sidebar-logo{padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px}
.sidebar-logo .icon{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:16px}
.sidebar-logo h1{font-family:var(--font-display);font-size:18px;letter-spacing:-.5px;color:var(--text)}
.sidebar-nav{flex:1;padding:12px;overflow-y:auto}
.nav-section{margin-bottom:16px}
.nav-section-title{font-size:10px;text-transform:uppercase;letter-spacing:1.5px;color:var(--text3);padding:8px 12px;font-weight:600}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius);color:var(--text2);font-weight:500;transition:all .2s;margin-bottom:2px;position:relative;font-size:13px}
.nav-item:hover{background:var(--surface2);color:var(--text)}
.nav-item.active{background:var(--accent-glow);color:var(--accent)}
.nav-item.active::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);width:3px;height:20px;background:var(--accent);border-radius:3px}
.nav-item svg{width:18px;height:18px;flex-shrink:0}
.nav-badge{margin-left:auto;background:var(--accent);color:#fff;font-size:11px;font-weight:600;padding:2px 7px;border-radius:10px}
.sidebar-user{padding:16px;border-top:1px solid var(--border);display:flex;align-items:center;gap:10px}
.sidebar-user .avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--pink));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff}
.sidebar-user .name{font-weight:600;font-size:13px;color:var(--text)}
.sidebar-user .role{font-size:11px;color:var(--text3)}

/* MAIN */
.main{flex:1;display:flex;flex-direction:column;height:100vh;overflow:hidden}
.topbar{display:flex;align-items:center;gap:16px;padding:14px 28px;border-bottom:1px solid var(--border);background:var(--surface);flex-shrink:0}
.topbar h2{font-size:18px;font-weight:700;color:var(--text)}
.topbar .spacer{flex:1}
.search-box{position:relative}
.search-box input{padding-left:36px;width:240px;background:var(--surface2);border-color:transparent}
.search-box input:focus{border-color:var(--accent);background:var(--surface)}
.search-box svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text3);width:16px;height:16px}
.topbar-actions{display:flex;gap:8px}
.icon-btn{width:36px;height:36px;border-radius:var(--radius);display:flex;align-items:center;justify-content:center;transition:background .2s;position:relative}
.icon-btn:hover{background:var(--surface2)}
.icon-btn svg{width:18px;height:18px;color:var(--text2)}
.icon-btn .dot{position:absolute;top:6px;right:6px;width:8px;height:8px;background:var(--red);border-radius:50%;border:2px solid var(--surface)}
.content{flex:1;overflow-y:auto;padding:28px}

/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:var(--radius);font-weight:600;font-size:13px;transition:all .15s}
.btn-primary{background:var(--accent);color:#fff}
.btn-primary:hover{background:#4a3cc5;transform:translateY(-1px);box-shadow:0 2px 8px rgba(91,76,219,.3)}
.btn-secondary{background:var(--surface);border:1px solid var(--border);color:var(--text)}
.btn-secondary:hover{border-color:var(--border-light);background:var(--surface2)}
.btn-sm{padding:5px 12px;font-size:12px}
.btn-danger{background:var(--red-bg);color:var(--red);border:1px solid rgba(222,53,11,.15)}
.btn-danger:hover{background:rgba(222,53,11,.14)}
.btn-success{background:var(--green-bg);color:var(--green);border:1px solid rgba(0,135,90,.15)}
.btn-success:hover{background:rgba(0,135,90,.14)}
.btn-warning{background:var(--yellow-bg);color:#b8860b;border:1px solid rgba(255,153,31,.2)}
.btn-warning:hover{background:rgba(255,153,31,.14)}
.btn-blue{background:var(--blue-bg);color:var(--blue);border:1px solid rgba(0,101,255,.15)}
.btn-blue:hover{background:rgba(0,101,255,.12)}

/* BADGES */
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap}
.badge-needs-review{background:var(--yellow-bg);color:#b8860b}
.badge-invite-sent{background:var(--blue-bg);color:var(--blue)}
.badge-interview{background:var(--accent-glow);color:var(--accent)}
.badge-prescreening{background:var(--orange-bg);color:var(--orange)}
.badge-offer-sent{background:var(--pink-bg);color:var(--pink)}
.badge-offer-accepted{background:var(--green-bg);color:var(--green)}
.badge-rejected{background:var(--red-bg);color:var(--red)}
.badge-queue{background:var(--surface2);color:var(--text3)}
.badge-declined{background:var(--red-bg);color:var(--red)}
.badge-onboarding{background:var(--green-bg);color:var(--green)}
.badge-post-interview{background:var(--accent-glow);color:var(--accent)}
.badge-bg-check{background:var(--orange-bg);color:var(--orange)}

/* STATS */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:14px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px;position:relative;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.stat-card::after{content:'';position:absolute;bottom:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.08}
.stat-card.purple::after{background:var(--accent)}.stat-card.green::after{background:var(--green)}.stat-card.blue::after{background:var(--blue)}.stat-card.orange::after{background:var(--orange)}.stat-card.pink::after{background:var(--pink)}.stat-card.yellow::after{background:var(--yellow)}
.stat-label{font-size:11px;color:var(--text3);margin-bottom:4px;font-weight:500;text-transform:uppercase;letter-spacing:.5px}
.stat-value{font-size:26px;font-weight:700;letter-spacing:-.5px;color:var(--text)}
.stat-change{display:inline-flex;align-items:center;gap:3px;font-size:11px;font-weight:600;margin-top:4px;padding:2px 6px;border-radius:6px}
.stat-change.up{color:var(--green);background:var(--green-bg)}
.stat-change.down{color:var(--red);background:var(--red-bg)}

/* TABLES */
.table-wrap{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.table-header{display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:8px}
.table-header h3{font-size:15px;font-weight:700;color:var(--text)}
.table-filters{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
.table-filters select,.table-filters input{font-size:12px;padding:6px 10px}
table{width:100%;border-collapse:collapse}
th{text-align:left;padding:10px 14px;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--text3);font-weight:600;border-bottom:1px solid var(--border);white-space:nowrap;background:var(--surface2)}
td{padding:11px 14px;border-bottom:1px solid var(--border);font-size:13px}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(91,76,219,.03)}
.candidate-name{font-weight:600;color:var(--text)}
.candidate-sub{font-size:11px;color:var(--text3)}
.actions-cell{display:flex;gap:4px;flex-wrap:wrap}
.actions-cell button{padding:4px 8px;font-size:11px}

/* PIPELINE */
.pipeline-bar{display:flex;gap:2px;margin-bottom:24px;background:var(--surface);border-radius:var(--radius-lg);padding:5px;border:1px solid var(--border);overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.pipeline-stage{flex:1;text-align:center;padding:8px 6px;border-radius:var(--radius);font-size:10px;font-weight:600;color:var(--text3);cursor:pointer;transition:all .2s;min-width:80px}
.pipeline-stage:hover{background:var(--surface2);color:var(--text2)}
.pipeline-stage .num{display:block;font-size:18px;font-weight:700;color:var(--text);margin-bottom:1px}

/* KANBAN */
.kanban{display:flex;gap:12px;overflow-x:auto;padding-bottom:16px;min-height:500px}
.kanban-col{min-width:250px;max-width:250px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius-lg);display:flex;flex-direction:column;max-height:calc(100vh - 180px)}
.kanban-col-header{padding:12px 14px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;flex-shrink:0;background:var(--surface)}
.kanban-col-header .dot{width:10px;height:10px;border-radius:50%}
.kanban-col-header h4{font-size:12px;font-weight:600;flex:1;color:var(--text)}
.kanban-col-header .count{font-size:11px;color:var(--text3);background:var(--surface2);padding:2px 8px;border-radius:10px}
.kanban-cards{flex:1;overflow-y:auto;padding:8px}
.kanban-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:12px;margin-bottom:8px;cursor:pointer;transition:all .2s;box-shadow:0 1px 2px rgba(0,0,0,.04)}
.kanban-card:hover{border-color:var(--accent);transform:translateY(-2px);box-shadow:0 4px 12px rgba(91,76,219,.12)}
.kanban-card .name{font-weight:600;font-size:13px;margin-bottom:3px;color:var(--text)}
.kanban-card .role{font-size:11px;color:var(--text3);margin-bottom:6px}
.kanban-card .meta{display:flex;gap:6px;font-size:11px;color:var(--text3)}

/* MODALS */
.modal-overlay{position:fixed;inset:0;background:rgba(23,43,77,.4);backdrop-filter:blur(4px);z-index:1000;display:none;align-items:center;justify-content:center}
.modal-overlay.open{display:flex}
.modal{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);width:92%;max-width:680px;max-height:88vh;overflow-y:auto;box-shadow:0 8px 40px rgba(0,0,0,.15)}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border)}
.modal-header h3{font-size:16px;font-weight:700;color:var(--text)}
.modal-body{padding:24px}
.modal-footer{padding:14px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px}

/* FORMS */
.form-group{margin-bottom:14px}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--text2);margin-bottom:5px}
.form-group input,.form-group select,.form-group textarea{width:100%}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}

/* TABS */
.tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:1px solid var(--border);overflow-x:auto}
.tab{padding:10px 16px;font-weight:600;font-size:13px;color:var(--text3);border-bottom:2px solid transparent;margin-bottom:-1px;transition:all .2s;white-space:nowrap}
.tab:hover{color:var(--text2)}
.tab.active{color:var(--accent);border-bottom-color:var(--accent)}
.tab-content{display:none}
.tab-content.active{display:block}

/* TIMELINE */
.timeline{position:relative;padding-left:24px}
.timeline::before{content:'';position:absolute;left:8px;top:4px;bottom:4px;width:2px;background:var(--border)}
.timeline-item{position:relative;padding-bottom:18px}
.timeline-item::before{content:'';position:absolute;left:-20px;top:4px;width:12px;height:12px;border-radius:50%;background:var(--accent);border:2px solid var(--surface)}
.timeline-item .time{font-size:11px;color:var(--text3);margin-bottom:2px}
.timeline-item .event{font-size:13px;font-weight:500;color:var(--text)}

/* CHECKLIST */
.checklist-item{display:flex;align-items:center;gap:10px;padding:11px 14px;border:1px solid var(--border);border-radius:var(--radius);margin-bottom:6px;background:var(--surface);cursor:pointer;transition:all .2s}
.checklist-item:hover{border-color:var(--accent);background:rgba(91,76,219,.02)}
.checklist-item.done{opacity:.55}
.checklist-item .checkbox{width:20px;height:20px;border-radius:6px;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .2s}
.checklist-item.done .checkbox{background:var(--green);border-color:var(--green)}
.checklist-item .checkbox svg{width:12px;height:12px;color:#fff;opacity:0}
.checklist-item.done .checkbox svg{opacity:1}
.checklist-item .text{flex:1;font-weight:500;font-size:13px;color:var(--text)}

/* NOTIFICATIONS */
.notif-panel{position:fixed;right:0;top:0;width:360px;height:100vh;background:var(--surface);border-left:1px solid var(--border);z-index:900;transform:translateX(100%);transition:transform .3s;overflow-y:auto;box-shadow:-4px 0 20px rgba(0,0,0,.1)}
.notif-panel.open{transform:translateX(0)}
.notif-panel .header{display:flex;align-items:center;justify-content:space-between;padding:20px;border-bottom:1px solid var(--border)}
.notif-panel .header h3{font-size:15px;font-weight:700}
.notif-item{display:flex;gap:12px;padding:14px 20px;border-bottom:1px solid var(--border);cursor:pointer;transition:background .2s}
.notif-item:hover{background:var(--surface2)}
.notif-item.unread{background:var(--accent-glow)}
.notif-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.notif-text{flex:1}
.notif-text .title{font-weight:600;font-size:13px;margin-bottom:2px;color:var(--text)}
.notif-text .desc{font-size:12px;color:var(--text2)}
.notif-text .time{font-size:11px;color:var(--text3);margin-top:3px}

/* EMPLOYEE CARDS */
.emp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px}
.emp-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:18px;transition:all .2s;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.emp-card:hover{border-color:var(--accent);transform:translateY(-2px);box-shadow:var(--shadow)}
.emp-card .header{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.emp-card .avatar{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#fff;flex-shrink:0}
.emp-card .info h4{font-size:14px;font-weight:600;color:var(--text)}
.emp-card .info p{font-size:12px;color:var(--text3)}
.emp-card .details{display:flex;flex-wrap:wrap;gap:5px}
.emp-card .detail-tag{font-size:11px;padding:3px 8px;border-radius:6px;background:var(--surface2);color:var(--text2)}

/* UPLOAD */
.upload-zone{border:2px dashed var(--border);border-radius:var(--radius-lg);padding:48px 20px;text-align:center;cursor:pointer;transition:all .3s;background:var(--surface)}
.upload-zone:hover,.upload-zone.drag-over{border-color:var(--accent);background:var(--accent-glow)}
.upload-zone .icon{font-size:48px;margin-bottom:12px}.upload-zone h3{font-size:16px;margin-bottom:6px;color:var(--text)}.upload-zone p{font-size:13px;color:var(--text3)}
.upload-zone input[type=file]{display:none}

/* REVIEW CARD */
.review-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;margin-bottom:14px;transition:border .2s;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.review-card:hover{border-color:var(--accent)}
.review-card .top{display:flex;align-items:flex-start;gap:14px;margin-bottom:14px}
.review-card .avatar{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;color:#fff;flex-shrink:0}
.review-card .info{flex:1}
.review-card .info h4{font-size:15px;font-weight:600;margin-bottom:3px;color:var(--text)}
.review-card .info .meta-row{display:flex;flex-wrap:wrap;gap:8px;font-size:12px;color:var(--text3)}
.review-card .resume-preview{background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:14px;margin-bottom:14px;font-size:13px;color:var(--text2);line-height:1.6;max-height:140px;overflow-y:auto}
.review-card .action-bar{display:flex;gap:8px;flex-wrap:wrap}

/* BG CHECKS */
.bg-check-grid{display:grid;gap:6px}
.bg-check-row{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);font-size:13px}

/* CARDS */
.card-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.04)}
.section-title{font-size:15px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px;color:var(--text)}
.page{display:none}.page.active{display:block}

/* PROGRESS BAR */
.progress-wrap{background:var(--surface2);border-radius:8px;height:6px;margin-bottom:14px;overflow:hidden}
.progress-fill{background:var(--green);height:100%;border-radius:8px;transition:width .4s}
    </style>
    @endverbatim
    @stack('styles')
</head>
<body>
    @include('partials.sidebar')
    <div class="main">
        @include('partials.topbar')
        <div class="content" id="contentArea">
            @yield('content')
        </div>
    </div>

    @include('partials.notifications')
    @include('partials.modals')

    <script>
/* â”€â”€ Auth helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function getToken(){ return localStorage.getItem('hris_token'); }
function clearToken(){ localStorage.removeItem('hris_token'); }

async function apiFetch(url, opts){
    opts = opts || {};
    var token = getToken();
    if (!token){ window.location.href = '/login'; return null; }
    var isForm = opts.body instanceof FormData;
    var headers = Object.assign({'Accept':'application/json','Authorization':'Bearer '+token}, opts.headers||{});
    if (!isForm) headers['Content-Type'] = 'application/json';
    opts.headers = headers;
    try {
        var r = await fetch(url, opts);
        if (r.status === 401){ clearToken(); window.location.href='/login'; return null; }
        return r;
    } catch(e){ toast('Network error','error'); return null; }
}

/* â”€â”€ Utilities â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

var CLR=['#5b4cdb','#e5508c','#00875a','#ff991f','#0065ff','#ff8b00','#7b68ee','#36b37e'];
function Cl(id){ return CLR[(+id||0)%CLR.length]; }
function In(f,l){ return ((f||'')[0]||'').toUpperCase()+((l||'')[0]||'').toUpperCase(); }
function fD(dt){ if(!dt) return 'â€”'; var d=new Date(dt.replace(' ','T')); return d.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric'})+' '+d.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit'}); }
function fDate(dt){ if(!dt) return 'â€”'; return new Date(dt).toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'}); }

var SL={needs_review:'Needs Review',invite_sent:'Invite Sent',no_response:'No Response',interview_scheduled:'Interview Scheduled',post_interview_review:'Post-Interview Review',pre_screening_passed:'Pre-Screening Passed',awaiting_background_check:'Awaiting Background Check',offer_sent:'Offer Sent',offer_accepted:'Offer Accepted',rejected:'Rejected',applicant_declined:'Applicant Declined',queue:'Queue',onboarding:'Onboarding',hired:'Hired'};
var SB={needs_review:'needs-review',invite_sent:'invite-sent',no_response:'queue',interview_scheduled:'interview',post_interview_review:'post-interview',pre_screening_passed:'prescreening',awaiting_background_check:'bg-check',offer_sent:'offer-sent',offer_accepted:'offer-accepted',rejected:'rejected',applicant_declined:'declined',queue:'queue',onboarding:'onboarding',hired:'offer-accepted'};
function B(s){ var lbl=SL[s]||s; return '<span class="badge badge-'+(SB[s]||'queue')+'">'+esc(lbl)+'</span>'; }

function toast(m,type,duration){
    var bg = type==='error' ? 'var(--red)' : type==='info' ? 'var(--primary)' : 'var(--green)';
    var e=document.createElement('div');
    e.style.cssText='position:fixed;bottom:24px;right:24px;background:'+bg+';color:#fff;padding:12px 20px;border-radius:var(--radius);font-weight:600;font-size:13px;z-index:2000;animation:fadeIn .3s ease;box-shadow:0 4px 12px rgba(0,0,0,.2);max-width:380px';
    e.textContent=m; document.body.appendChild(e); setTimeout(function(){ e.remove(); }, duration||2800);
}

function loading(id){ var el=document.getElementById(id); if(el) el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:32px;margin-bottom:12px">â³</div><p>Loadingâ€¦</p></div>'; }
function empty(id,msg){ var el=document.getElementById(id); if(el) el.innerHTML='<div style="text-align:center;padding:60px;color:var(--text3)"><div style="font-size:40px;margin-bottom:12px">ðŸ“­</div><p>'+(msg||'Nothing here yet.')+'</p></div>'; }

/* â”€â”€ Modals â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function openModal(id){ document.getElementById('modal-'+id).classList.add('open'); }
function closeModal(id){ document.getElementById('modal-'+id).classList.remove('open'); }
document.addEventListener('click', function(e){
    if(e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
});

/* â”€â”€ Notifications â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function toggleNotif(){ document.getElementById('notifPanel').classList.toggle('open'); }

async function loadNotifications(){
    var r = await apiFetch('/api/notifications');
    if(!r) return;
    var data = await r.json();
    var list = document.getElementById('notifList');
    var dot  = document.querySelector('#notifBtn .dot');
    if(!list) return;
    var unread = data.filter(function(n){ return !n.read_at; });
    if(dot) dot.style.display = unread.length ? 'block' : 'none';
    if(!data.length){ list.innerHTML='<div style="padding:24px;text-align:center;color:var(--text3);font-size:13px">No notifications yet.</div>'; return; }
    list.innerHTML = data.map(function(n){
        var d = typeof n.data === 'string' ? JSON.parse(n.data) : n.data;
        return '<div class="notif-item '+(n.read_at?'':'unread')+'" onclick="markNotifRead(\''+esc(n.id)+'\')">'
            +'<div class="notif-icon" style="background:var(--accent-glow);font-size:18px">ðŸ””</div>'
            +'<div class="notif-text"><div class="title">'+esc(d.title||'Notification')+'</div>'
            +'<div class="desc">'+esc(d.message||'')+'</div>'
            +'<div class="time">'+fDate(n.created_at)+'</div></div></div>';
    }).join('');
}

async function markNotifRead(id){
    await apiFetch('/api/notifications/'+id+'/read', {method:'POST'});
    loadNotifications();
}

async function readAllNotifs(){
    var r = await apiFetch('/api/notifications/read-all', {method:'POST'});
    if(r) loadNotifications();
}

/* â”€â”€ Global: update review badge â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
async function updateReviewBadge(){
    var r = await apiFetch('/api/candidates-review-queue');
    if(!r) return;
    var data = await r.json();
    var el = document.getElementById('reviewBadge');
    if(el) el.textContent = data.length || 0;
}

/* â”€â”€ Global: Candidate detail modal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
async function viewCandidate(id){
    document.getElementById('detailName').textContent = 'Loadingâ€¦';
    document.getElementById('detailBody').innerHTML = '<div style="text-align:center;padding:40px;color:var(--text3)">â³ Loading profileâ€¦</div>';
    document.getElementById('detailFooter').innerHTML = '';
    openModal('candidateDetail');

    var r = await apiFetch('/api/candidates/'+id);
    if(!r){ closeModal('candidateDetail'); return; }
    var data = await r.json();
    var c = data.candidate;
    var prog = data.onboarding_progress;
    var fp = esc(c.first_name)+' '+esc(c.last_name);
    document.getElementById('detailName').textContent = c.first_name+' '+c.last_name;

    var statusOpts = Object.keys(SL).map(function(k){
        return '<option value="'+k+'"'+(c.status===k?' selected':'')+'>'+esc(SL[k])+'</option>';
    }).join('');

    var bgHtml = '';
    if(c.background_checks && c.background_checks.length){
        bgHtml='<div style="margin-top:14px"><h4 style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--text3);margin-bottom:8px">Background Checks</h4><div class="bg-check-grid">'
        +c.background_checks.map(function(b){
            var col=b.status==='complete'?'var(--green)':b.status==='failed'?'var(--red)':'var(--yellow)';
            return '<div class="bg-check-row"><span>'+esc((b.check_type||'').toUpperCase().replace('_','/')).replace('SAM_OIG','SAM/OIG').replace('NPDB','NPDB').replace('MDHHS','MDHHS')+'</span>'
                +'<span style="font-weight:600;color:'+col+'">'+esc(b.status||'pending')+'</span></div>';
        }).join('')+'</div></div>';
    }

    var refsHtml = '';
    if(c.references && c.references.length){
        refsHtml='<div style="margin-top:14px"><h4 style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--text3);margin-bottom:8px">References</h4>'
        +c.references.map(function(ref){
            var col=ref.status==='received'?'var(--green)':ref.status==='sent'?'var(--blue)':'var(--text3)';
            return '<div style="display:flex;justify-content:space-between;padding:8px 12px;background:var(--surface2);border-radius:var(--radius);margin-bottom:4px;font-size:12px">'
                +'<span>'+esc(ref.reference_name)+' ('+esc(ref.relationship||'Reference')+')</span>'
                +'<span style="color:'+col+';font-weight:600">'+esc(ref.status)+'</span></div>';
        }).join('')+'</div>';
    }

    var logsHtml = '';
    if(c.activity_logs && c.activity_logs.length){
        logsHtml='<div style="margin-top:14px"><h4 style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--text3);margin-bottom:8px">Activity</h4><div class="timeline">'
        +c.activity_logs.slice(0,8).map(function(l){
            return '<div class="timeline-item"><div class="time">'+esc(fDate(l.created_at))+'</div><div class="event">'+esc(l.description||l.action)+'</div></div>';
        }).join('')+'</div></div>';
    }

    document.getElementById('detailBody').innerHTML=
      '<div style="display:flex;gap:20px;flex-wrap:wrap">'
      +'<div style="flex:1;min-width:260px">'
        +'<div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">'
          +'<div style="width:52px;height:52px;border-radius:50%;background:'+Cl(c.id)+';display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px">'+In(c.first_name,c.last_name)+'</div>'
          +'<div>'+B(c.status)+'</div></div>'
        +'<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px;margin-bottom:14px">'
          +'<div><span style="color:var(--text3)">Email</span><br>'+esc(c.email||'â€”')+'</div>'
          +'<div><span style="color:var(--text3)">Phone</span><br>'+esc(c.phone||'â€”')+'</div>'
          +'<div><span style="color:var(--text3)">Category</span><br>'+esc(c.category?c.category.name:'â€”')+'</div>'
          +'<div><span style="color:var(--text3)">Source</span><br>'+esc(c.source||'â€”')+'</div>'
          +'<div><span style="color:var(--text3)">Applied</span><br>'+esc(fDate(c.created_at))+'</div>'
          +'<div><span style="color:var(--text3)">Assigned</span><br>'+esc(c.assigned_to?c.assigned_to.first_name+' '+c.assigned_to.last_name:'â€”')+'</div>'
        +'</div>'
        +(c.resume_text?'<div class="form-group"><label>Resume</label><div style="background:var(--surface2);border:1px solid var(--border);border-radius:var(--radius);padding:12px;font-size:12px;line-height:1.6;max-height:150px;overflow-y:auto;white-space:pre-wrap">'+esc(c.resume_text)+'</div></div>':'')
        +'<div class="form-group" style="margin-top:12px"><label>Change Status</label>'
        +'<select id="detailSt">'+statusOpts+'</select></div>'
      +'</div>'
      +'<div style="flex:1;min-width:220px">'+bgHtml+refsHtml+logsHtml+'</div>'
    +'</div>';

    document.getElementById('detailFooter').innerHTML=
      '<button class="btn btn-danger btn-sm" onclick="cdAction('+c.id+',\'rejected\')">Reject</button>'
      +'<button class="btn btn-secondary" onclick="closeModal(\'candidateDetail\')">Close</button>'
      +'<button class="btn btn-primary" onclick="cdAction('+c.id+',null)">Save Status</button>';
}

async function cdAction(id, forceStatus){
    var st = forceStatus || document.getElementById('detailSt').value;
    var r = await apiFetch('/api/candidates/'+id+'/status', {method:'PATCH', body:JSON.stringify({status:st})});
    if(!r) return;
    var data = await r.json();
    closeModal('candidateDetail');
    toast((data.first_name||'Candidate')+' â†’ '+(SL[st]||st));
    if(typeof pageRefresh === 'function') pageRefresh();
    updateReviewBadge();
}

/* â”€â”€ Global: logout â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
function logout(){
    apiFetch('/api/logout', {method:'POST'}).finally(function(){ clearToken(); window.location.href='/login'; });
}

/* â”€â”€ DOMContentLoaded â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
document.addEventListener('DOMContentLoaded', async function(){
    if(!getToken()){ window.location.href='/login'; return; }

    // Load current user
    var r = await apiFetch('/api/me');
    if(!r) return;
    var me = await r.json();
    var av = document.getElementById('sidebarUserAvatar');  if(av) av.textContent = In(me.first_name, me.last_name);
    var nm = document.getElementById('sidebarUserName');    if(nm) nm.textContent = me.first_name+' '+me.last_name;
    var rl = document.getElementById('sidebarUserRole');    if(rl) rl.textContent = me.role === 'admin' ? 'Admin' : 'HR Staff';

    loadNotifications();
    updateReviewBadge();
    document.getElementById('notifBtn').addEventListener('click', toggleNotif);

    // Global search
    var gs = document.getElementById('globalSearch');
    if(gs) gs.addEventListener('keydown', function(e){
        if(e.key==='Enter' && this.value.trim()) window.location.href='/hris/pipeline?search='+encodeURIComponent(this.value.trim());
    });
});
    </script>
    @stack('scripts')
</body>
</html>
