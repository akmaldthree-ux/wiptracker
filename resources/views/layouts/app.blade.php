<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#533afd">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<title>@yield('title','DPIS') — Dthree Production Integration System</title>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
<style>
/* ════════════════════════════════════════════════════════════
   DESIGN TOKENS — Stripi-inspired design system
════════════════════════════════════════════════════════════ */
:root {
  /* Brand */
  --primary:           #533afd;
  --primary-deep:      #4434d4;
  --primary-press:     #2e2b8c;
  --primary-soft:      #665efd;
  --primary-subdued:   #b9b9f9;
  --primary-bg:        #f0eeff;

  /* Ink */
  --ink:               #0d253d;
  --ink-secondary:     #273951;
  --ink-mute:          #64748d;
  --ink-mute-2:        #61718a;

  /* Surface */
  --canvas:            #ffffff;
  --canvas-soft:       #f6f9fc;
  --canvas-cream:      #f5e9d4;
  --hairline:          #e3e8ee;
  --hairline-input:    #a8c3de;

  /* Sidebar */
  --sidebar-bg:        #0d1b2e;
  --sidebar-w:         252px;
  --sidebar-text:      rgba(255,255,255,.5);
  --sidebar-text-active: #ffffff;
  --sidebar-hover:     rgba(83,58,253,.12);
  --sidebar-active-bg: rgba(83,58,253,.18);
  --sidebar-active-border: #533afd;

  /* Layout */
  --topbar-h:          60px;

  /* Radius */
  --radius-xs:         4px;
  --radius-sm:         6px;
  --radius-md:         8px;
  --radius-lg:         12px;
  --radius-xl:         16px;
  --radius-pill:       9999px;

  /* Shadow */
  --shadow-sm:         rgba(0,55,112,.08) 0 1px 3px;
  --shadow-md:         rgba(0,55,112,.08) 0 8px 24px, rgba(0,55,112,.04) 0 2px 6px;

  /* Page bg */
  --bg:                #f6f9fc;
}

/* ────────────────────────────────
   BASE
──────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

body {
  font-family: 'Inter', system-ui, sans-serif;
  font-weight: 300;
  font-size: 15px;
  line-height: 1.4;
  color: var(--ink);
  background: var(--bg);
  margin: 0;
  -webkit-font-smoothing: antialiased;
  font-feature-settings: "ss01";
}

/* ────────────────────────────────
   SIDEBAR
──────────────────────────────── */
#sidebar {
  position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-w);
  background: var(--sidebar-bg);
  z-index: 1040; transition: .3s cubic-bezier(.4,0,.2,1);
  overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column;
  border-right: 1px solid rgba(255,255,255,.06);
}
#sidebar::-webkit-scrollbar { width: 3px; }
#sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.08); border-radius: 2px; }

.sidebar-brand {
  padding: 1.4rem 1.2rem 1.1rem;
  display: flex; flex-direction: column; align-items: center;
  border-bottom: 1px solid rgba(255,255,255,.07); flex-shrink: 0; gap: .4rem;
}
.brand-logo-img { width: 120px; height: auto; opacity: .93; }
.brand-text .sub {
  color: var(--primary-subdued); font-size: .7rem; font-weight: 600;
  letter-spacing: 3.5px; text-transform: uppercase; text-align: center;
}

.nav-section-title {
  color: rgba(255,255,255,.22); font-size: .6rem; font-weight: 600;
  letter-spacing: 1.6px; text-transform: uppercase;
  padding: 1.1rem 1.2rem .3rem;
}
.sidebar-nav { flex: 1; padding: .35rem 0 1rem; }
.sidebar-nav .nav-link {
  display: flex; align-items: center; gap: .55rem;
  padding: .44rem .85rem; margin: 1px .55rem;
  color: var(--sidebar-text); border-radius: var(--radius-md);
  font-size: .82rem; font-weight: 400; transition: all .13s ease;
  text-decoration: none; position: relative; line-height: 1.4;
}
.sidebar-nav .nav-link:hover {
  color: rgba(255,255,255,.88); background: var(--sidebar-hover);
}
.sidebar-nav .nav-link.active {
  color: var(--sidebar-text-active); background: var(--sidebar-active-bg); font-weight: 500;
}
.sidebar-nav .nav-link.active::before {
  content: ''; position: absolute; left: -.55rem; top: 50%; transform: translateY(-50%);
  height: 55%; width: 3px; border-radius: 0 3px 3px 0; background: var(--sidebar-active-border);
}
.sidebar-nav .nav-link i { font-size: .88rem; width: 16px; text-align: center; flex-shrink: 0; }
.sidebar-nav .nav-link.active i { color: var(--primary-soft); }
.sidebar-nav .badge { font-size: .58rem; padding: .2em .5em; margin-left: auto; border-radius: var(--radius-pill); }
.nav-submenu { padding: 0; }
.nav-submenu .nav-link { padding: .38rem .85rem .38rem 2.5rem; font-size: .8rem; }
.collapse-arrow { transition: .22s; opacity: .4; margin-left: auto; font-size: .68rem; }
[aria-expanded="true"] .collapse-arrow { transform: rotate(180deg); }

