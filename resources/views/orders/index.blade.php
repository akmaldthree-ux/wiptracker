@extends('layouts.app')
@section('title','Order Produksi')
@section('page-title','Order Produksi')
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Order Produksi</li>
  </ol>
</nav>
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <div>
    <h5 class="mb-0 fw-bold">Daftar Order Produksi</h5>
    <p class="text-muted small mb-0">Kelola dan pantau semua order produksi</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    <div class="btn-group btn-group-sm">
      <button class="btn btn-outline-secondary view-btn active" data-view="list" title="Tampilan List"><i class="bi bi-list-ul"></i></button>
      <button class="btn btn-outline-secondary view-btn" data-view="calendar" title="Kalender"><i class="bi bi-calendar3"></i></button>
      <button class="btn btn-outline-secondary view-btn" data-view="gantt" title="Gantt Chart"><i class="bi bi-bar-chart-steps"></i></button>
    </div>
    @if(auth()->user()->isSupervisor() || auth()->user()->isPPIC())
    <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-circle me-2"></i>Buat Order Baru</a>
    @endif
  </div>
</div>

<!-- Filter -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-auto"><small class="text-muted fw-semibold"><i class="bi bi-funnel me-1"></i>Filter:</small></div>
      <div class="col-12 col-sm"><input type="text" name="search" class="form-control form-control-sm" placeholder="No. order / produk..." value="{{ request('search') }}"></div>
      <div class="col-12 col-sm">
        <select name="status" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          @foreach(['draft'=>'Draft','active'=>'Aktif','completed'=>'Selesai','on_hold'=>'Ditahan','cancelled'=>'Dibatalkan'] as $v=>$l)
          <option value="{{ $v }}" {{ request('status')==$v ? 'selected' : '' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-auto d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- ── LIST VIEW ── --}}
<div id="view-list" class="view-panel">
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>No. Order</th><th>Produk / Series</th><th>Target Tanggal</th><th class="d-mob-none">Total Qty</th><th>Progress</th><th>Status</th><th class="d-mob-none">Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($orders as $o)
        @php
          $progress = $o->getProgressPercentage();
          $overdue = $o->isOverdue();
          $daysLeft = now()->startOfDay()->diffInDays($o->target_date->startOfDay(), false);
          $nearDeadline = !$overdue && $daysLeft <= 3 && $o->status === 'active';
        @endphp
        <tr class="{{ $overdue ? 'table-danger' : ($nearDeadline ? 'table-warning' : '') }}">
          <td><a href="{{ route('orders.show',$o) }}" class="fw-bold text-primary text-decoration-none">{{ $o->order_no }}</a></td>
          <td><div class="fw-semibold">{{ $o->product->name }}</div><small class="text-muted">{{ optional($o->series)->name }}</small></td>
          <td>
            <span class="{{ $overdue ? 'text-danger fw-semibold' : ($nearDeadline ? 'text-warning fw-semibold' : '') }}">{{ $o->target_date->format('d M Y') }}</span>
            @if($overdue)<div><span class="badge bg-danger" style="font-size:.65rem">TERLAMBAT</span></div>
            @elseif($nearDeadline)<div><span class="badge bg-warning" style="font-size:.65rem">{{ $daysLeft <= 0 ? 'HARI INI' : $daysLeft.' HARI LAGI' }}</span></div>
            @endif
          </td>
          <td class="d-mob-none">{{ number_format($o->getTotalTargetQty()) }} pcs</td>
          <td style="min-width:120px">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1">
                <div class="progress-bar {{ $progress>=100?'bg-success':($progress>=50?'bg-primary':'bg-warning') }}" style="width:{{ $progress }}%"></div>
              </div>
              <small class="text-muted fw-semibold" style="min-width:32px">{{ $progress }}%</small>
            </div>
          </td>
          <td><span class="badge badge-{{ $o->status }} px-2 py-1">{{ $o->status_label }}</span></td>
          <td class="d-mob-none"><small class="text-muted">{{ $o->created_at->format('d/m/Y') }}<br>{{ $o->creator->name }}</small></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('orders.show',$o) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a>
              @if((auth()->user()->isSupervisor() || auth()->user()->isPPIC()) && $o->status === 'draft')
              <a href="{{ route('orders.edit',$o) }}" class="btn btn-sm btn-outline-secondary py-1">Edit</a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-clipboard-x"></i><p class="fw-semibold mb-1">Tidak ada order ditemukan</p><p>Coba ubah filter atau buat order baru</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($orders->hasPages())
  <div class="card-footer py-3">{{ $orders->appends(request()->query())->links() }}</div>
  @endif
