<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','DPIS') — DTHREE Production Integration System</title>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-light: #eff6ff;
  --accent: #f97316;
  --accent-light: #fff7ed;
  --sidebar-bg: #0f172a;
  --sidebar-hover: rgba(255,255,255,.07);
  --sidebar-active-bg: rgba(37,99,235,.18);
  --sidebar-active-border: #3b82f6;
  --sidebar-text: rgba(255,255,255,.55);
  --sidebar-text-active: #fff;
  --sidebar-w: 256px;
  --topbar-h: 62px;
  --radius: 12px;
  --radius-sm: 8px;
  --radius-xs: 6px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.04);
  --shadow: 0 4px 20px rgba(0,0,0,.07);
  --shadow-lg: 0 12px 40px rgba(0,0,0,.14);
  --border: #e2e8f0;
  --bg: #f1f5f9;
  --bg2: #f8fafc;
  --text: #0f172a;
  --text-muted: #64748b;
}

* { box-sizing: border-box; }
body {
  font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
  background: var(--bg); color: var(--text); margin: 0; font-size: .9rem;
  -webkit-font-smoothing: antialiased;
}

/* ────────────────────────────────
   SIDEBAR
──────────────────────────────── */
#sidebar {
  position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-w);
  background: var(--sidebar-bg);
  background-image: linear-gradient(180deg, rgba(37,99,235,.12) 0%, transparent 40%);
  z-index: 1040; transition: .3s cubic-bezier(.4,0,.2,1);
  overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column;
}
#sidebar::-webkit-scrollbar { width: 3px; }
#sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

.sidebar-brand {
  padding: 1rem 1.2rem;
  display: flex; flex-direction: column; align-items: center;
  border-bottom: 1px solid rgba(255,255,255,.08); flex-shrink: 0;
  gap: .3rem;
}
.brand-logo-img {
  width: 90px; height: auto;
  mix-blend-mode: screen;
  opacity: .92;
}
.brand-text .sub { color: rgba(255,255,255,.25); font-size: .6rem; letter-spacing: .4px; text-align: center; }