/* ────────────────────────────────
   MAIN CONTENT
──────────────────────────────── */
#main-content {
  margin-left: var(--sidebar-w);
  min-height: 100vh; transition: .3s;
  display: flex; flex-direction: column;
}

/* ────────────────────────────────
   TOPBAR
──────────────────────────────── */
.topbar {
  height: var(--topbar-h); background: var(--canvas);
  border-bottom: 1px solid var(--hairline);
  display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
  position: sticky; top: 0; z-index: 1030;
  box-shadow: var(--shadow-sm);
  flex-shrink: 0;
}
.topbar .page-title {
  font-size: .9rem; font-weight: 500; color: var(--ink); margin: 0;
  letter-spacing: -.01em;
}
.topbar-right { display: flex; align-items: center; gap: .4rem; margin-left: auto; }

.icon-btn {
  position: relative; width: 34px; height: 34px;
  border: 1px solid var(--hairline);
  border-radius: var(--radius-md);
  display: flex; align-items: center; justify-content: center;
  background: transparent; color: var(--ink-mute);
  text-decoration: none; transition: all .13s; cursor: pointer;
}
.icon-btn:hover {
  border-color: var(--primary-subdued); color: var(--primary);
  background: var(--primary-bg);
}
.notif-badge {
  position: absolute; top: -4px; right: -4px; background: #ef4444; color: #fff;
  border-radius: 50%; width: 16px; height: 16px; font-size: .55rem;
  display: flex; align-items: center; justify-content: center; font-weight: 600;
  border: 2px solid var(--canvas);
}

.user-menu .dropdown-toggle {
  display: flex; align-items: center; gap: .4rem; background: transparent;
  border: 1px solid var(--hairline); border-radius: var(--radius-pill);
  padding: .25rem .65rem .25rem .3rem; cursor: pointer; color: var(--ink);
  transition: all .13s;
}
.user-menu .dropdown-toggle:hover {
  border-color: var(--primary-subdued); background: var(--primary-bg);
}
.user-menu .dropdown-toggle::after { display: none; }

.avatar {
  width: 26px; height: 26px; border-radius: var(--radius-pill);
  background: linear-gradient(135deg, var(--primary), var(--primary-soft));
  display: flex; align-items: center; justify-content: center; color: #fff;
  font-size: .62rem; font-weight: 600; flex-shrink: 0; letter-spacing: .3px;
}
.user-info .user-name { font-size: .78rem; font-weight: 500; line-height: 1.25; }
.user-info .user-role { font-size: .65rem; color: var(--ink-mute); line-height: 1.25; }

/* ────────────────────────────────
   PAGE CONTENT
──────────────────────────────── */
.page-content { padding: 1.5rem; flex: 1; }

/* ────────────────────────────────
   CARDS
──────────────────────────────── */
.card {
  border: 1px solid var(--hairline); border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm); background: var(--canvas);
}
.card-header {
  background: var(--canvas-soft); border-bottom: 1px solid var(--hairline);
  padding: .8rem 1.25rem; font-weight: 500; font-size: .875rem; color: var(--ink);
  border-radius: var(--radius-lg) var(--radius-lg) 0 0;
  display: flex; align-items: center;
}
.card-footer {
  background: var(--canvas-soft); border-top: 1px solid var(--hairline);
  padding: .8rem 1.25rem; border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}

