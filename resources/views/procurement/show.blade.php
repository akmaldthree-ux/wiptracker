@extends('layouts.app')
@section('title','Detail Kebutuhan Bahan — '.$order->order_no)
@section('page-title','Detail Kebutuhan Bahan')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('procurement.index') }}">Procurement</a></li>
<li class="breadcrumb-item active">{{ $order->order_no }}</li>
@endsection
@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold mb-1">{{ $order->order_no }} <span class="badge badge-{{ $order->status }}">{{ $order->status_label }}</span></h4>
    <p class="text-muted mb-0">{{ $order->product->name }} — {{ optional($order->series)->name }}</p>
    <small class="text-muted">Target: {{ $order->target_date->format('d M Y') }} &bull; Total Qty: {{ number_format($order->getTotalTargetQty()) }} pcs</small>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if(auth()->user()->isProcurement())
    <form method="POST" action="{{ route('procurement.regenerate',$order) }}">
      @csrf
      <button type="submit" class="btn btn-outline-secondary btn-sm" onclick="return confirm('Hitung ulang kebutuhan bahan?')">
        <i class="bi bi-arrow-clockwise me-1"></i>Hitung Ulang
      </button>
    </form>
    @if($order->materials_approved)
    <form method="POST" action="{{ route('procurement.revoke',$order) }}">
      @csrf
      <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Batalkan persetujuan bahan?')">
        <i class="bi bi-x-circle me-1"></i>Batalkan Persetujuan
      </button>
    </form>
    @else
    <form method="POST" action="{{ route('procurement.approve',$order) }}">
      @csrf
      <button type="submit" class="btn btn-success {{ !$allSufficient ? 'opacity-50':'' }}"
        @if(!$allSufficient) onclick="return confirm('Ada bahan yang stoknya kurang. Yakin tetap setujui?')" @endif>
        <i class="bi bi-check-circle me-1"></i>Setujui Bahan
      </button>
    </form>
    @endif
    @endif
    <a href="{{ route('orders.show',$order) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali ke Order</a>
  </div>
</div>

{{-- Approval Status Banner --}}
@if($order->materials_approved)
<div class="alert alert-success d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-check-circle-fill fs-5"></i>
  <div>
    <strong>Bahan Baku Disetujui</strong>
    oleh <strong>{{ $order->materialsApprovedBy?->name }}</strong>
    pada {{ $order->materials_approved_at?->format('d M Y, H:i') }}.
    Order ini siap dikirim ke Cutting.
  </div>
</div>
@elseif(!$allSufficient)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-5"></i>
  <div>
    <strong>Stok Tidak Cukup</strong> — Ada bahan yang stoknya di bawah kebutuhan.
    Buat Purchase Order untuk bahan yang kurang, atau setujui dengan catatan.
  </div>
</div>
@else
<div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-hourglass-split fs-5"></i>
  <div>
    <strong>Menunggu Persetujuan</strong> — Semua stok cukup. Silakan setujui agar order bisa dikirim ke Cutting.
  </div>
</div>
@endif

{{-- Requirements Table --}}
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-boxes me-2 text-primary"></i>Kebutuhan Bahan Baku</span>
    <small class="text-muted">Berdasarkan BOM × {{ number_format($order->getTotalTargetQty()) }} pcs</small>
  </div>
  @if($requirements->isEmpty())
  <div class="card-body text-center text-muted py-4">
    Belum ada BOM yang terdaftar untuk produk ini.
    <a href="{{ route('bom.index') }}" class="d-block mt-2">Atur BOM di sini</a>
  </div>
  @else
  <div class="table-responsive">
    <table class="table mb-0">
      <thead>
        <tr>
          <th>Kode</th>
          <th>Bahan Baku</th>
          <th>Satuan</th>
          <th class="text-end">Qty Dibutuhkan</th>
          <th class="text-end">Stok Tersedia</th>
          <th class="text-end">Kekurangan</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($requirements as $req)
        <tr class="{{ !$req->is_sufficient ? 'table-danger':'' }}">
          <td><small class="text-monospace text-muted">{{ $req->rawMaterial->code }}</small></td>
          <td class="fw-semibold">{{ $req->rawMaterial->name }}</td>
          <td>{{ $req->rawMaterial->unit }}</td>
          <td class="text-end fw-semibold">{{ number_format($req->qty_needed, 2) }}</td>
          <td class="text-end {{ $req->is_sufficient ? 'text-success':'text-danger' }} fw-semibold">
            {{ number_format($req->qty_available, 2) }}
          </td>
          <td class="text-end">
            @if($req->qty_shortage > 0)
              <span class="text-danger fw-bold">-{{ number_format($req->qty_shortage, 2) }}</span>
            @else
              <span class="text-success">—</span>
            @endif
          </td>
          <td>
            @if($req->is_sufficient)
              <span class="badge bg-success">Cukup</span>
            @else
              <span class="badge bg-danger">Kurang</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

{{-- Quick Link to Purchase Orders --}}
@php $shortages = $requirements->filter(fn($r) => !$r->is_sufficient); @endphp
@if($shortages->count() > 0)
<div class="card">
  <div class="card-header"><i class="bi bi-cart-plus me-2 text-warning"></i>Bahan Perlu Dibeli</div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-sm mb-3">
        <thead><tr><th>Bahan</th><th class="text-end">Kekurangan</th><th class="text-end">Harga Satuan</th><th class="text-end">Est. Biaya</th></tr></thead>
        <tbody>
          @foreach($shortages as $req)
          <tr>
            <td>{{ $req->rawMaterial->name }}</td>
            <td class="text-end text-danger fw-semibold">{{ number_format($req->qty_shortage, 2) }} {{ $req->rawMaterial->unit }}</td>
            <td class="text-end text-muted">Rp {{ number_format($req->rawMaterial->unit_price) }}</td>
            <td class="text-end fw-semibold">Rp {{ number_format($req->qty_shortage * $req->rawMaterial->unit_price) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3">Total Estimasi Pembelian</th>
            <th class="text-end">Rp {{ number_format($shortages->sum(fn($r) => $r->qty_shortage * $r->rawMaterial->unit_price)) }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
    <a href="{{ route('purchase-order.create') }}" class="btn btn-warning"><i class="bi bi-cart-plus me-1"></i>Buat Purchase Order</a>
  </div>
</div>
@endif
@endsection
