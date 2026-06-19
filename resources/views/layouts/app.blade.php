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
  --sidebar-active-bg: rgba(37,99,235,.25);
  --sidebar-active-border: #2563eb;
  --sidebar-text: rgba(255,255,255,.6);
  --sidebar-text-active: #fff;
  --sidebar-w: 260px;
  --topbar-h: 60px;
  --radius: 12px;
  --radius-sm: 8px;
  --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
  --shadow: 0 4px 16px rgba(0,0,0,.08);
  --shadow-lg: 0 10px 40px rgba(0,0,0,.12);
  --border: #e2e8f0;
  --bg: #f1f5f9;
  --text: #0f172a;
  --text-muted: #64748b;
}
* { box-sizing: border-box; }
body { font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: var(--text); margin: 0; font-size: .9rem; }

/* ── Sidebar ── */
#sidebar {
  position: fixed; top: 0; left: 0; height: 100vh; width: var(--sidebar-w);
  background: var(--sidebar-bg); z-index: 1040; transition: .3s cubic-bezier(.4,0,.2,1);
  overflow-y: auto; overflow-x: hidden; display: flex; flex-direction: column;
}
#sidebar::-webkit-scrollbar { width: 3px; }
#sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

.sidebar-brand {
  padding: 1.25rem 1.25rem 1rem;
  display: flex; align-items: center; gap: .75rem;
  border-bottom: 1px solid rgba(255,255,255,.06);
  flex-shrink: 0;
}
.brand-icon {
  width: 38px; height: 38px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--primary), #60a5fa);
  border-radius: 10px; display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,.4);
}
.brand-text .name { color: #fff; font-weight: 700; font-size: .92rem; letter-spacing: .3px; }
.brand-text .sub { color: rgba(255,255,255,.35); font-size: .65rem; margin-top: 1px; }

.nav-section-title {
  color: rgba(255,255,255,.25); font-size: .62rem; font-weight: 700;
  letter-spacing: 1.5px; text-transform: uppercase;
  padding: 1.25rem 1.25rem .4rem; margin-top: .25rem;
}
.sidebar-nav { flex: 1; padding: .5rem 0; }
.sidebar-nav .nav-link {
  display: flex; align-items: center; gap: .65rem;
  padding: .5rem 1rem; margin: 1px .75rem;
  color: var(--sidebar-text); border-radius: var(--radius-sm);
  font-size: .835rem; font-weight: 500; transition: all .15s ease;
  text-decoration: none; position: relative;
}
.sidebar-nav .nav-link:hover {
  color: #fff; background: var(--sidebar-hover);
}
.sidebar-nav .nav-link.active {
  color: var(--sidebar-text-active); background: var(--sidebar-active-bg);
  font-weight: 600;
}
.sidebar-nav .nav-link.active::before {
  content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
  width: 3px; border-radius: 0 3px 3px 0; background: var(--primary);
}
.sidebar-nav .nav-link i { font-size: .95rem; width: 18px; text-align: center; flex-shrink: 0; opacity: .8; }
.sidebar-nav .nav-link.active i { opacity: 1; color: #60a5fa; }
.sidebar-nav .badge { font-size: .62rem; padding: .2em .5em; margin-left: auto; }
.nav-submenu { padding: 0; }
.nav-submenu .nav-link { padding: .4rem 1rem .4rem 2.75rem; font-size: .815rem; }
.collapse-arrow { transition: .25s; opacity: .5; margin-left: auto; }
[aria-expanded="true"] .collapse-arrow { transform: rotate(180deg); }

/* ── Main ── */
#main-content { margin-left: var(--sidebar-w); min-height: 100vh; transition: .3s; display: flex; flex-direction: column; }

/* ── Topbar ── */
.topbar {
  height: var(--topbar-h); background: #fff; border-bottom: 1px solid var(--border);
  display: flex; align-items: center; padding: 0 1.5rem; gap: 1rem;
  position: sticky; top: 0; z-index: 1030;
  box-shadow: 0 1px 0 #e2e8f0, 0 2px 8px rgba(0,0,0,.04);
  flex-shrink: 0;
}
.topbar .page-title { font-size: 1rem; font-weight: 700; color: var(--text); margin: 0; }
.topbar-right { display: flex; align-items: center; gap: .5rem; margin-left: auto; }
.icon-btn {
  position: relative; width: 36px; height: 36px; border: 1.5px solid var(--border);
  border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;
  background: #fff; color: var(--text-muted); text-decoration: none; transition: all .15s;
  cursor: pointer;
}
.icon-btn:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-light); }
.notif-badge {
  position: absolute; top: -5px; right: -5px; background: #ef4444; color: #fff;
  border-radius: 50%; width: 17px; height: 17px; font-size: .58rem;
  display: flex; align-items: center; justify-content: center; font-weight: 700;
  border: 2px solid #fff;
}
.user-menu .dropdown-toggle {
  display: flex; align-items: center; gap: .5rem; background: #fff;
  border: 1.5px solid var(--border); border-radius: var(--radius-sm);
  padding: .3rem .6rem .3rem .4rem; cursor: pointer; color: var(--text); transition: all .15s;
}
.user-menu .dropdown-toggle:hover { border-color: var(--primary); background: var(--primary-light); }
.user-menu .dropdown-toggle::after { display: none; }
.avatar {
  width: 28px; height: 28px; border-radius: 8px;
  background: linear-gradient(135deg, var(--primary), #60a5fa);
  display: flex; align-items: center; justify-content: center; color: #fff;
  font-size: .7rem; font-weight: 700; flex-shrink: 0;
}
.user-info .user-name { font-size: .8rem; font-weight: 600; line-height: 1.2; }
.user-info .user-role { font-size: .68rem; color: var(--text-muted); line-height: 1.2; }

/* ── Page Content ── */
.page-content { padding: 1.5rem; flex: 1; }

/* ── Cards ── */
.card {
  border: 1px solid #e8edf3; border-radius: var(--radius);
  box-shadow: var(--shadow-sm); transition: box-shadow .2s, transform .2s;
  background: #fff;
}
.card:hover { box-shadow: var(--shadow); }
.card-header {
  background: transparent; border-bottom: 1px solid #f1f5f9;
  padding: .875rem 1.25rem; font-weight: 600; font-size: .875rem; color: var(--text);
}
.card-footer { background: transparent; border-top: 1px solid #f1f5f9; padding: .875rem 1.25rem; }

/* ── KPI Cards ── */
.kpi-card {
  border-radius: var(--radius); padding: 1.4rem 1.5rem;
  color: #fff; position: relative; overflow: hidden; border: none !important;
  box-shadow: var(--shadow) !important;
}
.kpi-card::after {
  content: ''; position: absolute; right: -20px; top: -20px;
  width: 100px; height: 100px; border-radius: 50%;
  background: rgba(255,255,255,.08);
}
.kpi-card .kpi-icon { position: absolute; right: 1rem; bottom: .75rem; font-size: 3rem; opacity: .15; }
.kpi-card .kpi-value { font-size: 2rem; font-weight: 800; line-height: 1; }
.kpi-card .kpi-label { font-size: .75rem; opacity: .8; margin-top: .35rem; font-weight: 500; letter-spacing: .3px; }
.kpi-card .kpi-change { font-size: .72rem; margin-top: .5rem; opacity: .85; }
.kpi-blue    { background: linear-gradient(135deg, #1e40af, #3b82f6); }
.kpi-green   { background: linear-gradient(135deg, #065f46, #10b981); }
.kpi-orange  { background: linear-gradient(135deg, #c2410c, #f97316); }
.kpi-red     { background: linear-gradient(135deg, #991b1b, #ef4444); }
.kpi-purple  { background: linear-gradient(135deg, #4c1d95, #8b5cf6); }
.kpi-teal    { background: linear-gradient(135deg, #0f766e, #14b8a6); }
.kpi-indigo  { background: linear-gradient(135deg, #312e81, #6366f1); }

/* ── Status badges ── */
.badge-draft      { background: #f1f5f9; color: #475569; }
.badge-active     { background: #dbeafe; color: #1d4ed8; }
.badge-completed  { background: #d1fae5; color: #065f46; }
.badge-on_hold    { background: #fef3c7; color: #92400e; }
.badge-cancelled  { background: #fee2e2; color: #991b1b; }

/* ── Pipeline ── */
.pipeline-step { text-align: center; position: relative; }
.pipeline-step:not(:last-child)::after {
  content: ''; position: absolute; top: 20px; left: calc(50% + 22px);
  width: calc(100% - 44px); height: 2px; background: #e2e8f0; z-index: 0;
}
.pipeline-dot {
  width: 44px; height: 44px; border-radius: 50%;
  display: inline-flex; align-items: center; justify-content: center;
  font-size: .8rem; font-weight: 700; position: relative; z-index: 1; margin-bottom: .5rem;
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
}
.pipeline-dot.green  { background: #d1fae5; color: #065f46; border: 2px solid #10b981; }
.pipeline-dot.yellow { background: #fef3c7; color: #92400e; border: 2px solid #f59e0b; }
.pipeline-dot.red    { background: #fee2e2; color: #991b1b; border: 2px solid #ef4444; }
.pipeline-dot.grey   { background: #f8fafc; color: #94a3b8; border: 2px solid #e2e8f0; }

/* ── Alerts ── */
.alert { border-radius: var(--radius-sm); border: none; font-size: .875rem; }
.alert-success { background: #f0fdf4; color: #15803d; border-left: 4px solid #22c55e !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-danger  { background: #fef2f2; color: #b91c1c; border-left: 4px solid #ef4444 !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-warning { background: #fffbeb; color: #92400e; border-left: 4px solid #f59e0b !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }
.alert-info    { background: #eff6ff; color: #1d4ed8; border-left: 4px solid #3b82f6 !important; border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important; }

/* ── Buttons ── */
.btn { border-radius: var(--radius-sm); font-weight: 500; font-size: .84rem; transition: all .15s; }
.btn-primary { background: var(--primary); border-color: var(--primary); }
.btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); box-shadow: 0 4px 12px rgba(37,99,235,.3); }
.btn-success:hover { box-shadow: 0 4px 12px rgba(16,185,129,.3); }
.btn-danger:hover  { box-shadow: 0 4px 12px rgba(239,68,68,.3); }
.btn-warning { color: #fff; }
.btn-warning:hover { box-shadow: 0 4px 12px rgba(245,158,11,.3); color: #fff; }
.btn-outline-primary:hover { background: var(--primary-light); }
.btn-sm { font-size: .78rem; padding: .3rem .65rem; }

/* ── Tables ── */
.table { font-size: .865rem; }
.table thead th {
  font-size: .72rem; text-transform: uppercase; letter-spacing: .6px;
  color: var(--text-muted); font-weight: 600; border-bottom: 2px solid #f1f5f9;
  padding: .75rem 1rem; background: #fafafa;
}
.table tbody td { padding: .75rem 1rem; vertical-align: middle; border-color: #f8fafc; }
.table-hover tbody tr { transition: background .1s; }
.table-hover tbody tr:hover { background: #f8fafc; }

/* ── Form controls ── */
.form-control, .form-select {
  border-radius: var(--radius-sm); border-color: var(--border);
  font-size: .875rem; padding: .45rem .75rem; transition: all .15s;
}
.form-control:focus, .form-select:focus {
  border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.form-label { font-size: .82rem; font-weight: 600; color: #374151; margin-bottom: .35rem; }

/* ── Dropdown menus ── */
.dropdown-menu {
  border: 1px solid #e2e8f0; border-radius: var(--radius-sm);
  box-shadow: var(--shadow-lg); font-size: .85rem; padding: .35rem;
}
.dropdown-item { border-radius: 6px; padding: .45rem .75rem; transition: background .1s; }
.dropdown-item:hover { background: var(--primary-light); color: var(--primary); }

/* ── Progress ── */
.progress { height: 6px; border-radius: 3px; background: #e2e8f0; }
.progress-bar { border-radius: 3px; }

/* ── Badges ── */
.badge { font-weight: 600; }

/* ── Pagination ── */
.pagination { gap: 2px; }
.page-link { border-radius: 6px !important; border-color: var(--border); color: var(--primary); font-size: .82rem; padding: .35rem .65rem; }
.page-item.active .page-link { background: var(--primary); border-color: var(--primary); }

/* ── Misc ── */
.text-muted { color: var(--text-muted) !important; }
a { color: var(--primary); }

/* ── Fix warning color contrast (Bootstrap yellow is near-invisible on white) ── */
.text-warning { color: #b45309 !important; }
.alert-warning { color: #92400e !important; }

/* ── Solid badges: always white text ── */
.badge.bg-primary,
.badge.bg-success,
.badge.bg-danger,
.badge.bg-secondary,
.badge.bg-dark,
.badge.bg-info,
.badge.bg-warning { color: #fff !important; }

/* ── Warning button: white text ── */
.btn-warning, .btn-warning:hover { color: #fff !important; }

/* ── Light-bg badges (bg-opacity-15): dark text for contrast ── */
.badge.bg-success.bg-opacity-15   { color: #065f46 !important; }
.badge.bg-warning.bg-opacity-15   { color: #92400e !important; background-color: rgba(245,158,11,.15) !important; }
.badge.bg-danger.bg-opacity-15    { color: #991b1b !important; }
.badge.bg-primary.bg-opacity-15   { color: #1d4ed8 !important; }
.badge.bg-secondary.bg-opacity-15 { color: #475569 !important; }
.badge.bg-info.bg-opacity-15      { color: #0369a1 !important; }

/* ── Mobile ── */
.overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 1039; backdrop-filter: blur(2px); }
.overlay.show { display: block; }
@media (max-width: 992px) {
  #sidebar { transform: translateX(-100%); }
  #sidebar.show { transform: translateX(0); box-shadow: var(--shadow-lg); }
  #main-content { margin-left: 0; }
}

/* ── Animations ── */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.page-content > * { animation: fadeInUp .25s ease both; }
</style>
@stack('styles')
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-icon"><i class="bi bi-factory"></i></div>
    <div class="brand-text">
      <div class="name">DPIS</div>
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
