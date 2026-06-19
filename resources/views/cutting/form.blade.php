@extends('layouts.app')
@section('title', $plan->exists ? 'Edit Cutting Plan' : 'Buat Cutting Plan')
@section('content')
<div class="mb-4">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-1">
      <li class="breadcrumb-item"><a href="{{ route('cutting.index') }}">Cutting Plan</a></li>
      <li class="breadcrumb-item active">{{ $plan->exists ? 'Edit '.$plan->plan_no : 'Buat Plan Baru' }}</li>
    </ol>
  </nav>
  <h4 class="fw-bold mb-0">{{ $plan->exists ? 'Edit Cutting Plan' : 'Buat Cutting Plan Baru' }}</h4>
</div>

@if($errors->any())
<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<form method="POST" action="{{ $plan->exists ? route('cutting.update', $plan) : route('cutting.store') }}" id="planForm">
  @csrf
  @if($plan->exists) @method('PUT') @endif

  <div class="row g-4">
    {{-- Left: Main Info --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-clipboard-data me-2 text-primary"></i>Informasi Rencana Potong</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Order Produksi <span class="text-danger">*</span></label>
              <select name="production_order_id" id="orderSel" class="form-select @error('production_order_id') is-invalid @enderror" required>
                <option value="">-- Pilih Order Produksi --</option>
                @foreach($orders as $o)
                <option value="{{ $o->id }}" {{ old('production_order_id', $plan->production_order_id) == $o->id ? 'selected' : '' }}>
                  {{ $o->order_no }} — {{ $o->product->name }}
                  <span class="text-muted">(Target: {{ $o->getTotalTargetQty() }} pcs, {{ $o->target_date?->format('d M Y') }})</span>
                </option>
                @endforeach
              </select>
              @error('production_order_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Tanggal Rencana Potong <span class="text-danger">*</span></label>
              <input type="date" name="planned_date" class="form-control @error('planned_date') is-invalid @enderror"
                value="{{ old('planned_date', $plan->planned_date?->format('Y-m-d')) }}" required>
              @error('planned_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Shift</label>
              <select name="shift" class="form-select">
                <option value="">-- Pilih Shift --</option>
                @foreach(['pagi'=>'Pagi (07:00-15:00)','siang'=>'Siang (15:00-23:00)','malam'=>'Malam (23:00-07:00)'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('shift', $plan->shift) == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-semibold">Target Qty (pcs) <span class="text-danger">*</span></label>
              <input type="number" name="planned_qty" class="form-control @error('planned_qty') is-invalid @enderror"
                value="{{ old('planned_qty', $plan->planned_qty) }}" min="1" required>
              @error('planned_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            @if($plan->exists)
            <div class="col-md-6">
              <label class="form-label fw-semibold">Realisasi Qty (pcs)</label>
              <input type="number" name="actual_qty" class="form-control"
                value="{{ old('actual_qty', $plan->actual_qty) }}" min="0">
            </div>
            @endif

            <div class="col-md-6">
              <label class="form-label fw-semibold">Status</label>
              <select name="status" class="form-select">
                @foreach(['draft'=>'Draft','scheduled'=>'Terjadwal','in_progress'=>'Sedang Dipotong','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('status', $plan->status ?? 'draft') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold">Catatan</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes', $plan->notes) }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- Bundle Section (only for create) --}}
      @if(!$plan->exists)
      <div class="card border-0 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span class="fw-semibold"><i class="bi bi-boxes me-2 text-success"></i>Bundle SKU Hasil Potong</span>
          <button type="button" id="addBundle" class="btn btn-sm btn-outline-success"><i class="bi bi-plus me-1"></i>Tambah Bundle</button>
        </div>
        <div class="card-body">
          <div class="alert alert-info py-2 small mb-3"><i class="bi bi-info-circle me-1"></i>Opsional. Isi jika sudah tahu breakdown SKU yang akan dipotong. Bisa ditambah nanti di halaman detail.</div>
          <div id="bundleContainer">
            <div class="row g-2 align-items-end mb-2 bundle-row">
              <div class="col-md-7">
                <label class="form-label small mb-1">SKU</label>
                <select name="bundles[0][sku_id]" class="form-select form-select-sm sku-sel">
                  <option value="">-- Pilih Order dulu --</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small mb-1">Qty (pcs)</label>
                <input type="number" name="bundles[0][qty]" class="form-control form-control-sm" placeholder="0" min="1">
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 remove-bundle"><i class="bi bi-trash"></i></button>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- Right: Marker & Material --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header fw-semibold"><i class="bi bi-rulers me-2 text-info"></i>Data Marker & Kain</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Panjang Marker <small class="text-muted">(meter)</small></label>
              <div class="input-group">
                <input type="number" name="marker_length" id="markerLength" class="form-control" step="0.01" min="0.1"
                  value="{{ old('marker_length', $plan->marker_length) }}" placeholder="0.00">
                <span class="input-group-text text-muted">m</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Lebar Kain <small class="text-muted">(cm)</small></label>
              <div class="input-group">
                <input type="number" name="fabric_width" id="fabricWidth" class="form-control" step="0.1" min="1"
                  value="{{ old('fabric_width', $plan->fabric_width) }}" placeholder="0.0">
                <span class="input-group-text text-muted">cm</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Jumlah Layer (Lapisan)</label>
              <input type="number" name="total_layers" id="totalLayers" class="form-control"
                value="{{ old('total_layers', $plan->total_layers) }}" min="1" placeholder="Contoh: 50">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Efisiensi Marker <small class="text-muted">(%)</small></label>
              <div class="input-group">
                <input type="number" name="efficiency" class="form-control" step="0.1" min="0" max="100"
                  value="{{ old('efficiency', $plan->efficiency) }}" placeholder="Contoh: 85.5">
                <span class="input-group-text text-muted">%</span>
              </div>
              <div class="form-text">Persentase utilisasi kain vs sisa/waste.</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Auto-calc material --}}
      <div class="card border-0 shadow-sm border-info">
        <div class="card-header fw-semibold bg-info bg-opacity-10"><i class="bi bi-calculator me-2 text-info"></i>Estimasi Kebutuhan Kain</div>
        <div class="card-body">
          <div class="row g-2 text-center">
            <div class="col-12">
              <div class="p-3 bg-info bg-opacity-10 rounded">
                <div class="fs-2 fw-bold text-info" id="calcFabric">—</div>
                <small class="text-muted">Total Kain Dibutuhkan</small>
                <div class="text-muted small" id="calcFabricFormula">Isi Panjang Marker × Jumlah Layer</div>
              </div>
            </div>
            <div class="col-12 mt-2">
              <div class="p-2 bg-light rounded small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Kalkulasi otomatis: <strong>Panjang Marker × Jumlah Layer</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
    <a href="{{ route('cutting.index') }}" class="btn btn-outline-secondary">Batal</a>
  </div>
</form>
@endsection
@push('scripts')
<script>
// Auto-calc fabric needed
function calcFabric() {
  const len = parseFloat(document.getElementById('markerLength').value) || 0;
  const layers = parseInt(document.getElementById('totalLayers').value) || 0;
  const el = document.getElementById('calcFabric');
  const formula = document.getElementById('calcFabricFormula');
  if (len > 0 && layers > 0) {
    const total = (len * layers).toFixed(2);
    el.textContent = total + ' m';
    formula.textContent = len + ' m × ' + layers + ' layer = ' + total + ' m';
  } else {
    el.textContent = '—';
    formula.textContent = 'Isi Panjang Marker × Jumlah Layer';
  }
}
document.getElementById('markerLength').addEventListener('input', calcFabric);
document.getElementById('totalLayers').addEventListener('input', calcFabric);
calcFabric();

@if(!$plan->exists)
// Bundle SKU management
let bIdx = 1;
function loadSKUs(sel) {
  const oid = document.getElementById('orderSel').value;
  if (!oid) { sel.innerHTML = '<option value="">-- Pilih Order dulu --</option>'; return; }
  fetch(`/api/order-skus/${oid}`).then(r => r.json()).then(data => {
    sel.innerHTML = '<option value="">-- Pilih SKU --</option>';
    data.forEach(item => {
      const sku = item.sku;
      if (sku) sel.innerHTML += `<option value="${sku.id}">${sku.sku_code}</option>`;
    });
  });
}
document.getElementById('orderSel').addEventListener('change', () => {
  document.querySelectorAll('.sku-sel').forEach(s => loadSKUs(s));
  autoFillBundles();
});

async function autoFillBundles() {
  const oid = document.getElementById('orderSel').value;
  if (!oid) return;
  const r = await fetch(`/api/order-skus/${oid}`);
  const data = await r.json();
  if (!data.length) return;
  // Clear existing rows
  document.getElementById('bundleContainer').innerHTML = '';
  bIdx = 0;
  data.forEach(item => {
    if (!item.sku) return;
    const div = document.createElement('div');
    div.className = 'row g-2 align-items-end mb-2 bundle-row';
    div.innerHTML = `<div class="col-md-7"><label class="form-label small mb-1">SKU</label><select name="bundles[${bIdx}][sku_id]" class="form-select form-select-sm sku-sel"><option value="${item.sku.id}">${item.sku.sku_code}</option></select></div><div class="col-md-3"><label class="form-label small mb-1">Qty (pcs)</label><input type="number" name="bundles[${bIdx}][qty]" class="form-control form-control-sm" value="${item.target_qty}" min="1"></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-bundle"><i class="bi bi-trash"></i></button></div>`;
    document.getElementById('bundleContainer').appendChild(div);
    div.querySelector('.remove-bundle').addEventListener('click', () => div.remove());
    bIdx++;
  });
  // Update planned_qty with total target
  const total = data.reduce((s, i) => s + (i.target_qty || 0), 0);
  const qtyInput = document.querySelector('[name="planned_qty"]');
  if (qtyInput && !qtyInput.value) qtyInput.value = total;
}
document.getElementById('addBundle').addEventListener('click', () => {
  const div = document.createElement('div');
  div.className = 'row g-2 align-items-end mb-2 bundle-row';
  div.innerHTML = `<div class="col-md-7"><label class="form-label small mb-1">SKU</label><select name="bundles[${bIdx}][sku_id]" class="form-select form-select-sm sku-sel"><option value="">-- Pilih SKU --</option></select></div><div class="col-md-3"><label class="form-label small mb-1">Qty (pcs)</label><input type="number" name="bundles[${bIdx}][qty]" class="form-control form-control-sm" placeholder="0" min="1"></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger w-100 remove-bundle"><i class="bi bi-trash"></i></button></div>`;
  document.getElementById('bundleContainer').appendChild(div);
  loadSKUs(div.querySelector('.sku-sel'));
  div.querySelector('.remove-bundle').addEventListener('click', () => div.remove());
  bIdx++;
});
document.querySelectorAll('.remove-bundle').forEach(b => b.addEventListener('click', () => b.closest('.bundle-row').remove()));
@endif
</script>
@endpush
