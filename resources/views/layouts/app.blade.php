<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','DPIS') — DTHREE Production Integration System</title>
<link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{--primary:#1a3c6e;--primary-dark:#0d2444;--accent:#e85d04;--sidebar-w:260px;--topbar-h:64px}
*{box-sizing:border-box}
body{font-family:'Segoe UI',system-ui,sans-serif;background:#f0f4f8;color:#2d3748;margin:0}
/* Sidebar */
#sidebar{position:fixed;top:0;left:0;height:100vh;width:var(--sidebar-w);background:linear-gradient(180deg,var(--primary-dark) 0%,var(--primary) 100%);z-index:1040;transition:.3s;overflow-y:auto;overflow-x:hidden}
#sidebar::-webkit-scrollbar{width:4px}
#sidebar::-webkit-scrollbar-track{background:transparent}
#sidebar::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:2px}
.sidebar-brand{padding:1.25rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.1);display:flex;align-items:center;gap:.75rem}
.brand-icon{width:40px;height:40px;background:linear-gradient(135deg,var(--accent),#f48c06);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;color:#fff;flex-shrink:0}
.brand-text .name{color:#fff;font-weight:700;font-size:.95rem;letter-spacing:.5px}
.brand-text .sub{color:rgba(255,255,255,.5);font-size:.68rem}
.nav-section-title{color:rgba(255,255,255,.4);font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.75rem 1.5rem .25rem;margin-top:.5rem}
.sidebar-nav .nav-link{display:flex;align-items:center;gap:.75rem;padding:.6rem 1.5rem;color:rgba(255,255,255,.7);border-radius:0;font-size:.875rem;transition:.2s;border-left:3px solid transparent}
.sidebar-nav .nav-link:hover{color:#fff;background:rgba(255,255,255,.08);border-left-color:rgba(255,255,255,.3)}
.sidebar-nav .nav-link.active{color:#fff;background:rgba(255,255,255,.12);border-left-color:var(--accent)}
.sidebar-nav .nav-link i{font-size:1rem;width:20px;text-align:center;flex-shrink:0}
.sidebar-nav .badge{font-size:.65rem;padding:.2em .5em}
.sidebar-collapse{padding:1.5rem;margin-top:auto;border-top:1px solid rgba(255,255,255,.1)}
/* Main */
#main-content{margin-left:var(--sidebar-w);min-height:100vh;transition:.3s}
/* Topbar */
.topbar{height:var(--topbar-h);background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;padding:0 1.5rem;gap:1rem;position:sticky;top:0;z-index:1030;box-shadow:0 1px 4px rgba(0,0,0,.06)}
.topbar .page-title{font-size:1.1rem;font-weight:600;color:var(--primary);margin:0}
.topbar .breadcrumb{font-size:.78rem;margin:0}
.topbar-right{display:flex;align-items:center;gap:.75rem;margin-left:auto}
.notif-btn{position:relative;width:38px;height:38px;border:1.5px solid #e2e8f0;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#fff;color:#64748b;text-decoration:none;transition:.2s}
.notif-btn:hover{border-color:var(--primary);color:var(--primary)}
.notif-badge{position:absolute;top:-4px;right:-4px;background:var(--accent);color:#fff;border-radius:50%;width:18px;height:18px;font-size:.6rem;display:flex;align-items:center;justify-content:center;font-weight:700}
.user-menu .dropdown-toggle{display:flex;align-items:center;gap:.5rem;background:none;border:1.5px solid #e2e8f0;border-radius:10px;padding:.35rem .75rem;cursor:pointer;color:#2d3748;transition:.2s}
.user-menu .dropdown-toggle:hover{border-color:var(--primary);color:var(--primary)}
.avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--primary),#2557a7);display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700}
/* Content */
.page-content{padding:1.5rem}
/* Cards */
.card{border:none;border-radius:12px;box-shadow:0 1px 4px rgba(0,0,0,.06);transition:.2s}
.card:hover{box-shadow:0 4px 12px rgba(0,0,0,.1)}
.card-header{background:transparent;border-bottom:1px solid #f1f5f9;padding:.875rem 1.25rem;font-weight:600}
/* KPI Cards */
.kpi-card{border-radius:12px;padding:1.5rem;color:#fff;position:relative;overflow:hidden;border:none}
.kpi-card .kpi-icon{position:absolute;right:-10px;top:-10px;font-size:5rem;opacity:.12}
.kpi-card .kpi-value{font-size:2.2rem;font-weight:800;line-height:1.1}
.kpi-card .kpi-label{font-size:.8rem;opacity:.85;margin-top:.25rem}
.kpi-card .kpi-change{font-size:.75rem;margin-top:.5rem;opacity:.9}
.kpi-blue{background:linear-gradient(135deg,#1a3c6e,#2557a7)}
.kpi-green{background:linear-gradient(135deg,#065f46,#059669)}
.kpi-orange{background:linear-gradient(135deg,#c2410c,var(--accent))}
.kpi-red{background:linear-gradient(135deg,#991b1b,#dc2626)}
.kpi-purple{background:linear-gradient(135deg,#4c1d95,#7c3aed)}
.kpi-teal{background:linear-gradient(135deg,#0f766e,#0d9488)}
/* Status badges */
.badge-draft{background:#e2e8f0;color:#475569}
.badge-active{background:#dbeafe;color:#1d4ed8}
.badge-completed{background:#d1fae5;color:#065f46}
.badge-on_hold{background:#fef3c7;color:#92400e}
.badge-cancelled{background:#fee2e2;color:#991b1b}
/* Progress bars */
.progress{height:8px;border-radius:4px;background:#e2e8f0}
.progress-bar{border-radius:4px}
/* Table */
.table-hover tbody tr:hover{background:#f8fafc}
.table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.5px;color:#64748b;font-weight:600;border-bottom:2px solid #e2e8f0;padding:.75rem 1rem}
.table tbody td{padding:.75rem 1rem;vertical-align:middle;border-color:#f1f5f9}
/* Station pipeline */
.pipeline-step{text-align:center;position:relative}
.pipeline-step:not(:last-child)::after{content:'';position:absolute;top:20px;left:calc(50% + 20px);width:calc(100% - 40px);height:2px;background:#e2e8f0;z-index:0}
.pipeline-dot{width:40px;height:40px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;position:relative;z-index:1;margin-bottom:.5rem}
.pipeline-dot.green{background:#d1fae5;color:#065f46;border:2px solid #059669}
.pipeline-dot.yellow{background:#fef3c7;color:#92400e;border:2px solid #d97706}
.pipeline-dot.red{background:#fee2e2;color:#991b1b;border:2px solid #dc2626}
.pipeline-dot.grey{background:#f1f5f9;color:#94a3b8;border:2px solid #e2e8f0}
/* Alert styling */
.alert{border-radius:10px;border:none}
.alert-success{background:#d1fae5;color:#065f46}
.alert-danger{background:#fee2e2;color:#991b1b}
.alert-warning{background:#fef3c7;color:#92400e}
.alert-info{background:#dbeafe;color:#1d4ed8}
/* Buttons */
.btn{border-radius:8px;font-weight:500;font-size:.875rem}
.btn-primary{background:var(--primary);border-color:var(--primary)}
.btn-primary:hover{background:var(--primary-dark);border-color:var(--primary-dark)}
/* Sidebar toggle for mobile */
@media(max-width:992px){
  #sidebar{transform:translateX(-100%)}
  #sidebar.show{transform:translateX(0)}
  #main-content{margin-left:0}
  .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1039}
  .overlay.show{display:block}
}
/* Submenu */
.nav-submenu{padding-left:2.75rem}
.nav-submenu .nav-link{padding:.4rem 1rem;font-size:.825rem}
.collapse-arrow{transition:.3s}
[aria-expanded="true"] .collapse-arrow{transform:rotate(180deg)}
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
  <nav class="sidebar-nav mt-2">
    <div class="nav-section-title">Utama</div>
    <a href="{{ route('dashboard.executive') }}" class="nav-link {{ request()->routeIs('dashboard.executive') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard Eksekutif</a>
    <a href="{{ route('dashboard.operational') }}" class="nav-link {{ request()->routeIs('dashboard.operational') ? 'active' : '' }}"><i class="bi bi-display"></i> Dashboard Operasional</a>

    <div class="nav-section-title">Produksi</div>
    <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}"><i class="bi bi-clipboard-check"></i> Order Produksi</a>
    <a href="{{ route('wip.index') }}" class="nav-link {{ request()->routeIs('wip.*') ? 'active' : '' }}"><i class="bi bi-activity"></i> WIP Tracker</a>
    <a href="{{ route('handover.index') }}" class="nav-link {{ request()->routeIs('handover.*') ? 'active' : '' }}">
      <i class="bi bi-arrow-left-right"></i> Handover
      @php $pending = \App\Models\Handover::where('status','pending')->count(); @endphp
      @if($pending > 0)<span class="badge bg-warning text-dark ms-auto">{{ $pending }}</span>@endif
    </a>

    <div class="nav-section-title">Bahan & Biaya</div>
    <a href="{{ route('bahan-baku.index') }}" class="nav-link {{ request()->routeIs('bahan-baku.*') ? 'active' : '' }}">
      <i class="bi bi-boxes"></i> Bahan Baku
      @php $lowStock = \App\Models\RawMaterial::whereRaw('current_stock < min_stock')->count(); @endphp
      @if($lowStock > 0)<span class="badge bg-danger ms-auto">{{ $lowStock }}</span>@endif
    </a>
    <a href="{{ route('budget.index') }}" class="nav-link {{ request()->routeIs('budget.*') ? 'active' : '' }}"><i class="bi bi-wallet2"></i> Budget & Biaya</a>

    <div class="nav-section-title">Laporan</div>
    <a href="{{ route('laporan.index') }}" class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}"><i class="bi bi-file-bar-graph"></i> Laporan</a>

    @if(in_array(auth()->user()->role ?? '', ['admin','supervisor']))
    <div class="nav-section-title">Master Data</div>
    <a class="nav-link {{ request()->routeIs('master.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#masterMenu" role="button" aria-expanded="{{ request()->routeIs('master.*') ? 'true':'false' }}">
      <i class="bi bi-database"></i> Master Data <i class="bi bi-chevron-down ms-auto collapse-arrow"></i>
    </a>
    <div class="collapse {{ request()->routeIs('master.*') ? 'show' : '' }}" id="masterMenu">
      <div class="nav-submenu">
        <a href="{{ route('master.produk.index') }}" class="nav-link {{ request()->routeIs('master.produk.*') ? 'active' : '' }}"><i class="bi bi-tag"></i> Produk</a>
        <a href="{{ route('master.series.index') }}" class="nav-link {{ request()->routeIs('master.series.*') ? 'active' : '' }}"><i class="bi bi-collection"></i> Series</a>
        <a href="{{ route('master.warna.index') }}" class="nav-link {{ request()->routeIs('master.warna.*') ? 'active' : '' }}"><i class="bi bi-palette"></i> Warna</a>
        <a href="{{ route('master.ukuran.index') }}" class="nav-link {{ request()->routeIs('master.ukuran.*') ? 'active' : '' }}"><i class="bi bi-rulers"></i> Ukuran</a>
        <a href="{{ route('master.stasiun.index') }}" class="nav-link {{ request()->routeIs('master.stasiun.*') ? 'active' : '' }}"><i class="bi bi-geo-alt"></i> Stasiun</a>
        <a href="{{ route('master.sewing-location.index') }}" class="nav-link {{ request()->routeIs('master.sewing-location.*') ? 'active' : '' }}"><i class="bi bi-building"></i> Tempat Sewing</a>
      </div>
    </div>
    @endif

    @if(auth()->user() && auth()->user()->role === 'admin')
    <div class="nav-section-title">Administrasi</div>
    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Manajemen User</a>
    @endif

    <div class="nav-section-title">Akun</div>
    <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
      <i class="bi bi-bell"></i> Notifikasi
      @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
      @if($unread > 0)<span class="badge bg-danger ms-auto">{{ $unread }}</span>@endif
    </a>
    <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Profil Saya</a>
  </nav>
</div>

<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Main Content -->
<div id="main-content">
  <!-- Topbar -->
  <div class="topbar">
    <button class="btn btn-sm p-1 border-0 d-lg-none me-2" onclick="toggleSidebar()" style="font-size:1.3rem;color:var(--primary)"><i class="bi bi-list"></i></button>
    <div>
      <h6 class="page-title">@yield('page-title','Dashboard')</h6>
      @hasSection('breadcrumb')
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">@yield('breadcrumb')</ol>
      </nav>
      @endif
    </div>
    <div class="topbar-right">
      <a href="{{ route('notifications.index') }}" class="notif-btn">
        <i class="bi bi-bell" style="font-size:1.05rem"></i>
        @if(isset($unread) && $unread > 0)<span class="notif-badge">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
      </a>
      <div class="user-menu dropdown">
        <button class="dropdown-toggle" data-bs-toggle="dropdown">
          <div class="avatar">{{ strtoupper(substr(auth()->user()->name,0,2)) }}</div>
          <div class="d-none d-md-block text-start">
            <div style="font-size:.8rem;font-weight:600">{{ auth()->user()->name }}</div>
            <div style="font-size:.7rem;color:#64748b">{{ auth()->user()->role_label }}</div>
          </div>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:10px;min-width:180px">
          <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form method="POST" action="{{ route('logout') }}">
              @csrf<button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Page Content -->
  <div class="page-content">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('warning'))
      <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i><span>{{ session('warning') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-x-circle-fill"></i><span>{{ session('error') }}</span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terdapat kesalahan:</strong>
        <ul class="mb-0 mt-1">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
      </div>
    @endif
    @yield('content')
  </div>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
<script src="/js/chart.umd.min.js"></script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('overlay').classList.remove('show');
}
</script>
@stack('scripts')
</body>
</html>
