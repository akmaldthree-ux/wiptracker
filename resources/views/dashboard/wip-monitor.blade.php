@extends('layouts.app')
@section('title','WIP Position Monitor')
@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1"><i class="bi bi-radar me-2 text-primary"></i>WIP Position Monitor</h4>
    <p class="text-muted mb-0">Monitoring posisi qty produk di setiap stasiun secara realtime</p>
  </div>
  <div class="d-flex gap-2 align-items-center">
    <select id="filterOrder" class="form-select form-select-sm" style="min-width:220px">
      <option value="">Semua Order</option>
      @foreach($activeOrders as $o)
      <option value="{{ $o->id }}">{{ $o->order_no }} — {{ $o->product->name }}</option>
      @endforeach
    </select>
    <button class="btn btn-sm btn-outline-primary" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</button>
  </div>
</div>

{{-- ====== PIPELINE FLOW VISUAL ====== --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span class="fw-semibold"><i class="bi bi-diagram-3 me-2 text-primary"></i>Alur Produksi — Posisi WIP per Stasiun</span>
    <div class="d-flex gap-2">
      <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Normal</span>
      <span class="badge bg-warning bg-opacity-15 text-warning border border-warning border-opacity-25"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Hampir Penuh</span>
      <span class="badge bg-danger bg-opacity-15 text-danger border border-danger border-opacity-25"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Bottleneck</span>
    </div>
  </div>
  <div class="card-body p-4">
    <div class="d-flex align-items-stretch gap-0" id="pipelineContainer">
      @foreach($stations as $idx => $station)
      @php
        $netWip = $station->net_wip ?? 0;
        $threshold = $station->bottleneck_threshold ?? 500;
        $pct = $threshold > 0 ? min(100, ($netWip / $threshold) * 100) : 0;
        $isBottleneck = $netWip > $threshold;
        $isWarning = $pct >= 70 && !$isBottleneck;
        $colorClass = $isBottleneck ? 'danger' : ($isWarning ? 'warning' : ($netWip > 0 ? 'success' : 'secondary'));
        $bgClass = $isBottleneck ? 'bg-danger' : ($isWarning ? 'bg-warning' : ($netWip > 0 ? 'bg-success' : 'bg-secondary'));
      @endphp
      <div class="flex-fill pipeline-station" data-station="{{ $station->id }}" style="min-width:0">
        <div class="text-center">
          {{-- Station Header --}}
          <div class="pipeline-station-header rounded-top p-2 {{ $bgClass }} bg-opacity-10 border border-{{ $colorClass }} border-opacity-25">
            <div class="d-flex align-items-center justify-content-center gap-1 mb-1">
              @if($isBottleneck)
              <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:.9rem"></i>
              @endif
              <span class="fw-bold small text-{{ $colorClass === 'secondary' ? 'muted' : $colorClass }}">{{ $station->name }}</span>
            </div>
            <div class="fw-bold fs-3 text-{{ $colorClass === 'secondary' ? 'muted' : $colorClass }}">{{ number_format($netWip) }}</div>
            <small class="text-muted">unit dalam proses</small>
            <div class="progress mt-2 mx-2" style="height:8px;border-radius:4px">
              <div class="progress-bar {{ $bgClass }}" style="width:{{ $pct }}%"></div>
            </div>
            <small class="text-muted" style="font-size:.65rem">{{ number_format($pct, 0) }}% dari threshold {{ number_format($threshold) }}</small>
          </div>

          {{-- Order breakdown in this station --}}
          <div class="pipeline-orders border border-top-0 border-{{ $colorClass }} border-opacity-25 rounded-bottom" style="min-height:120px">
            @php $stOrders = $wipByStation[$station->id] ?? collect(); @endphp
            @forelse($stOrders->take(5) as $wip)
            <div class="pipeline-order-item d-flex justify-content-between align-items-center px-2 py-1 border-bottom wip-order-row"
              data-order="{{ $wip->production_order_id }}" style="font-size:.72rem">
              <span class="text-primary fw-semibold text-truncate" style="max-width:70px" title="{{ $wip->order_no }}">{{ $wip->order_no }}</span>
              <span class="badge bg-{{ $colorClass }} bg-opacity-{{ $netWip > 0 ? '15' : '10' }} text-{{ $colorClass === 'secondary' ? 'muted' : $colorClass }} fw-bold">{{ number_format($wip->net_wip) }}</span>
            </div>
            @empty
            <div class="text-center py-4 text-muted" style="font-size:.75rem">
              <i class="bi bi-dash-circle opacity-25 d-block mb-1" style="font-size:1.2rem"></i>
              Kosong
            </div>
            @endforelse
            @if($stOrders->count() > 5)
            <div class="text-center py-1 text-muted" style="font-size:.68rem">+{{ $stOrders->count()-5 }} order lainnya</div>
            @endif
          </div>
        </div>
      </div>

      {{-- Arrow between stations --}}
      @if(!$loop->last)
      <div class="d-flex align-items-center px-1" style="color:#cbd5e1">
        <i class="bi bi-chevron-right fs-5"></i>
      </div>
      @endif
      @endforeach
    </div>
  </div>
</div>

{{-- ====== CHARTS ROW ====== --}}
<div class="row g-4 mb-4">
  {{-- Stacked bar: WIP per station per order --}}
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold">
        <i class="bi bi-bar-chart-steps me-2 text-primary"></i>Distribusi WIP per Stasiun (per Order)
      </div>
      <div class="card-body">
        <canvas id="wipStationChart" height="120"></canvas>
      </div>
    </div>
  </div>

  {{-- Donut: Total WIP by station --}}
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold">
        <i class="bi bi-pie-chart me-2 text-info"></i>Proporsi WIP per Stasiun
      </div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <canvas id="wipDonutChart" width="200" height="200" style="max-width:200px"></canvas>
        <div class="mt-3 w-100">
          @foreach($stations as $station)
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div class="d-flex align-items-center gap-2">
              <span class="rounded-circle d-inline-block" style="width:10px;height:10px;background:{{ $stationColors[$loop->index] }}"></span>
              <small>{{ $station->name }}</small>
            </div>
            <small class="fw-semibold">{{ number_format($station->net_wip ?? 0) }}</small>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ====== PER-ORDER PROGRESS TABLE ====== --}}
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header fw-semibold">
    <i class="bi bi-table me-2 text-success"></i>Posisi WIP per Order Produksi
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0" id="wipOrderTable">
      <thead class="table-light">
        <tr>
          <th>Order</th>
          <th>Produk</th>
          <th class="text-center">Target Qty</th>
          @foreach($stations as $s)
          <th class="text-center" style="min-width:90px">
            <small>{{ $s->name }}</small>
          </th>
          @endforeach
          <th class="text-center">Total WIP</th>
          <th class="text-center">Progress</th>
        </tr>
      </thead>
      <tbody>
        @forelse($activeOrders as $order)
        @php
          $totalWip = 0;
          $targetQty = $order->getTotalTargetQty();
        @endphp
        <tr class="wip-order-row" data-order="{{ $order->id }}">
          <td>
            <a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_no }}</a>
            @if($order->isOverdue())<br><span class="badge bg-danger" style="font-size:.65rem">Terlambat</span>@endif
          </td>
          <td>
            <div class="small fw-semibold">{{ $order->product->name }}</div>
            <small class="text-muted">Target: {{ $order->target_date?->format('d M Y') }}</small>
          </td>
          <td class="text-center fw-semibold">{{ number_format($targetQty) }}</td>

          @foreach($stations as $station)
          @php
            $wip = $wipByOrderStation[$order->id][$station->id] ?? 0;
            $totalWip += $wip;
          @endphp
          <td class="text-center">
            @if($wip > 0)
            <div class="fw-bold text-primary">{{ number_format($wip) }}</div>
            <div class="progress mt-1" style="height:4px">
              <div class="progress-bar bg-primary" style="width:{{ $targetQty > 0 ? min(100,($wip/$targetQty)*100) : 0 }}%"></div>
            </div>
            @else
            <span class="text-muted">—</span>
            @endif
          </td>
          @endforeach

          <td class="text-center fw-bold text-info">{{ number_format($totalWip) }}</td>
          <td class="text-center" style="min-width:120px">
            @php $prog = $order->getProgressPercentage(); @endphp
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px">
                <div class="progress-bar {{ $prog >= 100 ? 'bg-success' : ($prog >= 50 ? 'bg-primary' : 'bg-warning') }}" style="width:{{ $prog }}%"></div>
              </div>
              <small class="fw-semibold text-muted">{{ $prog }}%</small>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="{{ 5 + count($stations) }}" class="text-center py-5 text-muted">
          <i class="bi bi-inbox display-6 d-block mb-2 opacity-25"></i>Tidak ada order aktif
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ====== BOTTLENECK ALERT ====== --}}
@php $bottlenecks = $stations->filter(fn($s) => ($s->net_wip ?? 0) > $s->bottleneck_threshold); @endphp
@if($bottlenecks->count() > 0)
<div class="alert alert-danger border-0 shadow-sm">
  <div class="d-flex align-items-start gap-3">
    <i class="bi bi-exclamation-triangle-fill fs-4 text-danger mt-1"></i>
    <div>
      <div class="fw-bold">Bottleneck Terdeteksi!</div>
      <div class="mt-1">
        @foreach($bottlenecks as $b)
        <div class="small">
          <strong>{{ $b->name }}</strong>: {{ number_format($b->net_wip) }} unit
          (threshold: {{ number_format($b->bottleneck_threshold) }},
          kelebihan: <strong class="text-danger">{{ number_format($b->net_wip - $b->bottleneck_threshold) }}</strong> unit)
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