</div>
</div>

{{-- ── CALENDAR VIEW ── --}}
<div id="view-calendar" class="view-panel" style="display:none">
  <div class="card">
    <div class="card-body p-0">
      <div id="calendarEl" style="padding:1rem"></div>
    </div>
  </div>
</div>

{{-- ── GANTT VIEW ── --}}
<div id="view-gantt" class="view-panel" style="display:none">
  <div class="d-sm-none alert alert-info mb-2 py-2 px-3" style="font-size:.82rem"><i class="bi bi-info-circle me-1"></i>Gantt Chart lebih baik dilihat di layar yang lebih lebar. Geser ke kanan untuk melihat seluruh timeline.</div>
  <div class="card">
    <div class="card-header d-flex align-items-center gap-2">
      <i class="bi bi-bar-chart-steps text-primary"></i>
      <span>Gantt Chart — Timeline Order Produksi</span>
      <small class="text-muted ms-auto">Skala: per hari</small>
    </div>
    <div class="card-body p-2 p-sm-3" style="overflow-x:auto;-webkit-overflow-scrolling:touch">
      @php
        $allOrders = $orders->getCollection();
      @endphp
      @if($allOrders->isEmpty())
        <div class="empty-state"><i class="bi bi-bar-chart-steps"></i><p class="fw-semibold mb-1">Tidak ada order untuk ditampilkan</p></div>
      @else
      @php
        $minDate = $allOrders->min(fn($o) => $o->created_at)->startOfDay();
        $maxDate = $allOrders->max(fn($o) => $o->target_date)->addDays(3);
        $totalDays = $minDate->diffInDays($maxDate) + 1;
        $today = now()->startOfDay();
        $todayOffset = max(0, $minDate->diffInDays($today));
        $statusColors = ['draft'=>'#94a3b8','active'=>'#00ADB5','completed'=>'#22c55e','on_hold'=>'#f59e0b','cancelled'=>'#ef4444'];
      @endphp

      {{-- Header: bulan + hari --}}
      <div style="display:flex;min-width:{{ max(900, $totalDays * 28) }}px">
        <div style="width:220px;flex-shrink:0"></div>
        <div style="flex:1;position:relative">
          {{-- Month labels --}}
          <div style="display:flex;height:22px;border-bottom:1px solid #e2e8f0">
            @php $cur = $minDate->copy(); $mStart = 0; @endphp
            @while($cur <= $maxDate)
              @php $daysInMonth = min($cur->daysInMonth - $cur->day + 1, $maxDate->diffInDays($cur) + 1); @endphp
              <div style="width:{{ $daysInMonth * 28 }}px;flex-shrink:0;font-size:.7rem;font-weight:700;color:#64748b;padding:3px 4px;overflow:hidden;border-right:1px solid #e2e8f0">
                {{ $cur->format('M Y') }}
              </div>
              @php $cur->addDays($daysInMonth) @endphp
            @endwhile
          </div>
          {{-- Day numbers --}}
          <div style="display:flex;height:20px;background:#fafbfd">
            @for($d = 0; $d < $totalDays; $d++)
              @php $dayDate = $minDate->copy()->addDays($d); $isToday = $dayDate->isToday(); $isWknd = $dayDate->isWeekend(); @endphp
              <div style="width:28px;flex-shrink:0;text-align:center;font-size:.62rem;color:{{ $isToday?'#00ADB5':($isWknd?'#ef4444':'#94a3b8') }};font-weight:{{ $isToday?'800':'400' }};border-right:1px solid #f1f5f9;padding-top:3px">
                {{ $dayDate->format('d') }}
              </div>
            @endfor
          </div>
        </div>
      </div>

      {{-- Rows per order --}}
      @foreach($allOrders as $o)
      @php
        $start     = $minDate->diffInDays($o->created_at->startOfDay());
        $end       = $minDate->diffInDays(\Carbon\Carbon::parse($o->target_date)->startOfDay());
        $barWidth  = max(1, $end - $start + 1) * 28;
        $barLeft   = $start * 28;
        $progress  = $o->getProgressPercentage();
        $color     = $statusColors[$o->status] ?? '#94a3b8';
        $overdue   = $o->isOverdue();
      @endphp
      <div style="display:flex;min-width:{{ max(900, $totalDays * 28) }}px;height:44px;border-bottom:1px solid #f1f5f9;align-items:center">
        <div style="width:220px;flex-shrink:0;padding:0 12px;overflow:hidden">
          <a href="{{ route('orders.show',$o) }}" class="text-decoration-none fw-semibold" style="font-size:.8rem;color:#0f172a">{{ $o->order_no }}</a>
          <div style="font-size:.7rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $o->product->name }}</div>
        </div>
        <div style="flex:1;position:relative;height:100%;background:{{ $loop->even ? '#fafbfd' : '#fff' }}">
          {{-- today line --}}
          <div style="position:absolute;top:0;bottom:0;left:{{ $todayOffset * 28 }}px;width:2px;background:rgba(0,173,181,.4);z-index:1"></div>
          {{-- bar --}}
          <div style="position:absolute;top:10px;left:{{ $barLeft }}px;width:{{ $barWidth }}px;height:24px;background:{{ $color }};border-radius:4px;opacity:.85;overflow:hidden" title="{{ $o->order_no }}: {{ $o->product->name }}">
            {{-- progress fill --}}
            <div style="height:100%;width:{{ $progress }}%;background:rgba(255,255,255,.35);border-radius:4px 0 0 4px"></div>
          </div>
          {{-- label --}}
          <div style="position:absolute;top:14px;left:{{ $barLeft + 6 }}px;font-size:.67rem;color:#fff;font-weight:700;white-space:nowrap;z-index:2;pointer-events:none">
            {{ $progress }}%{{ $overdue ? ' ⚠' : '' }}
          </div>
        </div>
      </div>
      @endforeach

      {{-- Legend --}}
      <div class="d-flex gap-3 mt-3 flex-wrap">
        @foreach(['draft'=>['#94a3b8','Draft'],'active'=>['#00ADB5','Aktif'],'completed'=>['#22c55e','Selesai'],'on_hold'=>['#f59e0b','Ditahan'],'cancelled'=>['#ef4444','Dibatalkan']] as $s=>[$c,$l])
        <div class="d-flex align-items-center gap-1"><div style="width:12px;height:12px;border-radius:2px;background:{{ $c }}"></div><small class="text-muted">{{ $l }}</small></div>
        @endforeach
        <div class="d-flex align-items-center gap-1"><div style="width:2px;height:14px;background:rgba(0,173,181,.6)"></div><small class="text-muted">Hari ini</small></div>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css' rel='stylesheet'>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
