@extends('layouts.app')
@section('title','Dashboard Reject')
@section('page-title','Dashboard Reject & Kualitas')
@section('content')

{{-- Filter Bulan --}}
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
  <form method="GET" class="d-flex align-items-center gap-2">
    <label class="fw-semibold text-muted mb-0" style="font-size:.85rem">Periode:</label>
    <select name="month" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
      @foreach($months as $m)
      <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
        {{ \Carbon\Carbon::parse($m.'-01')->translatedFormat('F Y') }}
      </option>
      @endforeach
    </select>
  </form>
  <span class="text-muted" style="font-size:.82rem"><i class="bi bi-info-circle me-1"></i>Data dari handover yang sudah dikonfirmasi</span>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-red">
      <div class="kpi-icon"><i class="bi bi-x-octagon"></i></div>
      <div class="kpi-value">{{ number_format($totalRejectQty) }}</div>
      <div class="kpi-label">Total Reject</div>
      <div class="kpi-change"><i class="bi bi-calendar-month"></i>Bulan ini (pcs)</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card {{ $rejectRate > 5 ? 'kpi-red' : ($rejectRate > 2 ? 'kpi-orange' : 'kpi-green') }}">
      <div class="kpi-icon"><i class="bi bi-percent"></i></div>
      <div class="kpi-value">{{ $rejectRate }}%</div>
      <div class="kpi-label">Reject Rate</div>
      <div class="kpi-change"><i class="bi bi-arrow-{{ $rejectRate > 5 ? 'up text-danger' : 'down text-success' }}"></i>dari {{ number_format($totalProduced) }} pcs produksi</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-teal">
      <div class="kpi-icon"><i class="bi bi-tools"></i></div>
      <div class="kpi-value">{{ number_format($totalRework) }}</div>
      <div class="kpi-label">Rework</div>
      <div class="kpi-change"><i class="bi bi-arrow-repeat"></i>Dikembalikan untuk diperbaiki</div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="kpi-card kpi-orange">
      <div class="kpi-icon"><i class="bi bi-trash3"></i></div>
      <div class="kpi-value">{{ number_format($totalScrap) }}</div>
      <div class="kpi-label">Scrap (Kerugian)</div>
      <div class="kpi-change"><i class="bi bi-exclamation-triangle"></i>Tidak dapat diselamatkan</div>
    </div>
  </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
  {{-- Trend 30 hari --}}
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-graph-up me-2"></i>Trend Reject — 30 Hari Terakhir</span>
      </div>
      <div class="card-body">
        <canvas id="trendChart" height="80"></canvas>
      </div>
    </div>
  </div>

  {{-- Pie: Tipe Reject --}}
  <div class="col-12 col-xl-5">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Komposisi Tipe Reject</div>
      <div class="card-body d-flex flex-column align-items-center justify-content-center">
        <canvas id="typeChart" style="max-height:200px;max-width:200px"></canvas>
        <div class="d-flex gap-3 mt-3 flex-wrap justify-content-center" style="font-size:.82rem">
          <span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#f59e0b;margin-right:4px"></span>Rework {{ $rejectByType['rework'] ?? 0 }} pcs</span>
          <span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#06b6d4;margin-right:4px"></span>Second {{ $rejectByType['second'] ?? 0 }} pcs</span>
          <span><span style="display:inline-block;width:12px;height:12px;border-radius:2px;background:#ef4444;margin-right:4px"></span>Scrap {{ $rejectByType['scrap'] ?? 0 }} pcs</span>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Reject by Stasiun & by Order --}}
<div class="row g-3 mb-4">
  {{-- By Stasiun --}}
  <div class="col-12 col-md-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-building me-2"></i>Reject per Stasiun</div>
      <div class="card-body p-0">
        @forelse($rejectByStation as $row)
        @php $max = $rejectByStation->max('total') ?: 1; @endphp
        <div class="px-3 py-2 border-bottom">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="fw-semibold" style="font-size:.85rem">{{ $row['station'] }}</span>
            <span class="badge bg-danger">{{ number_format($row['total']) }} pcs</span>
          </div>
          <div class="progress" style="height:6px;border-radius:3px">
            <div class="progress-bar bg-danger" style="width:{{ round($row['total']/$max*100) }}%"></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">Tidak ada data reject bulan ini</div>
        @endforelse
      </div>
    </div>
  </div>

  {{-- By Order/Produk --}}
  <div class="col-12 col-md-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-clipboard-x me-2"></i>Reject per Order Produksi</div>
      <div class="card-body p-0">
        @forelse($rejectByOrder as $row)
        @php $max2 = $rejectByOrder->max('total') ?: 1; @endphp
        <div class="px-3 py-2 border-bottom">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
              <span class="fw-semibold" style="font-size:.85rem">{{ $row['order_no'] }}</span>
              <small class="text-muted ms-1">{{ $row['product'] }}</small>
            </div>
            <span class="badge bg-warning text-dark">{{ number_format($row['total']) }} pcs</span>
          </div>
          <div class="progress" style="height:6px;border-radius:3px">
            <div class="progress-bar bg-warning" style="width:{{ round($row['total']/$max2*100) }}%"></div>
          </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">Tidak ada data reject bulan ini</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Tabel Reject Terbaru --}}
