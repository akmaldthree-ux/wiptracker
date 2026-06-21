@extends('layouts.app')
@section('title','Purchase Order')
@section('page-title','Purchase Order')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="mb-0 fw-bold">Purchase Order</h5>
    <p class="text-muted small mb-0">Kelola pembelian bahan baku ke supplier</p>
  </div>
  @if(in_array(auth()->user()->role,['admin','supervisor']))
  <a href="{{ route('purchase-order.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Buat PO Baru</a>
  @endif
</div>

<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-auto"><small class="text-muted fw-semibold"><i class="bi bi-funnel me-1"></i>Filter:</small></div>
      <div class="col">
        <select name="status" class="form-select form-select-sm" style="max-width:200px">
          <option value="">Semua Status</option>
          @foreach(['draft'=>'Draft','sent'=>'Terkirim','partial'=>'Diterima Sebagian','received'=>'Diterima Lengkap','cancelled'=>'Dibatalkan'] as $v=>$l)
          <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col">
        <select name="supplier_id" class="form-select form-select-sm" style="max-width:220px">
          <option value="">Semua Supplier</option>
          @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
        </select>
      </div>
      <div class="col-auto d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
        <a href="{{ route('purchase-order.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr><th>No. PO</th><th>Supplier</th><th>Tgl Order</th><th>Estimasi Tiba</th><th>Total</th><th>Progress Terima</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($pos as $po)
        <tr>
          <td><a href="{{ route('purchase-order.show',$po) }}" class="fw-semibold text-primary text-decoration-none font-monospace">{{ $po->po_no }}</a></td>
          <td>
            <div class="fw-semibold">{{ $po->supplier->name }}</div>
            <small class="text-muted">{{ $po->items->count() }} item</small>
          </td>
          <td><small>{{ $po->order_date->format('d M Y') }}</small></td>
          <td>
            @if($po->expected_date)
              <small class="{{ $po->expected_date->isPast() && !in_array($po->status,['received','cancelled']) ? 'text-danger fw-semibold' : '' }}">
                {{ $po->expected_date->format('d M Y') }}
                @if($po->expected_date->isPast() && !in_array($po->status,['received','cancelled']))<br><span class="badge bg-danger" style="font-size:.6rem">TERLAMBAT</span>@endif
              </small>
            @else <small class="text-muted">—</small> @endif
          </td>
          <td class="fw-semibold">Rp {{ number_format($po->total_amount,0,',','.') }}</td>
          <td style="min-width:120px">
            @php $pct = $po->receive_progress @endphp
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1"><div class="progress-bar bg-{{ $pct>=100?'success':'primary' }}" style="width:{{ $pct }}%"></div></div>
              <small class="fw-semibold">{{ $pct }}%</small>
            </div>
          </td>
          <td><span class="badge bg-{{ $po->status_color }}">{{ $po->status_label }}</span></td>
          <td><a href="{{ route('purchase-order.show',$po) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-cart-x"></i><p class="fw-semibold mb-1">Belum ada Purchase Order</p><p>Buat PO pertama untuk pembelian bahan baku</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($pos->hasPages())
  <div class="card-footer py-2">{{ $pos->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
