@extends('layouts.app')
@section('title','WIP Tracker')
@section('page-title','WIP Tracker')
@section('content')
<!-- Station Summary -->
<div class="row g-3 mb-4">
  @foreach($stationSummary as $s)
  <div class="col-6 col-md-4 col-lg-2">
    <div class="card text-center border-{{ $s['is_bottleneck'] ? 'danger' : '' }}" style="{{ $s['is_bottleneck'] ? 'border-top:3px solid #dc2626' : 'border-top:3px solid #059669' }}">
      <div class="card-body py-3">
        <div class="fw-bold text-{{ $s['is_bottleneck'] ? 'danger' : 'primary' }}" style="font-size:.85rem">{{ $s['station']->name }}</div>
        @if($s['is_bottleneck'])<span class="badge bg-danger" style="font-size:.6rem">BOTTLENECK</span>@endif
        <div class="fs-3 fw-bold mt-1">{{ number_format($s['wip']) }}</div>
        <small class="text-muted">unit WIP</small>
      </div>
    </div>
  </div>
  @endforeach
</div>

<div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
  <h5 class="mb-0 fw-bold">Input WIP</h5>
  @if(!auth()->user()->isManager())
  <div class="d-flex gap-2 flex-wrap align-items-center">
      <x-import-button import-route="{{ route('import.wip') }}" template-route="{{ route('import.template.wip') }}" label="WIP" />
      <a href="{{ route('wip.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Input WIP Baru</a>
  </div>
  @endif
</div>
<x-import-result />

<!-- Filter -->
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-12 col-md-3">
        <select name="station_id" class="form-select form-select-sm">
          <option value="">Semua Stasiun</option>
          @foreach($stations as $st)<option value="{{ $st->id }}" {{ request('station_id')==$st->id ? 'selected' : '' }}>{{ $st->name }}</option>@endforeach
        </select>
      </div>
      <div class="col-12 col-md-3">
        <select name="order_id" class="form-select form-select-sm">
          <option value="">Semua Order</option>
          @foreach($orders as $o)<option value="{{ $o->id }}" {{ request('order_id')==$o->id ? 'selected' : '' }}>{{ $o->order_no }}</option>@endforeach
        </select>
      </div>
      <div class="col-12 col-md-2"><input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}"></div>
      <div class="col-6 col-md-2"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
      <div class="col-6 col-md-2"><a href="{{ route('wip.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Tanggal</th><th>Order</th><th class="d-mob-none">SKU</th><th>Stasiun</th><th class="text-center">Masuk</th><th class="text-center">Keluar</th><th class="text-center d-mob-none">Reject</th><th class="text-center">WIP</th><th class="d-mob-none">Input Oleh</th></tr></thead>
      <tbody>
        @forelse($entries as $e)
        <tr>
          <td><small>{{ $e->input_date->format('d M Y') }}</small></td>
          <td><a href="{{ route('orders.show',$e->order) }}" class="text-primary fw-semibold text-decoration-none">{{ $e->order->order_no }}</a></td>
          <td class="d-mob-none"><small class="font-monospace">{{ $e->sku->sku_code }}</small><br><small class="text-muted">{{ optional($e->sku->color)->name }} / {{ optional($e->sku->size)->name }}</small></td>
          <td><span class="badge bg-primary text-white">{{ $e->station->name }}</span></td>
          <td class="text-center text-success fw-semibold">{{ number_format($e->qty_in) }}</td>
          <td class="text-center text-primary fw-semibold">{{ number_format($e->qty_out) }}</td>
          <td class="text-center text-danger fw-semibold d-mob-none">{{ number_format($e->qty_reject) }}</td>
          <td class="text-center"><span class="badge bg-info text-white fw-bold">{{ $e->qty_in_process }}</span></td>
          <td class="d-mob-none"><small>{{ $e->creator->name }}</small></td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Belum ada data WIP</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($entries->hasPages())
  <div class="card-footer py-3">{{ $entries->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