<div class="card">
  <div class="card-header d-flex align-items-center">
    <i class="bi bi-table me-2"></i>
    <span>Riwayat Reject Terbaru</span>
    <span class="badge bg-secondary ms-auto">{{ $recentRejects->count() }} item</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.85rem">
      <thead>
        <tr>
          <th>Tanggal</th>
          <th>Handover</th>
          <th>Order</th>
          <th>Stasiun</th>
          <th>SKU</th>
          <th>Qty Reject</th>
          <th>Tipe</th>
          <th>Alasan</th>
          <th>Foto</th>
          <th>PIC</th>
        </tr>
      </thead>
      <tbody>
        @forelse($recentRejects as $item)
        <tr>
          <td class="text-muted">{{ $item->handover->confirmed_at?->format('d M Y') ?? '-' }}</td>
          <td>
            <a href="{{ route('handover.show', $item->handover) }}" class="fw-semibold text-decoration-none" style="font-size:.8rem">
              {{ $item->handover->handover_no }}
            </a>
          </td>
          <td><small>{{ $item->handover->order?->order_no }}</small></td>
          <td><span class="badge bg-primary bg-opacity-15 text-primary" style="font-size:.72rem">{{ $item->handover->toStation?->name }}</span></td>
          <td><small class="font-monospace">{{ $item->sku->sku_code }}</small></td>
          <td class="fw-bold text-danger">{{ $item->qty_reject }}</td>
          <td>
            @if($item->reject_type === 'rework')
              <span class="badge bg-warning text-dark">🔧 Rework</span>
            @elseif($item->reject_type === 'second')
              <span class="badge bg-info text-white">🏷️ Second</span>
            @elseif($item->reject_type === 'scrap')
              <span class="badge bg-danger">🗑️ Scrap</span>
            @endif
          </td>
          <td><small class="text-muted">{{ $item->reject_notes ?: '-' }}</small></td>
          <td>
            @if($item->photo_reject)
            <a href="{{ asset($item->photo_reject) }}" target="_blank">
              <img src="{{ asset($item->photo_reject) }}" alt="Foto Reject"
                   style="width:44px;height:44px;object-fit:cover;border-radius:6px;border:2px solid #fca5a5;cursor:zoom-in"
                   loading="lazy">
            </a>
            @else
            <span class="text-muted">—</span>
            @endif
          </td>
          <td><small>{{ $item->handover->confirmedBy?->name ?? '-' }}</small></td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Belum ada data reject</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Trend Chart
const trendLabels = @json($trendReject->keys());
const trendData   = @json($trendReject->values());

// Fill missing dates with 0
const allDates = [];
const allData  = [];
const trendMap = {};
trendLabels.forEach((d, i) => trendMap[d] = trendData[i]);
for (let i = 29; i >= 0; i--) {
  const d = new Date();
  d.setDate(d.getDate() - i);
  const key = d.toISOString().slice(0, 10);
  allDates.push(key.slice(5)); // MM-DD
  allData.push(trendMap[key] ?? 0);
}

new Chart(document.getElementById('trendChart'), {
  type: 'bar',
  data: {
    labels: allDates,
    datasets: [{
      label: 'Qty Reject',
      data: allData,
      backgroundColor: 'rgba(239,68,68,.25)',
      borderColor: '#ef4444',
      borderWidth: 1.5,
      borderRadius: 4,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } },
      x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
    }
  }
});

// Pie Chart
const rework = {{ $rejectByType['rework'] ?? 0 }};
const second = {{ $rejectByType['second'] ?? 0 }};
const scrap  = {{ $rejectByType['scrap'] ?? 0 }};

new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: ['Rework', 'Second', 'Scrap'],
    datasets: [{
      data: [rework, second, scrap],
      backgroundColor: ['#f59e0b', '#06b6d4', '#ef4444'],
      borderWidth: 2,
      borderColor: '#fff',
    }]
  },
  options: {
    responsive: true,
    cutout: '65%',
    plugins: {
      legend: { display: false },
      tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.raw} pcs` } }
    }
  }
});
</script>
@endpush
