@extends('layouts.app')
@section('title','Dashboard Operasional')
@section('page-title','Dashboard Operasional')
@section('content')
<!-- Station Cards -->
<div class="row g-3 mb-4">
  @foreach($stations as $st)
  @php $statusColor = $st->is_bottleneck ? 'danger' : ($st->qty_in_process > $st->bottleneck_threshold*0.7 ? 'warning' : 'success'); @endphp
  <div class="col-6 col-md-4 col-lg-2">
    <div class="card border-{{ $statusColor }}" style="border-top:4px solid !important;border-top-color:var(--bs-{{ $statusColor }}) !important">
      <div class="card-body text-center py-3">
        @if($st->is_bottleneck)<div class="badge bg-danger text-white mb-2">BOTTLENECK</div>@endif
        <div class="fw-bold text-{{ $statusColor == 'danger' ? 'danger' : 'primary' }}">{{ $st->station->name ?? $st->name }}</div>
        <div class="display-6 fw-bold mt-1">{{ number_format($st->qty_in_process) }}</div>
        <small class="text-muted">dalam proses</small>
        <hr class="my-2">
        <div class="row text-center g-0">
          <div class="col"><div class="text-success fw-semibold">{{ number_format($st->qty_in_total) }}</div><div style="font-size:.65rem" class="text-muted">Masuk</div></div>
          <div class="col"><div class="text-primary fw-semibold">{{ number_format($st->qty_out_total) }}</div><div style="font-size:.65rem" class="text-muted">Keluar</div></div>
          <div class="col"><div class="text-danger fw-semibold">{{ number_format($st->qty_reject_total) }}</div><div style="font-size:.65rem" class="text-muted">Reject</div></div>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="row g-3">
  <!-- Pending Handovers -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-arrow-left-right me-2 text-warning"></i>Handover Pending ({{ $pendingHandovers->count() }})</span>
        <a href="{{ route('handover.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="list-group list-group-flush">
        @forelse($pendingHandovers->take(5) as $h)
        <a href="{{ route('handover.show',$h) }}" class="list-group-item list-group-item-action py-3">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold text-primary">{{ $h->handover_no }}</span>
            <small class="text-muted">{{ $h->initiated_at ? $h->initiated_at->diffForHumans() : '-' }}</small>
          </div>
          <div class="small text-muted mt-1">
            {{ $h->fromStation?->name ?? 'Order Produksi' }} → {{ $h->toStation->name }} |
            <strong>{{ $h->order->order_no }}</strong> |
            {{ $h->items->sum('qty_sent') }} pcs
          </div>
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4"><i class="bi bi-check-circle text-success me-1"></i>Tidak ada handover pending</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Discrepancy Handovers -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Discrepancy Perlu Persetujuan ({{ $discrepancyHandovers->count() }})</span>
      </div>
      <div class="list-group list-group-flush">
        @forelse($discrepancyHandovers->take(5) as $h)
        <a href="{{ route('handover.show',$h) }}" class="list-group-item list-group-item-action py-3">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold text-danger">{{ $h->handover_no }}</span>
            <span class="badge bg-danger">Discrepancy</span>
          </div>
          <div class="small text-muted mt-1">{{ $h->fromStation?->name ?? 'Order Produksi' }} → {{ $h->toStation->name }} | {{ $h->order->order_no }}</div>
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4"><i class="bi bi-check-circle text-success me-1"></i>Tidak ada discrepancy</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Overdue Orders -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-alarm me-2 text-danger"></i>Order Terlambat ({{ $overdueOrders->count() }})</div>
      <div class="list-group list-group-flush">
        @forelse($overdueOrders as $o)
        <a href="{{ route('orders.show',$o) }}" class="list-group-item list-group-item-action py-3">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold text-danger">{{ $o->order_no }}</span>
            <small class="text-danger">{{ $o->target_date->diffForHumans() }}</small>
          </div>
          <small class="text-muted">{{ $o->product->name }} | Target: {{ $o->target_date->format('d M Y') }}</small>
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4"><i class="bi bi-check-circle text-success me-1"></i>Semua order on-track!</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Near Deadline -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-clock-history me-2 text-warning"></i>Mendekati Deadline (3 hari)</div>
      <div class="list-group list-group-flush">
        @forelse($nearDeadlineOrders as $o)
        <a href="{{ route('orders.show',$o) }}" class="list-group-item list-group-item-action py-3">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold text-warning">{{ $o->order_no }}</span>
            <small class="text-warning">{{ $o->target_date->diffForHumans() }}</small>
          </div>
          <small class="text-muted">{{ $o->product->name }} | Target: {{ $o->target_date->format('d M Y') }}</small>
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4"><i class="bi bi-check-circle text-success me-1"></i>Tidak ada order mendekati deadline</div>
        @endforelse
      </div>
    </div>
  </div>

  <!-- Recent WIP -->
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-activity me-2 text-primary"></i>Aktivitas WIP Terbaru</span>
        <a href="{{ route('wip.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Waktu</th><th>Order</th><th>SKU</th><th>Stasiun</th><th>Masuk</th><th>Keluar</th><th>Reject</th><th>WIP</th><th>Input Oleh</th></tr></thead>
          <tbody>
            @forelse($recentWip as $w)
            <tr>
              <td><small class="text-muted">{{ $w->created_at->format('d/m H:i') }}</small></td>
              <td><span class="text-primary fw-semibold">{{ $w->order->order_no }}</span></td>
              <td><span style="font-size:.78rem">{{ $w->sku->sku_code }}</span></td>
              <td><span class="badge bg-primary text-white">{{ $w->station->name }}</span></td>
              <td class="text-success fw-semibold">+{{ $w->qty_in }}</td>
              <td class="text-primary fw-semibold">+{{ $w->qty_out }}</td>
              <td class="text-danger fw-semibold">{{ $w->qty_reject }}</td>
              <td><span class="badge bg-info text-white">{{ $w->qty_in_process }}</span></td>
              <td><small>{{ $w->creator->name }}</small></td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada aktivitas WIP</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