// View toggle
document.querySelectorAll('.view-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const v = this.dataset.view;
    document.querySelectorAll('.view-panel').forEach(p => p.style.display = 'none');
    document.getElementById('view-' + v).style.display = 'block';
    if (v === 'calendar' && !window._calInit) initCalendar();
  });
});

// FullCalendar
@php
$calEvents = $orders->getCollection()->map(function($o) {
  return [
    'id'    => $o->id,
    'title' => $o->order_no . ' — ' . $o->product->name,
    'start' => $o->created_at->toDateString(),
    'end'   => \Carbon\Carbon::parse($o->target_date)->addDay()->toDateString(),
    'url'   => route('orders.show', $o),
    'color' => match($o->status) { 'active'=>'#00ADB5', 'completed'=>'#22c55e', 'on_hold'=>'#f59e0b', 'cancelled'=>'#ef4444', default=>'#94a3b8' },
    'extendedProps' => ['status' => $o->status_label, 'progress' => $o->getProgressPercentage()],
  ];
})->values();
@endphp
const events = @json($calEvents);

function initCalendar() {
  window._calInit = true;
  const cal = new FullCalendar.Calendar(document.getElementById('calendarEl'), {
    initialView: 'dayGridMonth',
    locale: 'id',
    headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,listMonth' },
    events: events,
    eventClick(info) { info.jsEvent.preventDefault(); window.location = info.event.url; },
    eventDidMount(info) {
      const p = info.event.extendedProps;
      info.el.title = `${info.event.title}\nStatus: ${p.status}\nProgress: ${p.progress}%`;
    },
    buttonText: { today: 'Hari Ini', month: 'Bulan', list: 'List' },
    height: 'auto',
  });
  cal.render();
}
</script>
@endpush
