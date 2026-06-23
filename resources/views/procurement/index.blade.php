@extends('layouts.app')
@section('title','Procurement — Kebutuhan Bahan Baku')
@section('page-title','Procurement')
@section('breadcrumb')
<li class="breadcrumb-item active">Procurement</li>
@endsection
@section('content')

{{-- KPI --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-blue">
      <div class="kpi-icon"><i class="bi bi-clipboard-check"></i></div>
      <div class="kpi-value">{{ $totalOrders }}</div>
      <div class="kpi-label">Total Order Aktif</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-green">
      <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
      <div class="kpi-value">{{ $readyOrders }}</div>
      <div class="kpi-label">Bahan Disetujui</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-red">
      <div class="kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></div>
      <div class="kpi-value">{{ $shortageOrders }}</div>
      <div class="kpi-label">Stok Kurang</div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="kpi-card kpi-orange">
      <div class="kpi-icon"><i class="bi bi-hourglass-split"></i></div>
      <div class="kpi-value">{{ $pendingOrders }}</div>
      <div class="kpi-label">Menunggu Review</div>
    </div>
  </div>
</div>

{{-- Filter --}}
<div class="mb-3 d-flex gap-2 flex-wrap">
  <a href="{{ route('procurement.index') }}" class="btn btn-sm {{ !request('filter') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
  <a href="{{ route('procurement.index') }}?filter=shortage" class="btn btn-sm {{ request('filter')==='shortage' ? 'btn-danger' : 'btn-outline-danger' }}">Stok Kurang / Belum Disetujui</a>
  <a href="{{ route('procurement.index') }}?filter=approved" class="btn btn-sm {{ request('filter')==='approved' ? 'btn-success' : 'btn-outline-success' }}">Sudah Disetujui</a>
</div>

{{-- Table --}}
<div class="card">
  <div class="card-header"><i class="bi bi-boxes me-2 text-primary"></i>Status Kebutuhan Bahan per Order</div>
  @if($orders->isEmpty())
  <div class="card-body text-center text-muted py-5">Tidak ada order ditemukan.</div>
  @else
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>No. Order</th><th>Produk</th><th>Target Selesai</th><th>Total Qty</th><th>Status Bahan</th><th>Stok</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
        @php
          $reqs = $order->materialRequirements;
          $shortageCount = $reqs->filter(fn($r) => $r->qty_shortage > 0)->count();
        @endphp
        <tr>
          <td><a href="{{ route('procurement.show',$order) }}" class="fw-semibold text-primary">{{ $order->order_no }}</a></td>
          <td>
            <div class="fw-semibold">{{ $order->product->name }}</div>
            <small class="text-muted">{{ optional($order->series)->name }}</small>
          </td>
          <td class="{{ $order->isOverdue() ? 'text-danger fw-semibold':'' }}">{{ $order->target_date->format('d M Y') }}</td>
          <td>{{ number_format($order->getTotalTargetQty()) }} pcs</td>
          <td>
            @if($order->materials_approved)
              <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Disetujui</span>
            @elseif($shortageCount > 0)
              <span class="badge bg-danger">{{ $shortageCount }} bahan kurang</span>
            @else
              <span class="badge bg-warning text-dark">Belum disetujui</span>
            @endif
          </td>
          <td>
            @if($reqs->count() > 0)
              <div class="progress" style="height:6px;min-width:80px">
                <div class="progress-bar {{ $shortageCount > 0 ? 'bg-danger' : 'bg-success' }}"
                  style="width:{{ $reqs->count() > 0 ? round((($reqs->count()-$shortageCount)/$reqs->count())*100) : 0 }}%"></div>
              </div>
              <small class="text-muted">{{ $reqs->count()-$shortageCount }}/{{ $reqs->count() }} bahan cukup</small>
            @else
              <small class="text-muted">—</small>
            @endif
          </td>
          <td>
            <a href="{{ route('procurement.show',$order) }}" class="btn btn-sm btn-outline-primary py-1">Review</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
