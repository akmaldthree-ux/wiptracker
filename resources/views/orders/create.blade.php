@extends('layouts.app')
@section('title','Buat Order Produksi')
@section('page-title','Buat Order Produksi')
@section('content')
<div class="card form-card-container mx-auto" style="max-width:900px">
  <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Order Produksi Baru</div>
  <div class="card-body">
    <form method="POST" action="{{ route('orders.store') }}" id="orderForm">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
          <select name="product_id" id="productSelect" class="form-select" required>
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $p)
            <option value="{{ $p->id }}" {{ old('product_id')==$p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-semibold">Series <span class="text-danger">*</span></label>
          <select name="series_id" id="seriesSelect" class="form-select" required>
            <option value="">-- Pilih Produk dulu --</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Target Tanggal Selesai <span class="text-danger">*</span></label>
          <input type="date" name="target_date" class="form-control" value="{{ old('target_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Harga Jual / Unit (Rp)</label>
          <input type="number" name="selling_price" class="form-control" value="{{ old('selling_price') }}" placeholder="0" min="0">
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Catatan</label>
          <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Catatan opsional">
        </div>
      </div>

      <hr class="my-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Item SKU & Target Qty</h6>
        <button type="button" id="addSku" class="btn btn-sm btn-outline-primary" disabled><i class="bi bi-plus me-1"></i>Tambah SKU</button>
      </div>
      <div id="skuContainer">
        <div class="text-muted text-center py-4" id="skuPlaceholder"><i class="bi bi-arrow-up-circle me-1"></i>Pilih Produk dan Series terlebih dahulu</div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Simpan Order</button>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
let skuIndex = 0;
const productSelect = document.getElementById('productSelect');
const seriesSelect = document.getElementById('seriesSelect');
const addSkuBtn = document.getElementById('addSku');
const skuContainer = document.getElementById('skuContainer');
const skuPlaceholder = document.getElementById('skuPlaceholder');

productSelect.addEventListener('change', function() {
  const pid = this.value;
  seriesSelect.innerHTML = '<option value="">-- Memuat series... --</option>';
  seriesSelect.disabled = true;
  if (!pid) return;
  fetch(`/api/series-by-product/${pid}`).then(r=>r.json()).then(data => {
    seriesSelect.innerHTML = '<option value="">-- Pilih Series --</option>';
    data.forEach(s => seriesSelect.innerHTML += `<option value="${s.id}">${s.code} — ${s.name}</option>`);
    seriesSelect.disabled = false;
  }).catch(() => {
    // fallback: load all series for product via simple approach
    seriesSelect.innerHTML = '<option value="">-- Error memuat series --</option>';
    seriesSelect.disabled = false;
  });
});

seriesSelect.addEventListener('change', function() {
  const sid = this.value;
  addSkuBtn.disabled = !sid;
  skuContainer.innerHTML = '';
  if (!sid) { skuContainer.appendChild(skuPlaceholder); return; }
  addSku();
});

addSkuBtn.addEventListener('click', addSku);

function addSku() {
  const sid = seriesSelect.value;
  if (!sid) return;
  const idx = skuIndex++;
  const div = document.createElement('div');
  div.className = 'row g-2 align-items-end mb-2 sku-row';
  div.innerHTML = `
    <div class="col-md-7">
      <label class="form-label small fw-semibold">SKU</label>
      <select name="skus[${idx}][sku_id]" class="form-select form-select-sm">
        <option value="">-- Memuat SKU... --</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label small fw-semibold">Target Qty</label>
      <input type="number" name="skus[${idx}][target_qty]" class="form-control form-control-sm" min="1" placeholder="0">
    </div>
    <div class="col-md-2">
      <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-sku"><i class="bi bi-trash"></i></button>
    </div>`;
  skuContainer.appendChild(div);
  div.querySelector('.remove-sku').addEventListener('click', () => div.remove());
  // Load SKUs
  fetch(`/api/skus-by-series/${sid}`).then(r=>r.json()).then(data => {
    const sel = div.querySelector('select');
    sel.innerHTML = '<option value="">-- Pilih SKU --</option>';
    data.forEach(s => sel.innerHTML += `<option value="${s.id}">${s.sku_code}</option>`);
  }).catch(() => {});
}
</script>
@endpush
