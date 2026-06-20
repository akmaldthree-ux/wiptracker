@extends('layouts.app')
@section('title','Input Penerimaan')
@section('page-title','Input Penerimaan Bahan Baku')
@section('content')
<div class="card" style="max-width:600px">
  <div class="card-header"><i class="bi bi-box-arrow-in-down me-2 text-success"></i>Penerimaan: {{ $rawMaterial->name }}</div>
  <div class="card-body">
    <div class="alert alert-info py-2 mb-4"><small>Stok saat ini: <strong>{{ number_format($rawMaterial->current_stock) }} {{ $rawMaterial->unit }}</strong></small></div>
    <form method="POST" action="{{ url('bahan-baku/'.$rawMaterial->id.'/receipt') }}">
      @csrf
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label fw-semibold">Supplier</label>
          @php $activeSuppliers = \App\Models\Supplier::where('is_active', true)->orderBy('name')->get(); @endphp
          <select name="supplier_id" class="form-select">
            <option value="">-- Pilih Supplier (opsional) --</option>
            @foreach($activeSuppliers as $sup)
            <option value="{{ $sup->id }}" {{ old('supplier_id')==$sup->id?'selected':'' }}>{{ $sup->name }} ({{ $sup->code }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6"><label class="form-label fw-semibold">Qty Diterima <span class="text-danger">*</span></label><input type="number" name="qty" class="form-control" min="0.01" step="0.01" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Harga Satuan (Rp) <span class="text-danger">*</span></label><input type="number" name="unit_price" class="form-control" value="{{ $rawMaterial->unit_price }}" min="0" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Tanggal Terima <span class="text-danger">*</span></label><input type="date" name="receipt_date" class="form-control" value="{{ today()->toDateString() }}" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Nama Supplier (teks)</label><input type="text" name="supplier" class="form-control" placeholder="Nama supplier (opsional)"></div>
        <div class="col-md-6"><label class="form-label fw-semibold">No. PO</label><input type="text" name="po_no" class="form-control" placeholder="Nomor PO"></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Catatan</label><input type="text" name="notes" class="form-control"></div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Konfirmasi Penerimaan</button>
        <a href="{{ url('bahan-baku/'.$rawMaterial->id) }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
