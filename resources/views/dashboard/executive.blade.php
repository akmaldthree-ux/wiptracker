@extends('layouts.app')
@section('title','Dashboard Eksekutif')
@section('page-title','Dashboard Eksekutif')
@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection
@section('content')
<!-- KPI Row -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-blue">
      <div class="kpi-icon"><i class="bi bi-clipboard-check"></i></div>
      <div class="kpi-value">{{ $totalActiveOrders }}</div>
      <div class="kpi-label">Order Aktif</div>
      <div class="kpi-change"><i class="bi bi-arrow-up-right"></i>Sedang berjalan</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-teal">
      <div class="kpi-icon"><i class="bi bi-layers"></i></div>
      <div class="kpi-value">{{ number_format($totalWipUnits) }}</div>
      <div class="kpi-label">Unit Dalam Proses</div>
      <div class="kpi-change"><i class="bi bi-activity"></i>Total WIP aktif</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card kpi-green">
      <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
      <div class="kpi-value">{{ number_format($todayThroughput) }}</div>
      <div class="kpi-label">Throughput Hari Ini</div>
      <div class="kpi-change"><i class="bi bi-calendar-day"></i>Unit selesai hari ini</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card {{ $overdueOrders > 0 ? 'kpi-red' : 'kpi-green' }}">
      <div class="kpi-icon"><i class="bi bi-alarm"></i></div>
      <div class="kpi-value">{{ $overdueOrders }}</div>
      <div class="kpi-label">Order Terlambat</div>
      <div class="kpi-change"><i class="bi bi-{{ $overdueOrders > 0 ? 'exclamation-triangle' : 'check-circle' }}"></i>Melewati target</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card {{ $pendingHandovers > 0 ? 'kpi-orange' : 'kpi-green' }}">
      <div class="kpi-icon"><i class="bi bi-arrow-left-right"></i></div>
      <div class="kpi-value">{{ $pendingHandovers }}</div>
      <div class="kpi-label">Handover Pending</div>
      <div class="kpi-change"><i class="bi bi-clock"></i>Perlu konfirmasi</div>
    </div>
  </div>
  <div class="col-6 col-md-4 col-xl-2">
    <div class="kpi-card {{ $lowStockMaterials > 0 ? 'kpi-red' : 'kpi-green' }}">
      <div class="kpi-icon"><i class="bi bi-boxes"></i></div>
      <div class="kpi-value">{{ $lowStockMaterials }}</div>
      <div class="kpi-label">Stok Kritis</div>
      <div class="kpi-change"><i class="bi bi-{{ $lowStockMaterials > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>Di bawah minimum</div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Pipeline Status -->
  <div class="col-12">
    <div class="card">
      <div class="card-header justify-content-between">
        <span><i class="bi bi-diagram-3 me-2 text-primary"></i>Status Pipeline Produksi</span>
        <div class="d-flex align-items-center gap-2 ms-auto">
          <span class="badge" style="background:#dcfce7;color:#15803d;font-size:.68rem">Normal</span>
          <span class="badge" style="background:#fef9c3;color:#a16207;font-size:.68rem">Mendekati</span>
          <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:.68rem">Bottleneck</span>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="d-flex align-items-start gap-0 overflow-x-auto pb-1">
          @foreach($stationWip as $s)
          @php $pct = $s['threshold'] > 0 ? min(100,($s['wip']/$s['threshold'])*100) : 0; @endphp
          <div class="pipeline-step flex-fill" style="min-width:90px">
            <div class="pipeline-dot {{ $s['is_bottleneck'] ? 'red' : ($pct > 70 ? 'yellow' : ($s['wip'] > 0 ? 'green' : 'grey')) }}">
              {{ number_format($s['wip']) }}
            </div>
            <div class="fw-semibold text-truncate px-1" style="font-size:.75rem">{{ $s['name'] }}</div>
            <div class="progress mt-1 mx-2" style="height:4px">
              <div class="progress-bar {{ $s['is_bottleneck'] ? 'bg-danger' : ($pct > 70 ? 'bg-warning' : 'bg-success') }}" style="width:{{ $pct }}%"></div>
            </div>
            <small class="text-muted" style="font-size:.62rem">{{ number_format($pct,0) }}%</small>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  <!-- Weekly Throughput Chart -->
  <div class="col-md-8">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2 text-primary"></i>Tren Throughput 7 Hari Terakhir</div>
      <div class="card-body"><canvas id="throughputChart" height="120"></canvas></div>
    </div>
  </div>
  <!-- Budget Utilization -->
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-wallet2 me-2 text-primary"></i>Utilisasi Budget</div>
      <div class="card-body">
        <div class="text-center mb-3">
          <canvas id="budgetChart" width="160" height="160" style="max-width:160px"></canvas>
        </div>
        @php $utilPct = $totalBudgetPlan > 0 ? min(100,($totalBudgetActual/$totalBudgetPlan)*100) : 0; @endphp
        <div class="d-flex justify-content-between mb-1"><small>Budget Total</small><small class="fw-semibold">Rp {{ number_format($totalBudgetPlan) }}</small></div>
        <div class="d-flex justify-content-between mb-1"><small>Realisasi</small><small class="fw-semibold text-{{ $utilPct > 100 ? 'danger':'primary' }}">Rp {{ number_format($totalBudgetActual) }}</small></div>
        <div class="d-flex justify-content-between"><small>Utilisasi</small><small class="fw-bold text-{{ $utilPct > 100 ? 'danger':($utilPct > 80 ? 'warning':'success') }}">{{ number_format($utilPct,1) }}%</small></div>
        @if($totalBudgetActual > $totalBudgetPlan)
        <div class="alert alert-danger py-2 mt-3 mb-0"><i class="bi bi-exclamation-triangle me-1"></i><small>Realisasi melebihi budget!</small></div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Active Orders -->
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-list-check me-2 text-primary"></i>Order Produksi Aktif</span>
    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>No. Order</th><th>Produk</th><th class="d-mob-none">Target Tanggal</th><th class="d-mob-none">Total Qty</th><th>Progress</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($activeOrders as $o)
        @php $progress = $o->getProgressPercentage(); $overdue = $o->isOverdue(); @endphp
        <tr class="{{ $overdue ? 'table-danger' : '' }}">
          <td><span class="fw-semibold text-primary">{{ $o->order_no }}</span></td>
          <td>
            <div class="fw-semibold">{{ $o->product->name }}</div>
            <small class="text-muted">{{ optional($o->series)->name }}</small>
          </td>
          <td class="d-mob-none">
            <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">
              {{ $o->target_date->format('d M Y') }}
              @if($overdue)<i class="bi bi-exclamation-triangle-fill text-danger ms-1"></i>@endif
            </span>
          </td>
          <td class="d-mob-none">{{ number_format($o->getTotalTargetQty()) }} pcs</td>
          <td style="min-width:140px">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1"><div class="progress-bar {{ $progress >= 100 ? 'bg-success' : ($progress >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width:{{ $progress }}%"></div></div>
              <small class="text-muted fw-semibold" style="min-width:35px">{{ $progress }}%</small>
            </div>
          </td>
          <td><span class="badge badge-{{ $o->status }}">{{ $o->status_label }}</span></td>
          <td><a href="{{ route('orders.show',$o) }}" class="btn btn-sm btn-outline-primary btn-sm py-1">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada order aktif</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
@push('scripts')
<script>
// Throughput Chart
const tpLabels = @json($weeklyThroughput->pluck('input_date'));
const tpData = @json($weeklyThroughput->pluck('total'));
new Chart(document.getElementById('throughputChart'), {
  type: 'bar',
  data: {
    labels: tpLabels.map(d => new Date(d).toLocaleDateString('id-ID',{weekday:'short',day:'numeric',month:'short'})),
    datasets: [{ label: 'Unit Selesai', data: tpData, backgroundColor: 'rgba(26,60,110,.75)', borderRadius: 6 }]
  },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } } }
});
// Budget Chart
const bp = {{ $totalBudgetPlan }}, ba = {{ $totalBudgetActual }};
new Chart(document.getElementById('budgetChart'), {
  type: 'doughnut',
  data: {
    labels: ['Realisasi','Sisa Budget'],
    datasets: [{ data: [ba, Math.max(0,bp-ba)], backgroundColor: ba>bp ? ['#dc2626','#fee2e2'] : ['#1a3c6e','#e2e8f0'], borderWidth: 0, cutout: '75%' }]
  },
  options: { plugins: { legend: { display: false } }, responsive: false }
});
</script>
@endpush