.nav-section-title {
  color: rgba(255,255,255,.2); font-size: .6rem; font-weight: 700;
  letter-spacing: 1.8px; text-transform: uppercase;
  padding: 1.2rem 1.2rem .35rem;
}
.sidebar-nav { flex: 1; padding: .4rem 0 1rem; }
.sidebar-nav .nav-link {
  display: flex; align-items: center; gap: .6rem;
  padding: .48rem .9rem; margin: 1px .6rem;
  color: var(--sidebar-text); border-radius: var(--radius-xs);
  font-size: .825rem; font-weight: 500; transition: all .15s ease;
  text-decoration: none; position: relative; line-height: 1.4;
}
.sidebar-nav .nav-link:hover {
  color: rgba(255,255,255,.9); background: var(--sidebar-hover);
}
.sidebar-nav .nav-link.active {
  color: #fff; background: var(--sidebar-active-bg); font-weight: 600;
}
.sidebar-nav .nav-link.active::before {
  content: ''; position: absolute; left: -0.6rem; top: 50%; transform: translateY(-50%);
  height: 60%; width: 3px; border-radius: 0 3px 3px 0; background: var(--sidebar-active-border);
}
.sidebar-nav .nav-link i { font-size: .9rem; width: 17px; text-align: center; flex-shrink: 0; }
.sidebar-nav .nav-link.active i { color: #60a5fa; }
.sidebar-nav .badge { font-size: .6rem; padding: .18em .5em; margin-left: auto; }
.nav-submenu { padding: 0; }
.nav-submenu .nav-link { padding: .4rem .9rem .4rem 2.6rem; font-size: .81rem; }
.collapse-arrow { transition: .25s; opacity: .45; margin-left: auto; font-size: .7rem; }
[aria-expanded="true"] .collapse-arrow { transform: rotate(180deg); }

/* ────────────────────────────────
   MAIN CONTENT
──────────────────────────────── */
#main-content { margin-left: var(--sidebar-w); min-height: 100vh; transition: .3s; display: flex; flex-direction: column; }

/* ────────────────────────────────
   TOPBAR
──────────────────────────────── */
.topbar {
  height: var(--topbar-h); background: #fff;
  border-bottom: 1px solid #edf0f7;
  display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
  position: sticky; top: 0; z-index: 1030;
  box-shadow: 0 1px 0 #edf0f7, 0 3px 12px rgba(15,23,42,.04);
  flex-shrink: 0;
}
.topbar .page-title { font-size: .975rem; font-weight: 700; color: var(--text); margin: 0; }
.topbar-right { display: flex; align-items: center; gap: .45rem; margin-left: auto; }
.icon-btn {
  position: relative; width: 36px; height: 36px; border: 1.5px solid var(--border);
  border-radius: var(--radius-xs); display: flex; align-items: center; justify-content: center;
  background: transparent; color: var(--text-muted); text-decoration: none; transition: all .15s; cursor: pointer;
}
.icon-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.notif-badge {
  position: absolute; top: -5px; right: -5px; background: #ef4444; color: #fff;
  border-radius: 50%; width: 17px; height: 17px; font-size: .58rem;
  display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid #fff;
}
.user-menu .dropdown-toggle {
  display: flex; align-items: center; gap: .45rem; background: transparent;
  border: 1.5px solid var(--border); border-radius: var(--radius-xs);
  padding: .3rem .6rem .3rem .35rem; cursor: pointer; color: var(--text); transition: all .15s;
}
.user-menu .dropdown-toggle:hover { border-color: var(--primary); background: var(--primary-light); }
.user-menu .dropdown-toggle::after { display: none; }
.avatar {
  width: 28px; height: 28px; border-radius: 7px;
  background: linear-gradient(135deg, var(--primary), #60a5fa);
  display: flex; align-items: center; justify-content: center; color: #fff;
  font-size: .68rem; font-weight: 700; flex-shrink: 0; letter-spacing: .5px;
}
.user-info .user-name { font-size: .79rem; font-weight: 600; line-height: 1.25; }
.user-info .user-role { font-size: .67rem; color: var(--text-muted); line-height: 1.25; }

/* ────────────────────────────────
   PAGE CONTENT
──────────────────────────────── */
.page-content { padding: 1.5rem; flex: 1; }

/* ────────────────────────────────
   CARDS
──────────────────────────────── */
.card {
  border: 1px solid #e8edf5; border-radius: var(--radius);
  box-shadow: var(--shadow-sm); transition: box-shadow .2s ease, transform .2s ease;
  background: #fff;
}
.card:hover { box-shadow: var(--shadow); }
.card-header {
  background: #fafbfd; border-bottom: 1px solid #edf0f7;
  padding: .85rem 1.25rem; font-weight: 600; font-size: .875rem; color: var(--text);
  border-radius: var(--radius) var(--radius) 0 0;
  display: flex; align-items: center;
}
.card-footer {
  background: #fafbfd; border-top: 1px solid #edf0f7;
  padding: .85rem 1.25rem; border-radius: 0 0 var(--radius) var(--radius);
}

/* ────────────────────────────────
   KPI CARDS
──────────────────────────────── */
.kpi-card {
  border-radius: var(--radius); padding: 1.35rem 1.4rem;
  color: #fff; position: relative; overflow: hidden; border: none !important;
  box-shadow: var(--shadow) !important; transition: transform .2s ease, box-shadow .2s ease;
}
.kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg) !important; }
.kpi-card::before {
  content: ''; position: absolute; top: -30px; right: -30px;
  width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,.08);
}
.kpi-card::after {
  content: ''; position: absolute; bottom: -40px; left: -20px;
  width: 100px; height: 100px; border-radius: 50%; background: rgba(0,0,0,.06);
}
.kpi-card .kpi-icon {
  position: absolute; right: 1rem; bottom: .75rem;
  font-size: 2.8rem; opacity: .12; z-index: 0;
}
.kpi-card .kpi-value { font-size: 1.9rem; font-weight: 800; line-height: 1; position: relative; z-index: 1; }
.kpi-card .kpi-label { font-size: .72rem; opacity: .85; margin-top: .3rem; font-weight: 500; letter-spacing: .4px; text-transform: uppercase; }
.kpi-card .kpi-change { font-size: .7rem; margin-top: .5rem; opacity: .8; display: flex; align-items: center; gap: .3rem; }
.kpi-blue    { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); }
.kpi-green   { background: linear-gradient(135deg, #065f46 0%, #10b981 100%); }
.kpi-orange  { background: linear-gradient(135deg, #c2410c 0%, #f97316 100%); }
.kpi-red     { background: linear-gradient(135deg, #991b1b 0%, #ef4444 100%); }
.kpi-purple  { background: linear-gradient(135deg, #4c1d95 0%, #8b5cf6 100%); }
.kpi-teal    { background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); }
.kpi-indigo  { background: linear-gradient(135deg, #312e81 0%, #6366f1 100%); }

/* ────────────────────────────────
   STATUS BADGES (order status)
──────────────────────────────── */
.badge-draft      { background: #f1f5f9; color: #475569; }
.badge-active     { background: #dbeafe; color: #1d4ed8; }
.badge-completed  { background: #d1fae5; color: #065f46; }
.badge-on_hold    { background: #fef3c7; color: #92400e; }
.badge-cancelled  { background: #fee2e2; color: #991b1b; }

/* ────────────────────────────────
   PIPELINE
──────────────────────────────── */
.pipeline-step { text-align: center; position: relative; min-width: 80px; }
.pipeline-step:not(:last-child)::after {
  content: ''; position: absolute; top: 21px; left: calc(50% + 24px);
  width: calc(100% - 48px); height: 2px;
  background: linear-gradient(90deg, #e2e8f0 0%, #e2e8f0 100%);
  z-index: 0;
}
.pipeline-dot {
  width: 44px; height: 44px; border-radius: 50%;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: .82rem; font-weight: 700; position: relative; z-index: 1; margin-bottom: .5rem;
  box-shadow: 0 2px 10px rgba(0,0,0,.1); transition: transform .2s;
}
.pipeline-dot:hover { transform: scale(1.08); }
.pipeline-dot.green  { background: #dcfce7; color: #15803d; border: 2px solid #22c55e; }
.pipeline-dot.yellow { background: #fef9c3; color: #a16207; border: 2px solid #eab308; }
.pipeline-dot.red    { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
.pipeline-dot.grey   { background: #f8fafc; color: #94a3b8; border: 2px solid #e2e8f0; }

/* ────────────────────────────────
   ALERTS
──────────────────────────────── */
.alert { border-radius: var(--radius-sm); border: none; font-size: .875rem; padding: .8rem 1rem; }
.alert-success { background: #f0fdf4; color: #15803d; border-left: 4px solid #22c55e !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-danger  { background: #fef2f2; color: #b91c1c; border-left: 4px solid #ef4444 !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-warning { background: #fffbeb; color: #92400e;  border-left: 4px solid #f59e0b !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-info    { background: #eff6ff; color: #1d4ed8;  border-left: 4px solid #3b82f6 !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }

/* ────────────────────────────────
   BUTTONS
──────────────────────────────── */
.btn { border-radius: var(--radius-xs); font-weight: 500; font-size: .84rem; transition: all .15s; letter-spacing: .1px; }
.btn-primary { background: var(--primary); border-color: var(--primary); }
.btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); box-shadow: 0 4px 14px rgba(37,99,235,.32); transform: translateY(-1px); }
.btn-success:hover { box-shadow: 0 4px 14px rgba(16,185,129,.3); transform: translateY(-1px); }
.btn-danger:hover  { box-shadow: 0 4px 14px rgba(239,68,68,.3); transform: translateY(-1px); }
.btn-warning { color: #fff !important; }
.btn-warning:hover { box-shadow: 0 4px 14px rgba(245,158,11,.3); color: #fff !important; transform: translateY(-1px); }
.btn-outline-primary:hover { background: var(--primary-light); transform: translateY(-1px); }
.btn-outline-secondary:hover { transform: translateY(-1px); }
.btn-sm { font-size: .78rem; padding: .3rem .65rem; }
.btn:active { transform: translateY(0) !important; }

/* ────────────────────────────────
   TABLES
──────────────────────────────── */
.table { font-size: .86rem; }
.table thead th {
  font-size: .7rem; text-transform: uppercase; letter-spacing: .7px;
  color: #94a3b8; font-weight: 700; border-bottom: 1px solid #edf0f7;
  padding: .7rem 1rem; background: #fafbfd; white-space: nowrap;
}
.table tbody td { padding: .78rem 1rem; vertical-align: middle; border-color: #f1f5f9; }
.table-hover tbody tr { transition: background .1s; }
.table-hover tbody tr:hover { background: #f8fafc; }
.table tbody tr:last-child td { border-bottom: 0; }

/* ────────────────────────────────
   FORM CONTROLS
──────────────────────────────── */
.form-control, .form-select {
  border-radius: var(--radius-xs); border-color: #dde3ed;
  font-size: .875rem; padding: .45rem .75rem; transition: all .15s;
  background: #fff;
}
.form-control:focus, .form-select:focus {
  border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); background: #fff;
}
.form-control-sm, .form-select-sm { font-size: .82rem; padding: .35rem .65rem; }
.form-label { font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: .35rem; }
.form-text { font-size: .77rem; }

/* ────────────────────────────────
   DROPDOWN MENUS
──────────────────────────────── */
.dropdown-menu {
  border: 1px solid #e2e8f0; border-radius: var(--radius-sm);
  box-shadow: var(--shadow-lg); font-size: .85rem; padding: .35rem;
}
.dropdown-item { border-radius: var(--radius-xs); padding: .45rem .75rem; transition: background .1s; }
.dropdown-item:hover { background: var(--primary-light); color: var(--primary); }
.dropdown-divider { border-color: #f1f5f9; }

/* ────────────────────────────────
   PROGRESS
──────────────────────────────── */
.progress { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; }
.progress-bar { border-radius: 3px; transition: width .6s ease; }

/* ────────────────────────────────
   BADGES
──────────────────────────────── */
.badge { font-weight: 600; letter-spacing: .2px; border-radius: 5px; }

/* Solid badges: white text */
.badge.bg-primary, .badge.bg-success, .badge.bg-danger,
.badge.bg-secondary, .badge.bg-dark, .badge.bg-info,
.badge.bg-warning { color: #fff !important; }

/* Light-bg badges: dark text */
.badge.bg-success.bg-opacity-15   { color: #065f46 !important; }
.badge.bg-warning.bg-opacity-15   { color: #92400e !important; background-color: rgba(245,158,11,.15) !important; }
.badge.bg-danger.bg-opacity-15    { color: #991b1b !important; }
.badge.bg-primary.bg-opacity-15   { color: #1d4ed8 !important; }
.badge.bg-secondary.bg-opacity-15 { color: #475569 !important; }
.badge.bg-info.bg-opacity-15      { color: #0369a1 !important; }

/* ────────────────────────────────
   LIST GROUPS
──────────────────────────────── */
.list-group-item { border-color: #f1f5f9; font-size: .875rem; }
.list-group-item-action:hover { background: #f8fafc; }
.list-group-flush .list-group-item:first-child { border-top: 0; }

/* ────────────────────────────────
   PAGINATION
──────────────────────────────── */
.pagination { gap: 2px; }
.page-link { border-radius: var(--radius-xs) !important; border-color: var(--border); color: var(--primary); font-size: .82rem; padding: .35rem .65rem; }
.page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

/* ────────────────────────────────
   EMPTY STATES
──────────────────────────────── */
.empty-state { text-align: center; padding: 3rem 1.5rem; color: var(--text-muted); }
.empty-state i { font-size: 2.5rem; opacity: .25; display: block; margin-bottom: .75rem; }
.empty-state p { margin: 0; font-size: .875rem; }

/* ────────────────────────────────
   MISC
──────────────────────────────── */
.text-muted { color: var(--text-muted) !important; }
a { color: var(--primary); }
.text-warning { color: #b45309 !important; }
.alert-warning { color: #92400e !important; }
.fw-semibold { font-weight: 600 !important; }
hr { border-color: #edf0f7; }

/* ────────────────────────────────
   MOBILE
──────────────────────────────── */
.overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1039; backdrop-filter: blur(3px); }
.overlay.show { display: block; }
@media (max-width: 992px) {
  #sidebar { transform: translateX(-100%); }
  #sidebar.show { transform: translateX(0); box-shadow: var(--shadow-lg); }
  #main-content { margin-left: 0; }
  .page-content { padding: 1rem; }
  .topbar { padding: 0 1rem; }
}
@media (max-width: 576px) {
  .page-content { padding: .75rem; }
  .kpi-card .kpi-value { font-size: 1.6rem; }
  .topbar .page-title { font-size: .88rem; }
  .user-info { display: none !important; }
  .table-responsive { border: 0; }
}

/* ────────────────────────────────
   STAT / SUMMARY MINI CARDS
──────────────────────────────── */
.stat-card {
  background: #fff; border: 1px solid #e8edf5; border-radius: var(--radius);
  padding: 1.1rem 1.25rem; text-align: center; box-shadow: var(--shadow-sm);
  transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: var(--shadow); transform: translateY(-1px); }
.stat-card .stat-value { font-size: 1.7rem; font-weight: 800; line-height: 1.1; }
.stat-card .stat-label { font-size: .72rem; color: var(--text-muted); margin-top: .2rem; font-weight: 500; text-transform: uppercase; letter-spacing: .4px; }

/* ────────────────────────────────
   PAGE HEADER
──────────────────────────────── */
.page-header { margin-bottom: 1.5rem; }
.page-header h4, .page-header h5 { font-weight: 700; margin-bottom: .2rem; line-height: 1.3; }

/* ────────────────────────────────
   TABLE ROW OVERDUE HIGHLIGHT
──────────────────────────────── */
.table-danger td { background: #fff5f5 !important; }
.table-warning td { background: #fffbeb !important; }

/* ────────────────────────────────
   SCROLLABLE X
──────────────────────────────── */
.overflow-x-auto { overflow-x: auto; }

/* ────────────────────────────────
   ANIMATIONS
──────────────────────────────── */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.page-content > * { animation: fadeInUp .22s ease both; }
.page-content > *:nth-child(2) { animation-delay: .04s; }
.page-content > *:nth-child(3) { animation-delay: .08s; }
.page-content > *:nth-child(4) { animation-delay: .11s; }
</style>
@stack('styles')
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
  <div class="sidebar-brand">
    <img src="https://mms.img.susercontent.com/a46d39218eb9dae49bf4abec2cd67815" alt="DTHREE" class="brand-logo-img">
    <div class="brand-text">
      <div class="sub">Production Integration System</div>
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

    <div class="nav-section-title">Bahan & Biaya</div>
    <a href="{{ route('bahan-baku.index') }}" class="nav-link {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}">
      <i class="bi bi-boxes"></i> Bahan Baku
      @php $lowStock = \App\Models\RawMaterial::whereRaw('current_stock < min_stock')->count(); @endphp
      @if($lowStock > 0)<span class="badge bg-danger ms-auto">{{ $lowStock }}</span>@endif
    </a>
    <a href="{{ route('budget.index') }}" class="nav-link {{ request()->routeIs('budget.*') ? 'active' : '' }}">
      <i class="bi bi-wallet2"></i> Budget & Biaya
    </a>

    <div class="nav-section-title">Laporan</div>
    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
      <i class="bi bi-file-bar-graph"></i> Laporan
    </a>

    <div class="nav-section-title">Data WIP</div>
    <a href="{{ route('wip.index') }}" class="nav-link {{ request()->routeIs('wip.index') || request()->routeIs('wip.show') ? 'active' : '' }}">
      <i class="bi bi-activity"></i> WIP Tracker
    </a>
    @if(auth()->user()->role === 'admin')
    <a href="{{ route('wip.create') }}" class="nav-link {{ request()->routeIs('wip.create') ? 'active' : '' }}">
      <i class="bi bi-pencil-square"></i> Input WIP Manual
    </a>
    @endif

    @if(in_array(auth()->user()->role ?? '', ['admin','supervisor']))
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
      </div>
    </div>
    @endif

    @if(auth()->user() && auth()->user()->role === 'admin')
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
  <div class="px-4 pt-3" style="margin-bottom:-1rem">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
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
    <div class="alert alert-danger">
      <div class="d-flex align-items-center gap-2 mb-1">
        <i class="bi bi-exclamation-triangle-fill"></i><strong>Terdapat kesalahan:</strong>
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
@stack('scripts')
</body>
</html>
