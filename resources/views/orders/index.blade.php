@extends('layouts.app')
@section('title','Order Produksi')
@section('page-title','Order Produksi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="mb-0 fw-bold">Daftar Order Produksi</h5>
    <p class="text-muted small mb-0">Kelola dan pantau semua order produksi</p>
  </div>
  @if(in_array(auth()->user()->role,['admin','supervisor']))
  <a href="{{ route('orders.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Buat Order Baru</a>
  @endif
</div>
<!-- Filter -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4"><input type="text" name="search" class="form-control" placeholder="Cari no. order / produk..." value="{{ request('search') }}"></div>
      <div class="col-md-3">
        <select name="status" class="form-select">
          <option value="">Semua Status</option>
          @foreach(['draft'=>'Draft','active'=>'Aktif','completed'=>'Selesai','on_hold'=>'Ditahan','cancelled'=>'Dibatalkan'] as $v=>$l)
          <option value="{{ $v }}" {{ request('status')==$v ? 'selected' : '' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
      <div class="col-md-2"><a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>No. Order</th><th>Produk / Series</th><th>Target Tanggal</th><th>Total Qty</th><th>Progress</th><th>Status</th><th>Dibuat</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($orders as $o)
        @php $progress = $o->getProgressPercentage(); $overdue = $o->isOverdue(); @endphp
        <tr class="{{ $overdue ? 'table-danger' : '' }}">
          <td><a href="{{ route('orders.show',$o) }}" class="fw-bold text-primary text-decoration-none">{{ $o->order_no }}</a></td>
          <td><div class="fw-semibold">{{ $o->product->name }}</div><small class="text-muted">{{ optional($o->series)->name }}</small></td>
          <td>
            <span class="{{ $overdue ? 'text-danger fw-semibold' : '' }}">{{ $o->target_date->format('d M Y') }}</span>
            @if($overdue)<div><span class="badge bg-danger" style="font-size:.65rem">TERLAMBAT</span></div>@endif
          </td>
          <td>{{ number_format($o->getTotalTargetQty()) }} pcs</td>
          <td style="min-width:120px">
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1">
                <div class="progress-bar {{ $progress>=100?'bg-success':($progress>=50?'bg-primary':'bg-warning') }}" style="width:{{ $progress }}%"></div>
              </div>
              <small class="text-muted fw-semibold" style="min-width:32px">{{ $progress }}%</small>
            </div>
          </td>
          <td><span class="badge badge-{{ $o->status }} px-2 py-1">{{ $o->status_label }}</span></td>
          <td><small class="text-muted">{{ $o->created_at->format('d/m/Y') }}<br>{{ $o->creator->name }}</small></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('orders.show',$o) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a>
              @if(in_array(auth()->user()->role,['admin','supervisor']) && $o->status === 'draft')
              <a href="{{ route('orders.edit',$o) }}" class="btn btn-sm btn-outline-secondary py-1">Edit</a>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Tidak ada order ditemukan</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($orders->hasPages())
  <div class="card-footer py-3">{{ $orders->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
