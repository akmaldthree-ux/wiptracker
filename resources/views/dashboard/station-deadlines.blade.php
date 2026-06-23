@extends('layouts.app')
@section('title','Monitor Deadline Stasiun')
@section('page-title','Monitor Deadline Stasiun')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Deadline Stasiun</li>
@endsection
@section('content')

{{-- Filter Bar --}}
<div class="card mb-4">
  <div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Stasiun</label>
        <select name="station_id" class="form-select form-select-sm">
          <option value="">Semua Stasiun</option>
          @foreach($stations as $st)
          <option value="{{ $st->id }}" {{ request('station_id')==$st->id ? 'selected':'' }}>{{ $st->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold mb-1">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          <option value="overdue"  {{ request('status')=='overdue'  ? 'selected':'' }}>Terlambat</option>
          <option value="critical" {{ request('status')=='critical' ? 'selected':'' }}>Kritis (≤2 hari)</option>
          <option value="warning"  {{ request('status')=='warning'  ? 'selected':'' }}>Perhatian (3–7 hari)</option>
          <option value="ontrack"  {{ request('status')=='ontrack'  ? 'selected':'' }}>On Track (>7 hari)</option>
        </select>
      </div>
      <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-filter me-1"></i>Filter</button>
        <a href="{{ route('dashboard.station-deadlines') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      </div>
    </form>
  </div>
</div>

{{-- Summary Chips --}}
@php
  $all = $deadlines;
  $countOverdue  = $all->filter(fn($d) => $d->status === 'overdue')->count();
  $countCritical = $all->filter(fn($d) => $d->status === 'critical')->count();
  $countWarning  = $all->filter(fn($d) => $d->status === 'warning')->count();
  $countOntrack  = $all->filter(fn($d) => $d->status === 'ontrack')->count();
@endphp
<div class="row g-2 mb-4">
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-red">
      <div class="kpi-icon"><i class="bi bi-alarm-fill"></i></div>
      <div class="kpi-value">{{ $countOverdue }}</div>
      <div class="kpi-label">Terlambat</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card" style="background:linear-gradient(135deg,#fff7ed,#ffedd5);border-left:4px solid #ea580c">
      <div class="kpi-icon" style="color:#ea580c"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div class="kpi-value" style="color:#ea580c">{{ $countCritical }}</div>
      <div class="kpi-label">Kritis ≤2 Hari</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-orange">
      <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
      <div class="kpi-value">{{ $countWarning }}</div>
      <div class="kpi-label">Perhatian 3–7 Hari</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-green">
      <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
      <div class="kpi-value">{{ $countOntrack }}</div>
      <div class="kpi-label">On Track</div>
    </div>
  </div>
</div>

{{-- Table --}}
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-calendar-range me-2 text-primary"></i>Semua Deadline Stasiun — Order Aktif</span>
    <span class="text-muted small">{{ $deadlines->count() }} deadline</span>
  </div>
  @if($deadlines->isEmpty())
  <div class="card-body text-center text-muted py-5">
    <i class="bi bi-calendar-check display-5 d-block mb-2 opacity-25"></i>
    Tidak ada deadline ditemukan untuk filter ini.
  </div>
  @else
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>No. Order</th>
          <th>Produk</th>
          <th>Stasiun</th>
          <th>Target Deadline</th>
          <th>Sisa Waktu</th>
          <th>Progress Keluar</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($deadlines as $dl)
        @php
          $order = $dl->order;
          $totalQty = $order->getTotalTargetQty();
          $qtyOut = \App\Models\WipEntry::where('production_order_id',$order->id)
                      ->where('station_id',$dl->station_id)->sum('qty_out');
          $pct = $totalQty > 0 ? min(100, round(($qtyOut / $totalQty) * 100)) : 0;
          $isDone = $pct >= 100;
        @endphp
        <tr class="{{ in_array($dl->status,['overdue','critical']) && !$isDone ? 'table-danger' : '' }}">
          <td><a href="{{ route('orders.show',$order) }}" class="fw-semibold text-primary">{{ $order->order_no }}</a></td>
          <td>
            <div class="fw-semibold">{{ $order->product->name }}</div>
            <small class="text-muted">{{ optional($order->series)->name }}</small>
          </td>
          <td><span class="badge bg-secondary bg-opacity-15 text-secondary">{{ $dl->station->name }}</span></td>
          <td class="{{ ($dl->is_overdue && !$isDone) ? 'text-danger fw-semibold':'' }}">
            {{ $dl->target_date->format('d M Y') }}
          </td>
          <td>
            @if($isDone)
              <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Selesai</span>
            @elseif($dl->status === 'overdue')
              <span class="badge bg-danger">{{ abs($dl->days_remaining) }} hari terlambat</span>
            @elseif($dl->status === 'critical')
              <span class="badge bg-danger">{{ $dl->days_remaining }} hari lagi</span>
            @elseif($dl->status === 'warning')
              <span class="badge bg-warning text-dark">{{ $dl->days_remaining }} hari lagi</span>
            @else
              <span class="badge bg-success">{{ $dl->days_remaining }} hari lagi</span>
            @endif
          </td>
          <td style="min-width:130px">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="height:6px">
                <div class="progress-bar {{ $isDone ? 'bg-success' : ($pct > 50 ? 'bg-primary' : 'bg-warning') }}" style="width:{{ $pct }}%"></div>
              </div>
              <small class="text-muted fw-semibold" style="min-width:30px">{{ $pct }}%</small>
            </div>
            <small class="text-muted">{{ number_format($qtyOut) }} / {{ number_format($totalQty) }} pcs</small>
          </td>
          <td>
            @if($isDone)
              <span class="badge bg-success">✓ Selesai</span>
            @else
              <span class="badge bg-{{ $dl->status_color }}">{{ $dl->status_label }}</span>
            @endif
          </td>
          <td><a href="{{ route('orders.show',$order) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