{{-- ====== RECENT ACTIVITY FEED ====== --}}
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center fw-semibold">
    <span><i class="bi bi-activity me-2 text-primary"></i>Aktivitas WIP Terbaru</span>
    <a href="{{ route('wip.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0 small">
      <thead class="table-light">
        <tr><th>Waktu</th><th>Order</th><th>SKU</th><th>Stasiun</th><th class="text-center">Masuk</th><th class="text-center">Keluar</th><th class="text-center">Reject</th><th class="text-center">In-Process</th><th>Oleh</th></tr>
      </thead>
      <tbody>
        @forelse($recentWip as $w)
        <tr>
          <td class="text-muted">{{ $w->created_at->format('d/m H:i') }}</td>
          <td><span class="text-primary fw-semibold">{{ $w->order->order_no }}</span></td>
          <td><span class="font-monospace text-muted">{{ $w->sku->sku_code }}</span></td>
          <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $w->station->name }}</span></td>
          <td class="text-center text-success fw-semibold">+{{ number_format($w->qty_in) }}</td>
          <td class="text-center text-primary fw-semibold">+{{ number_format($w->qty_out) }}</td>
          <td class="text-center text-danger fw-semibold">{{ number_format($w->qty_reject) }}</td>
          <td class="text-center"><span class="badge bg-info bg-opacity-15 text-info fw-bold">{{ number_format($w->qty_in_process) }}</span></td>
          <td class="text-muted">{{ $w->creator->name }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-4 text-muted">Belum ada aktivitas WIP</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
@push('scripts')
<script>
// ---- Data from PHP ----
const stationNames = @json($stations->pluck('name'));
const stationWips  = @json($stations->pluck('net_wip')->map(fn($v) => max(0, $v ?? 0)));
const stationColors = @json($stationColors);
const orderLabels  = @json($activeOrders->pluck('order_no'));
const orderColors  = @json($orderColors);

// WIP per order per station matrix: { orderId: { stationId: net_wip } }
const wipMatrix    = @json($wipByOrderStation);
const orderIds     = @json($activeOrders->pluck('id'));
const stationIds   = @json($stations->pluck('id'));

// ---- Stacked Bar: WIP per Station (stacked by Order) ----
const datasets = orderIds.map((oid, i) => ({
  label: orderLabels[i],
  data: stationIds.map(sid => (wipMatrix[oid] && wipMatrix[oid][sid]) ? wipMatrix[oid][sid] : 0),
  backgroundColor: orderColors[i],
  borderRadius: 4,
  borderSkipped: false,
}));

new Chart(document.getElementById('wipStationChart'), {
  type: 'bar',
  data: { labels: stationNames, datasets },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12 } },
      tooltip: {
        callbacks: {
          label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} unit`
        }
      }
    },
    scales: {
      x: { stacked: true, grid: { display: false } },
      y: { stacked: true, beginAtZero: true, grid: { color: '#f1f5f9' },
           ticks: { callback: v => v.toLocaleString('id-ID') } }
    }
  }
});

// ---- Donut: WIP proportion by station ----
new Chart(document.getElementById('wipDonutChart'), {
  type: 'doughnut',
  data: {
    labels: stationNames,
    datasets: [{
      data: stationWips,
      backgroundColor: stationColors,
      borderWidth: 2,
      borderColor: '#fff',
      hoverOffset: 6,
      cutout: '65%',
    }]
  },
  options: {
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString('id-ID')} unit`
        }
      }
    },
    responsive: false,
  }
});

// ---- Filter by Order ----
document.getElementById('filterOrder').addEventListener('change', function() {
  const oid = this.value;
  document.querySelectorAll('.wip-order-row').forEach(row => {
    if (!oid || row.dataset.order == oid) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
  // Pipeline: dim order items
  document.querySelectorAll('.pipeline-order-item').forEach(item => {
    if (!oid || item.dataset.order == oid) {
      item.style.opacity = '1';
      item.style.fontWeight = oid ? 'bold' : '';
    } else {
      item.style.opacity = '0.3';
    }
  });
});
</script>
@endpush