/* ────────────────────────────────
   KPI / STAT CARDS
──────────────────────────────── */
.kpi-card {
  border-radius: var(--radius-lg); padding: 1.3rem 1.4rem;
  color: #fff; position: relative; overflow: hidden; border: none !important;
  box-shadow: var(--shadow-md) !important;
  transition: transform .2s ease;
}
.kpi-card:hover { transform: translateY(-2px); }
.kpi-card::before {
  content: ''; position: absolute; top: -28px; right: -28px;
  width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,.07);
}
.kpi-card .kpi-icon {
  position: absolute; right: .9rem; bottom: .6rem;
  font-size: 2.6rem; opacity: .1; z-index: 0;
}
.kpi-card .kpi-value {
  font-size: 1.85rem; font-weight: 300; line-height: 1;
  letter-spacing: -1.2px; position: relative; z-index: 1;
  font-feature-settings: "tnum","ss01";
}
.kpi-card .kpi-label {
  font-size: .7rem; opacity: .78; margin-top: .35rem;
  font-weight: 500; letter-spacing: .4px; text-transform: uppercase;
}
.kpi-card .kpi-change { font-size: .68rem; margin-top: .45rem; opacity: .75; display: flex; align-items: center; gap: .3rem; }

.kpi-blue    { background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); }
.kpi-green   { background: linear-gradient(135deg, #065f46 0%, #059669 100%); }
.kpi-orange  { background: linear-gradient(135deg, #9a3412 0%, #ea580c 100%); }
.kpi-red     { background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%); }
.kpi-purple  { background: linear-gradient(135deg, var(--primary-press) 0%, var(--primary) 100%); }
.kpi-teal    { background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); }
.kpi-indigo  { background: linear-gradient(135deg, var(--primary-deep) 0%, var(--primary-soft) 100%); }

.stat-card {
  background: var(--canvas); border: 1px solid var(--hairline);
  border-radius: var(--radius-lg); padding: 1rem 1.25rem;
  text-align: center; box-shadow: var(--shadow-sm);
}
.stat-card .stat-value {
  font-size: 1.65rem; font-weight: 300; line-height: 1.1;
  letter-spacing: -0.8px; font-feature-settings: "tnum","ss01";
}
.stat-card .stat-label { font-size: .69rem; color: var(--ink-mute); margin-top: .2rem; font-weight: 500; text-transform: uppercase; letter-spacing: .4px; }

/* ────────────────────────────────
   BUTTONS — pill geometry per design spec
──────────────────────────────── */
.btn {
  border-radius: var(--radius-pill) !important;
  font-weight: 400; font-size: .875rem;
  padding: 6px 18px;
  transition: all .13s;
  letter-spacing: 0;
  line-height: 1;
}
.btn-sm  { font-size: .8rem !important; padding: 5px 14px !important; }
.btn-lg  { font-size: .95rem !important; padding: 9px 22px !important; }

.btn-primary {
  background: var(--primary); border-color: var(--primary); color: #fff;
}
.btn-primary:hover {
  background: var(--primary-deep); border-color: var(--primary-deep);
  box-shadow: 0 4px 14px rgba(83,58,253,.32); color: #fff;
}
.btn-primary:active { background: var(--primary-press) !important; border-color: var(--primary-press) !important; }

.btn-outline-primary {
  color: var(--primary); border-color: var(--primary);
}
.btn-outline-primary:hover {
  background: var(--primary-bg); color: var(--primary); border-color: var(--primary);
  box-shadow: 0 2px 8px rgba(83,58,253,.15);
}

.btn-outline-success:hover { background: #f0fdf4; }
.btn-outline-danger:hover  { background: #fef2f2; }
.btn-outline-secondary:hover { background: var(--canvas-soft); }
.btn-success:hover  { box-shadow: 0 4px 14px rgba(5,150,105,.28); }
.btn-danger:hover   { box-shadow: 0 4px 14px rgba(220,38,38,.28); }
.btn-warning        { color: #fff !important; }
.btn-warning:hover  { box-shadow: 0 4px 14px rgba(234,88,12,.28); color: #fff !important; }
.btn:active { transform: scale(.98); }

/* ────────────────────────────────
   TABLES
──────────────────────────────── */
.table { font-size: .855rem; }
.table thead th {
  font-size: .68rem; text-transform: uppercase; letter-spacing: .65px;
  color: var(--ink-mute); font-weight: 600;
  border-bottom: 1px solid var(--hairline);
  padding: .65rem 1rem; background: var(--canvas-soft); white-space: nowrap;
}
.table tbody td {
  padding: .75rem 1rem; vertical-align: middle;
  border-color: var(--hairline);
  font-feature-settings: "ss01";
}
/* money / numeric cells */
.table tbody td.num,
.table tbody td[data-num] {
  font-feature-settings: "tnum","ss01";
  letter-spacing: -.36px;
}
.table-hover tbody tr { transition: background .1s; }
.table-hover tbody tr:hover { background: #f8fafe; }
.table tbody tr:last-child td { border-bottom: 0; }

/* ────────────────────────────────
   FORM CONTROLS
──────────────────────────────── */
.form-control, .form-select {
  border-radius: var(--radius-sm);
  border-color: var(--hairline-input);
  font-size: .875rem; padding: .42rem .75rem;
  transition: all .13s;
  background: var(--canvas);
  color: var(--ink);
  font-weight: 300;
}
.form-control:focus, .form-select:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(83,58,253,.1);
  background: var(--canvas);
}
.form-control-sm, .form-select-sm { font-size: .82rem; padding: .33rem .65rem; }
.form-label { font-size: .82rem; font-weight: 500; color: var(--ink-secondary); margin-bottom: .3rem; }
.form-text  { font-size: .75rem; color: var(--ink-mute); }

/* ────────────────────────────────
   ALERTS
──────────────────────────────── */
.alert { border-radius: var(--radius-md); border: none; font-size: .875rem; padding: .75rem 1rem; font-weight: 300; }
.alert-success { background: #f0fdf4; color: #166534; border-left: 3px solid #22c55e !important; border-radius: 0 var(--radius-md) var(--radius-md) 0 !important; }
.alert-danger  { background: #fef2f2; color: #991b1b; border-left: 3px solid #ef4444 !important; border-radius: 0 var(--radius-md) var(--radius-md) 0 !important; }
.alert-warning { background: #fffbeb; color: #92400e; border-left: 3px solid #f59e0b !important; border-radius: 0 var(--radius-md) var(--radius-md) 0 !important; }
.alert-info    { background: #eff6ff; color: #1d4ed8; border-left: 3px solid var(--primary) !important; border-radius: 0 var(--radius-md) var(--radius-md) 0 !important; }

/* ────────────────────────────────
   BADGES
──────────────────────────────── */
.badge { font-weight: 500; letter-spacing: .15px; border-radius: var(--radius-xs); font-size: .72em; }

/* pill-tag-soft style */
.badge-soft-primary { background: var(--primary-bg); color: var(--primary-deep); border-radius: var(--radius-pill); padding: 3px 8px; }

.badge.bg-primary, .badge.bg-success, .badge.bg-danger,
.badge.bg-secondary, .badge.bg-dark, .badge.bg-info,
.badge.bg-warning { color: #fff !important; }

.badge.bg-success.bg-opacity-15   { color: #065f46 !important; }
.badge.bg-warning.bg-opacity-15   { color: #92400e !important; background-color: rgba(245,158,11,.15) !important; }
.badge.bg-danger.bg-opacity-15    { color: #991b1b !important; }
.badge.bg-primary.bg-opacity-15   { color: var(--primary-deep) !important; background: var(--primary-bg) !important; }
.badge.bg-secondary.bg-opacity-15 { color: #475569 !important; }
.badge.bg-info.bg-opacity-15      { color: #0369a1 !important; }

/* ────────────────────────────────
   STATUS BADGES
──────────────────────────────── */
.badge-draft     { background: #f1f5f9; color: #475569; border-radius: var(--radius-pill); }
.badge-active    { background: var(--primary-bg); color: var(--primary-deep); border-radius: var(--radius-pill); }
.badge-completed { background: #dcfce7; color: #166534; border-radius: var(--radius-pill); }
.badge-on_hold   { background: #fef3c7; color: #92400e; border-radius: var(--radius-pill); }
.badge-cancelled { background: #fee2e2; color: #991b1b; border-radius: var(--radius-pill); }

/* ────────────────────────────────
   DROPDOWN MENUS
──────────────────────────────── */
.dropdown-menu {
  border: 1px solid var(--hairline); border-radius: var(--radius-lg);
  box-shadow: var(--shadow-md); font-size: .855rem; padding: .3rem;
}
.dropdown-item { border-radius: var(--radius-md); padding: .42rem .75rem; transition: background .1s; color: var(--ink); font-weight: 300; }
.dropdown-item:hover { background: var(--primary-bg); color: var(--primary); }
.dropdown-divider { border-color: var(--hairline); }

/* ────────────────────────────────
   PIPELINE
──────────────────────────────── */
.pipeline-step { text-align: center; position: relative; min-width: 80px; }
.pipeline-step:not(:last-child)::after {
  content: ''; position: absolute; top: 21px; left: calc(50% + 24px);
  width: calc(100% - 48px); height: 2px;
  background: var(--hairline); z-index: 0;
}
.pipeline-dot {
  width: 44px; height: 44px; border-radius: 50%;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: .8rem; font-weight: 600; position: relative; z-index: 1; margin-bottom: .5rem;
  box-shadow: var(--shadow-sm);
}
.pipeline-dot.green  { background: #dcfce7; color: #166534; border: 2px solid #22c55e; }
.pipeline-dot.yellow { background: #fef9c3; color: #a16207; border: 2px solid #eab308; }
.pipeline-dot.red    { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
.pipeline-dot.grey   { background: var(--canvas-soft); color: var(--ink-mute); border: 2px solid var(--hairline); }

/* ────────────────────────────────
   PROGRESS
──────────────────────────────── */
.progress { height: 5px; border-radius: 3px; background: var(--hairline); overflow: hidden; }
.progress-bar { border-radius: 3px; transition: width .5s ease; background: var(--primary); }
.progress-bar.bg-success { background: #22c55e !important; }
.progress-bar.bg-danger  { background: #ef4444 !important; }
.progress-bar.bg-warning { background: #f59e0b !important; }

/* ────────────────────────────────
   LIST GROUPS
──────────────────────────────── */
.list-group-item { border-color: var(--hairline); font-size: .875rem; font-weight: 300; color: var(--ink); }
.list-group-item-action:hover { background: var(--canvas-soft); }

/* ────────────────────────────────
   PAGINATION
──────────────────────────────── */
.pagination { gap: 2px; }
.page-link {
  border-radius: var(--radius-pill) !important;
  border-color: var(--hairline); color: var(--primary);
  font-size: .8rem; padding: .3rem .65rem; font-weight: 300;
}
.page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

/* ────────────────────────────────
   EMPTY STATES
──────────────────────────────── */
.empty-state { text-align: center; padding: 3rem 1.5rem; color: var(--ink-mute); }
.empty-state i { font-size: 2.4rem; opacity: .22; display: block; margin-bottom: .75rem; }
.empty-state p { margin: 0; font-size: .875rem; }

/* ────────────────────────────────
   MISC
──────────────────────────────── */
.text-muted { color: var(--ink-mute) !important; }
.fw-semibold { font-weight: 500 !important; }
.fw-bold { font-weight: 600 !important; }
hr { border-color: var(--hairline); }
a { color: var(--primary); }
a:hover { color: var(--primary-deep); }
.text-warning { color: #b45309 !important; }

/* ────────────────────────────────
   BREADCRUMB
──────────────────────────────── */
.breadcrumb { font-size: .76rem; margin-bottom: 0; font-weight: 300; }
.breadcrumb-item a { color: var(--ink-mute); text-decoration: none; }
.breadcrumb-item a:hover { color: var(--primary); }
.breadcrumb-item.active { color: var(--ink-mute); }
.breadcrumb-item + .breadcrumb-item::before { color: var(--hairline); }

/* ────────────────────────────────
   PAGE HEADER
──────────────────────────────── */
.page-header { margin-bottom: 1.5rem; }
.page-header h4, .page-header h5 {
  font-weight: 300; margin-bottom: .2rem; line-height: 1.2;
  letter-spacing: -.4px; color: var(--ink);
}
h4, h5, h6 { letter-spacing: -.2px; }
h4 { font-weight: 300; letter-spacing: -.5px; }
h5 { font-weight: 300; letter-spacing: -.3px; }

/* ────────────────────────────────
   FILTER BAR
──────────────────────────────── */
.filter-bar {
  background: var(--canvas); border: 1px solid var(--hairline);
  border-radius: var(--radius-lg); padding: .55rem 1rem; margin-bottom: 1rem;
}

/* ────────────────────────────────
   TABLE HIGHLIGHTS
──────────────────────────────── */
.table-danger td  { background: #fff5f5 !important; }
.table-warning td { background: #fffceb !important; }

/* ────────────────────────────────
   MOBILE OVERLAY
──────────────────────────────── */
.overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(13,37,61,.45); z-index: 1039;
  backdrop-filter: blur(2px);
}
.overlay.show { display: block; }

/* ────────────────────────────────
   RESPONSIVE
──────────────────────────────── */
@media (max-width: 992px) {
  #sidebar { transform: translateX(-100%); }
  #sidebar.show { transform: translateX(0); box-shadow: var(--shadow-md); }
  #main-content { margin-left: 0; }
  .page-content { padding: 1rem; }
  .topbar { padding: 0 1rem; }
}
@media (max-width: 576px) {
  .page-content { padding: .75rem; }
  .kpi-card .kpi-value { font-size: 1.45rem; }
  .topbar .page-title { font-size: .82rem; max-width: 130px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .user-info { display: none !important; }
  .table { font-size: .78rem; }
  .table thead th { font-size: .63rem; padding: .5rem .6rem; }
  .table tbody td { padding: .58rem .6rem; }
  .breadcrumb { font-size: .7rem; }
  .card-header { font-size: .82rem; padding: .65rem 1rem; }
  .form-card-container { max-width: 100% !important; }
  .mobile-wrap { flex-wrap: wrap !important; }
  .d-mob-none { display: none !important; }
}
@media (max-width: 400px) {
  .kpi-card { padding: 1rem; }
  .kpi-card .kpi-value { font-size: 1.3rem; }
  .pipeline-dot { width: 36px; height: 36px; font-size: .72rem; }
}

/* ────────────────────────────────
   ANIMATIONS
──────────────────────────────── */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(6px); }
  to   { opacity: 1; transform: translateY(0); }
}
.page-content > * { animation: fadeInUp .2s ease both; }
.page-content > *:nth-child(2) { animation-delay: .04s; }
.page-content > *:nth-child(3) { animation-delay: .07s; }
.page-content > *:nth-child(4) { animation-delay: .1s; }

/* ────────────────────────────────
   OVERFLOW / MISC
──────────────────────────────── */
.overflow-x-auto { overflow-x: auto; }
</style>
@stack('styles')
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
  <div class="sidebar-brand">
    <img src="{{ asset('images/dthree-logo.png') }}" alt="DTHREE" class="brand-logo-img">
    <div class="brand-text">
      <div class="sub">DPIS</div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-section-title">Utama</div>
    <a href="{{ route('dashboard.executive') }}" class="nav-link {{ request()->routeIs('dashboard.executive') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i> Dashboard Eksekutif
    </a>
    <a href="{{ route('dashboard.operational') }}" class="nav-link {{ request()->routeIs('dashboard.operational') ? 'active' : '' }}">
      <i class="bi bi-display"></i> Dashboard Operasional
    </a>
    <a href="{{ route('dashboard.wip-monitor') }}" class="nav-link {{ request()->routeIs('dashboard.wip-monitor') ? 'active' : '' }}">
      <i class="bi bi-radar"></i> WIP Monitor
    </a>
    <a href="{{ route('dashboard.reject') }}" class="nav-link {{ request()->routeIs('dashboard.reject') ? 'active' : '' }}">
      <i class="bi bi-x-octagon"></i> Dashboard Reject
      @php
        try {
          $rejectCount = \App\Models\HandoverItem::where('qty_reject','>',0)
            ->whereNotNull('reject_type')
            ->whereHas('handover',fn($q)=>$q->where('confirmed_at','>=',now()->startOfMonth()))
            ->sum('qty_reject');
        } catch(\Throwable $e) { $rejectCount = 0; }
      @endphp
      @if($rejectCount > 0)<span class="badge bg-danger ms-auto">{{ $rejectCount }}</span>@endif
    </a>

    <div class="nav-section-title">Produksi</div>
    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
      <i class="bi bi-clipboard-check"></i> Order Produksi
    </a>
    <a href="{{ route('cutting.index') }}" class="nav-link {{ request()->routeIs('cutting.*') ? 'active' : '' }}">
      <i class="bi bi-scissors"></i> Cutting Plan
    </a>
    <a href="{{ route('handover.index') }}" class="nav-link {{ request()->routeIs('handover.*') ? 'active' : '' }}">
      <i class="bi bi-arrow-left-right"></i> Handover
      @php $pendingHo = \App\Models\Handover::where('status','pending')->count(); @endphp
      @if($pendingHo > 0)<span class="badge bg-warning text-dark ms-auto">{{ $pendingHo }}</span>@endif
    </a>
    <a href="{{ route('qc.index') }}" class="nav-link {{ request()->routeIs('qc.*') ? 'active' : '' }}">
        <i class="bi bi-shield-check"></i> QC Inspeksi
    </a>

    <div class="nav-section-title">Bahan & Biaya</div>
    <a href="{{ route('procurement.index') }}" class="nav-link {{ request()->routeIs('procurement.*') ? 'active' : '' }}">
      <i class="bi bi-clipboard2-check"></i> Procurement
      @php try { $pendingProc = \App\Models\ProductionOrder::whereIn('status',['draft','active'])->where('materials_approved',false)->count(); } catch(\Exception $e) { $pendingProc = 0; } @endphp
      @if($pendingProc > 0)<span class="badge bg-warning text-dark ms-auto">{{ $pendingProc }}</span>@endif
    </a>
    <a href="{{ route('bahan-baku.index') }}" class="nav-link {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}">
      <i class="bi bi-boxes"></i> Bahan Baku
      @php $lowStock = \App\Models\RawMaterial::whereRaw('current_stock < min_stock')->count(); @endphp
      @if($lowStock > 0)<span class="badge bg-danger ms-auto">{{ $lowStock }}</span>@endif
    </a>
    <a href="{{ route('bom.index') }}" class="nav-link {{ request()->routeIs('bom.*') ? 'active' : '' }}">
      <i class="bi bi-diagram-3"></i> Bill of Materials
    </a>
    <a href="{{ route('purchase-order.index') }}" class="nav-link {{ request()->routeIs('purchase-order.*') ? 'active' : '' }}">
      <i class="bi bi-cart-check"></i> Purchase Order
    </a>
    <a href="{{ route('budget.index') }}" class="nav-link {{ request()->routeIs('budget.*') ? 'active' : '' }}">
      <i class="bi bi-wallet2"></i> Budget & Biaya
    </a>

    <div class="nav-section-title">Laporan</div>
    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
      <i class="bi bi-file-bar-graph"></i> Laporan
    </a>

    @if(auth()->user()->isSupervisor())
    <div class="nav-section-title">Master Data</div>
    <a class="nav-link {{ request()->routeIs('master.*') ? 'active' : '' }}"
       data-bs-toggle="collapse" href="#masterMenu" role="button"
       aria-expanded="{{ request()->routeIs('master.*') ? 'true' : 'false' }}">
      <i class="bi bi-database"></i> Master Data
      <i class="bi bi-chevron-down collapse-arrow"></i>
    </a>
    <div class="collapse {{ request()->routeIs('master.*') ? 'show' : '' }}" id="masterMenu">
      <div class="nav-submenu">
        <a href="{{ route('master.produk.index') }}"          class="nav-link {{ request()->routeIs('master.produk.*') ? 'active' : '' }}"><i class="bi bi-tag"></i> Produk</a>
        <a href="{{ route('master.series.index') }}"          class="nav-link {{ request()->routeIs('master.series.*') ? 'active' : '' }}"><i class="bi bi-collection"></i> Series</a>
        <a href="{{ route('master.warna.index') }}"           class="nav-link {{ request()->routeIs('master.warna.*') ? 'active' : '' }}"><i class="bi bi-palette"></i> Warna</a>
        <a href="{{ route('master.ukuran.index') }}"          class="nav-link {{ request()->routeIs('master.ukuran.*') ? 'active' : '' }}"><i class="bi bi-rulers"></i> Ukuran</a>
        <a href="{{ route('master.stasiun.index') }}"         class="nav-link {{ request()->routeIs('master.stasiun.*') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i> Stasiun</a>
        <a href="{{ route('master.sewing-location.index') }}" class="nav-link {{ request()->routeIs('master.sewing-location.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Tempat Sewing</a>
        <a href="{{ route('master.supplier.index') }}" class="nav-link {{ request()->routeIs('master.supplier.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Supplier</a>
      </div>
    </div>
    @endif

    @if(auth()->user() && auth()->user()->isAdmin())
    <div class="nav-section-title">Administrasi</div>
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <i class="bi bi-people"></i> Manajemen User
    </a>
    @endif

    <div class="nav-section-title">Akun</div>
    <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
      <i class="bi bi-bell"></i> Notifikasi
      @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
      @if($unread > 0)<span class="badge bg-danger ms-auto">{{ $unread }}</span>@endif
    </a>
    <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
      <i class="bi bi-person-circle"></i> Profil Saya
    </a>
  </nav>
</div>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Main Content -->
<div id="main-content">
  <!-- Topbar -->
  <div class="topbar">
    <button class="icon-btn border-0 d-lg-none" onclick="toggleSidebar()" style="font-size:1.1rem">
      <i class="bi bi-list"></i>
    </button>
    <h6 class="page-title">@yield('page-title','Dashboard')</h6>

    <div class="topbar-right">
      <a href="{{ route('notifications.index') }}" class="icon-btn">
        <i class="bi bi-bell" style="font-size:.95rem"></i>
        @if(isset($unread) && $unread > 0)
          <span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>
        @endif
      </a>

      <div class="user-menu dropdown">
        <button class="dropdown-toggle" data-bs-toggle="dropdown">
          <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
          <div class="user-info d-none d-md-block">
            <div class="user-name">{{ auth()->user()->name }}</div>
            <div class="user-role">{{ auth()->user()->role_label }}</div>
          </div>
          <i class="bi bi-chevron-down ms-1" style="font-size:.65rem;opacity:.5"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <div class="px-3 py-2 border-bottom mb-1">
              <div class="fw-semibold" style="font-size:.82rem">{{ auth()->user()->name }}</div>
              <div class="text-muted" style="font-size:.72rem">{{ auth()->user()->email }}</div>
            </div>
          </li>
          <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2 text-primary"></i>Profil Saya</a></li>
          <li><hr class="dropdown-divider my-1"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="dropdown-item text-danger">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Alerts -->
  <div class="px-4 pt-3 pb-0" id="flash-area">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 js-auto-dismiss" role="alert">
      <i class="bi bi-check-circle-fill flex-shrink-0"></i>
      <span>{{ session('success') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
      <span>{{ session('warning') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-x-circle-fill flex-shrink-0"></i>
      <span>{{ session('error') }}</span>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
      <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i><strong>Terdapat kesalahan:</strong>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
      <ul class="mb-0 ps-3">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
    @endif
  </div>

  <!-- Page Content -->
  <div class="page-content">
    @yield('content')
  </div>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/chart.umd.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('overlay').classList.remove('show');
}
</script>
<script>
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function() {
    navigator.serviceWorker.register('/sw.js').catch(function(){});
  });
}

// Auto-dismiss success alerts after 4.5s
document.querySelectorAll('.js-auto-dismiss').forEach(function(el) {
  setTimeout(function() {
    var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
    if (bsAlert) bsAlert.close();
  }, 4500);
});

// Form submit protection: disable submit button, show spinner (POST forms only)
document.querySelectorAll('form:not([method="GET"]):not([method="get"])').forEach(function(form) {
  form.addEventListener('submit', function() {
    var btn = form.querySelector('[type="submit"]');
    if (!btn || btn.dataset.noSpinner) return;
    btn.disabled = true;
    var orig = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + btn.textContent.trim();
    // Re-enable after 10s as safety fallback
    setTimeout(function() { btn.disabled = false; btn.innerHTML = orig; }, 10000);
  });
});
</script>
@stack('scripts')
@stack('modals')

{{-- Session timeout toast --}}
<div aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
  <div id="sessionTimeoutToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
    <div class="d-flex">
      <div class="toast-body d-flex align-items-center gap-2">
        <i class="bi bi-clock-history flex-shrink-0" style="font-size:1.1rem"></i>
        <span>Sesi Anda akan berakhir dalam 2 menit. Simpan pekerjaan Anda.</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
<script>
(function() {
  // Laravel default session lifetime is 120 minutes; show toast 2 minutes before expiry (at 114 minutes)
  var SESSION_LIFETIME_MS = 7200000; // 120 min
  var WARN_BEFORE_MS     = 120000;  // 2 min
  var showAt = SESSION_LIFETIME_MS - WARN_BEFORE_MS; // 6840000 ms = 114 min

  setTimeout(function() {
    var toastEl = document.getElementById('sessionTimeoutToast');
    if (toastEl) {
      var toast = bootstrap.Toast.getOrCreateInstance(toastEl);
      toast.show();
    }
  }, showAt);
})();
</script>
</body>
</html>
