@extends('layouts.app')
@section('title','Detail Bahan Baku')
@section('page-title','Detail Bahan Baku')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $rawMaterial->name }} <span class="badge bg-secondary">{{ $rawMaterial->code }}</span></h4>
    <p class="text-muted mb-0">{{ $rawMaterial->category_label }} | {{ $rawMaterial->unit }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ url('bahan-baku/'.$rawMaterial->id.'/receipt') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-down me-2"></i>Input Penerimaan</a>
    <a href="{{ url('bahan-baku/'.$rawMaterial->id.'/edit') }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
  </div>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body">
        <div class="fs-2 fw-bold {{ $rawMaterial->isBelowMinStock() ? 'text-danger' : 'text-success' }}">{{ number_format($rawMaterial->current_stock) }}</div>
        <small class="text-muted">Stok Saat Ini ({{ $rawMaterial->unit }})</small>
        @if($rawMaterial->isBelowMinStock())<div class="badge bg-danger mt-1">Di bawah minimum!</div>@endif
      </div>
    </div>
  </div>
  <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-2 fw-bold text-warning">{{ number_format($rawMaterial->min_stock) }}</div><small class="text-muted">Stok Minimum</small></div></div></div>
  <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-2 fw-bold text-primary">Rp {{ number_format($rawMaterial->unit_price) }}</div><small class="text-muted">Harga / Unit</small></div></div></div>
  <div class="col-md-3"><div class="card text-center"><div class="card-body"><div class="fs-2 fw-bold text-info">Rp {{ number_format($rawMaterial->current_stock * $rawMaterial->unit_price) }}</div><small class="text-muted">Total Nilai Stok</small></div></div></div>
</div>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-receipt me-2"></i>Riwayat Penerimaan</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Tanggal</th><th>Qty</th><th>Harga/Unit</th><th>Supplier</th><th>No. PO</th></tr></thead>
          <tbody>
            @forelse($receipts as $r)
            <tr>
              <td>{{ $r->receipt_date->format('d M Y') }}</td>
              <td class="fw-semibold text-success">+{{ number_format($r->qty) }}</td>
              <td>Rp {{ number_format($r->unit_price) }}</td>
              <td>{{ $r->supplier ?? '-' }}</td>
              <td>{{ $r->po_no ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada penerimaan</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-link me-2"></i>Alokasi ke Order Produksi</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Order</th><th>Rencana</th><th>Aktual</th><th>Tanggal</th></tr></thead>
          <tbody>
            @forelse($allocations as $a)
            <tr>
              <td><a href="{{ route('orders.show',$a->order) }}" class="text-primary">{{ $a->order->order_no }}</a></td>
              <td>{{ number_format($a->qty_planned) }}</td>
              <td>{{ number_format($a->qty_actual) }}</td>
              <td>{{ $a->allocation_date->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada alokasi</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
