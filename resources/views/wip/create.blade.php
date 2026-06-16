@extends('layouts.app')
@section('title','Input WIP')
@section('page-title','Input Data WIP')
@section('content')
<div class="card" style="max-width:700px">
  <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Input WIP</div>
  <div class="card-body">
    <form method="POST" action="{{ route('wip.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Order Produksi <span class="text-danger">*</span></label>
          <select name="production_order_id" id="orderSelect" class="form-select" required>
            <option value="">-- Pilih Order --</option>
            @foreach($orders as $o)<option value="{{ $o->id }}" {{ old('production_order_id')==$o->id ? 'selected' : '' }}>{{ $o->order_no }} — {{ $o->product->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Stasiun <span class="text-danger">*</span></label>
          <select name="station_id" class="form-select" required>
            <option value="">-- Pilih Stasiun --</option>
            @foreach($stations as $st)<option value="{{ $st->id }}" {{ (old('station_id') ?? optional($myStation)->id)==$st->id ? 'selected' : '' }}>{{ $st->name }}</option>@endforeach
          </select>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
          <select name="sku_id" id="skuSelect" class="form-select" required>
            <option value="">-- Pilih Order dulu --</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Qty Masuk</label>
          <input type="number" name="qty_in" class="form-control" value="{{ old('qty_in',0) }}" min="0" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Qty Keluar</label>
          <input type="number" name="qty_out" class="form-control" value="{{ old('qty_out',0) }}" min="0" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Qty Reject</label>
          <input type="number" name="qty_reject" class="form-control" value="{{ old('qty_reject',0) }}" min="0" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Tanggal Input <span class="text-danger">*</span></label>
          <input type="date" name="input_date" class="form-control" value="{{ old('input_date',today()->toDateString()) }}" required>
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Catatan</label>
          <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Opsional">
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Simpan</button>
        <a href="{{ route('wip.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('orderSelect').addEventListener('change', function() {
  const oid = this.value;
  const sel = document.getElementById('skuSelect');
  sel.innerHTML = '<option value="">-- Memuat SKU... --</option>';
  if (!oid) return;
  fetch(`/api/order-skus/${oid}`).then(r=>r.json()).then(data => {
    sel.innerHTML = '<option value="">-- Pilih SKU --</option>';
    data.forEach(item => sel.innerHTML += `<option value="${item.sku_id}">${item.sku?.sku_code || ''} (${item.sku?.color?.name||''} / ${item.sku?.size?.name||''})</option>`);
  }).catch(() => sel.innerHTML = '<option value="">-- Error --</option>');
});
</script>
@endpush
