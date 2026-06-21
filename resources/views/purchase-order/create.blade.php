@extends('layouts.app')
@section('title','Buat Purchase Order')
@section('page-title','Buat Purchase Order')
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-0">
    <li class="breadcrumb-item"><a href="{{ route('purchase-order.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">Buat PO Baru</li>
  </ol>
</nav>

<form method="POST" action="{{ route('purchase-order.store') }}" id="poForm">
@csrf
<div class="row g-4">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi PO</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
          <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">— Pilih Supplier —</option>
            @foreach($suppliers as $s)
            <option value="{{ $s->id }}" {{ old('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
            @endforeach
          </select>
          @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Tanggal Order <span class="text-danger">*</span></label>
          <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', today()->toDateString()) }}" required>
          @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Estimasi Tiba</label>
          <input type="date" name="expected_date" class="form-control @error('expected_date') is-invalid @enderror" value="{{ old('expected_date') }}">
          @error('expected_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Catatan</label>
          <textarea name="notes" class="form-control" rows="3" placeholder="Instruksi khusus ke supplier...">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2"></i>Item Bahan Baku</span>
        <button type="button" onclick="addRow()" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus me-1"></i>Tambah Item</button>
      </div>
      <div class="table-responsive">
        <table class="table mb-0" id="itemTable">
          <thead><tr><th>Bahan Baku</th><th style="width:120px">Qty Order</th><th style="width:130px">Harga Satuan</th><th style="width:130px">Total</th><th style="width:130px">Catatan</th><th style="width:40px"></th></tr></thead>
          <tbody id="itemBody">
            <tr id="row_0">
              <td><select name="items[0][raw_material_id]" class="form-select form-select-sm" required onchange="updatePrice(0, this)">
                <option value="">— Pilih Material —</option>
                @foreach($materials as $m)<option value="{{ $m->id }}" data-price="{{ $m->unit_price }}" data-unit="{{ $m->unit }}">{{ $m->name }} ({{ $m->unit }})</option>@endforeach
              </select></td>
              <td><input type="number" name="items[0][qty_ordered]" class="form-control form-control-sm qty-input" data-idx="0" step="0.01" min="0.01" required oninput="calcRow(0)"></td>
              <td><input type="number" name="items[0][unit_price]" class="form-control form-control-sm price-input" data-idx="0" step="100" min="0" required oninput="calcRow(0)"></td>
              <td><div class="fw-semibold pt-1 total-display" id="total_0">Rp 0</div></td>
              <td><input type="text" name="items[0][notes]" class="form-control form-control-sm" placeholder="Opsional"></td>
              <td><button type="button" onclick="removeRow(0)" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button></td>
            </tr>
          </tbody>
          <tfoot>
            <tr><td colspan="3" class="text-end fw-bold">TOTAL:</td><td colspan="3" class="fw-bold text-primary" id="grandTotal">Rp 0</td></tr>
          </tfoot>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-end gap-2">
        <a href="{{ route('purchase-order.index') }}" class="btn btn-outline-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Simpan PO</button>
      </div>
    </div>
  </div>
</div>
</form>
@endsection

@push('scripts')
<script>
const materials = @json($materials->map(fn($m) => ['id'=>$m->id,'name'=>$m->name,'unit'=>$m->unit,'price'=>$m->unit_price]));
let rowCount = 1;

function updatePrice(idx, sel) {
  const mat = materials.find(m => m.id == sel.value);
  if (mat) document.querySelector(`[name="items[${idx}][unit_price]"]`).value = mat.price;
  calcRow(idx);
}

function calcRow(idx) {
  const qty   = parseFloat(document.querySelector(`[name="items[${idx}][qty_ordered]"]`)?.value) || 0;
  const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value) || 0;
  const total = qty * price;
  const el = document.getElementById('total_' + idx);
  if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
  calcGrand();
}

function calcGrand() {
  let grand = 0;
  document.querySelectorAll('.qty-input').forEach(q => {
    const idx   = q.dataset.idx;
    const qty   = parseFloat(q.value) || 0;
    const price = parseFloat(document.querySelector(`[name="items[${idx}][unit_price]"]`)?.value) || 0;
    grand += qty * price;
  });
  document.getElementById('grandTotal').textContent = 'Rp ' + grand.toLocaleString('id-ID');
}

function addRow() {
  const idx = rowCount++;
  const matOpts = materials.map(m => `<option value="${m.id}" data-price="${m.price}">${m.name} (${m.unit})</option>`).join('');
  const tr = document.createElement('tr');
  tr.id = 'row_' + idx;
  tr.innerHTML = `
    <td><select name="items[${idx}][raw_material_id]" class="form-select form-select-sm" required onchange="updatePrice(${idx}, this)"><option value="">— Pilih —</option>${matOpts}</select></td>
    <td><input type="number" name="items[${idx}][qty_ordered]" class="form-control form-control-sm qty-input" data-idx="${idx}" step="0.01" min="0.01" required oninput="calcRow(${idx})"></td>
    <td><input type="number" name="items[${idx}][unit_price]" class="form-control form-control-sm price-input" data-idx="${idx}" step="100" min="0" required oninput="calcRow(${idx})"></td>
    <td><div class="fw-semibold pt-1 total-display" id="total_${idx}">Rp 0</div></td>
    <td><input type="text" name="items[${idx}][notes]" class="form-control form-control-sm" placeholder="Opsional"></td>
    <td><button type="button" onclick="removeRow(${idx})" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></button></td>`;
  document.getElementById('itemBody').appendChild(tr);
}

function removeRow(idx) {
  const row = document.getElementById('row_' + idx);
  if (row && document.querySelectorAll('#itemBody tr').length > 1) { row.remove(); calcGrand(); }
}
</script>
@endpush
