@extends('layouts.app')
@section('title','Bill of Materials')
@section('page-title','Bill of Materials')
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Bill of Materials</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h5 class="mb-0 fw-bold">Bill of Materials (BOM) per SKU</h5>
    <p class="text-muted small mb-0">Kebutuhan bahan baku per series, warna, dan size</p>
  </div>
  @if(auth()->user()->isSupervisor())
  <x-import-button import-route="{{ route('import.bom') }}" template-route="{{ route('import.template.bom') }}" label="BOM" />
  @endif
</div>

<x-import-result />

@if(session('error'))
<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
@endif

<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2">
    <i class="bi bi-calculator text-primary"></i>
    <span>Kalkulator Kebutuhan Material per SKU</span>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-6">
        <label class="form-label fw-semibold">SKU</label>
        <select id="calcSku" class="form-select">
          <option value="">— Pilih SKU —</option>
          @foreach($allSkus as $sku)
          <option value="{{ $sku->id }}">{{ $sku->sku_code }} — {{ $sku->series->name }} / {{ $sku->color->name }} / {{ $sku->size->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Jumlah Order (pcs)</label>
        <input type="number" id="calcQty" class="form-control" min="1" value="100">
      </div>
      <div class="col-md-2">
        <button type="button" onclick="calculateBom()" class="btn btn-primary w-100"><i class="bi bi-calculator me-1"></i>Hitung</button>
      </div>
    </div>
    <div id="calcResult" class="mt-3 d-none">
      <h6 class="fw-semibold mb-2">Kebutuhan Material:</h6>
      <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
          <thead class="table-light"><tr><th>Material</th><th>Qty Dibutuhkan</th><th>Stok Saat Ini</th><th>Status</th><th>Kekurangan</th></tr></thead>
          <tbody id="calcTableBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label small fw-semibold">Cari SKU, series, warna, atau size</label>
        <input type="text" name="search" class="form-control form-control-sm" value="{{ request('search') }}" placeholder="Contoh: XL atau SKU-JBH...">
      </div>
      <div class="col-md-4">
        <label class="form-label small fw-semibold">Produk</label>
        <select name="product_id" class="form-select form-select-sm">
          <option value="">Semua Produk</option>
          @foreach($products as $product)
          <option value="{{ $product->id }}" @selected((string) request('product_id') === (string) $product->id)>{{ $product->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
      <div class="col-md-1"><a href="{{ route('bom.index') }}" class="btn btn-sm btn-outline-secondary w-100" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a></div>
    </form>
  </div>
</div>

@forelse($skus as $sku)
<div class="card mb-3">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
      <span class="font-monospace fw-bold text-primary">{{ $sku->sku_code }}</span>
      <span class="ms-2 fw-semibold">{{ $sku->product->name }} — {{ $sku->series->name }}</span>
      <span class="badge bg-light text-dark border ms-1">{{ $sku->color->name }}</span>
      <span class="badge bg-primary ms-1">Size {{ $sku->size->name }}</span>
    </div>
    @if(auth()->user()->isSupervisor())
    <div class="d-flex gap-1">
      @if($sku->bomItems->isNotEmpty())
      <button type="button" class="btn btn-sm btn-outline-secondary js-copy-bom" data-bs-toggle="modal" data-bs-target="#copyBomModal" data-source-id="{{ $sku->id }}" data-source-code="{{ $sku->sku_code }}">
        <i class="bi bi-copy me-1"></i>Salin BOM
      </button>
      @endif
      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addForm{{ $sku->id }}">
        <i class="bi bi-plus me-1"></i>Tambah Material
      </button>
    </div>
    @endif
  </div>

  @if($sku->bomItems->isNotEmpty())
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Material</th><th>Warna Material</th><th>Satuan</th><th class="text-end">Qty/pcs</th><th class="text-end">Waste</th><th>Catatan</th><th></th></tr></thead>
      <tbody>
        @foreach($sku->bomItems as $bom)
        <tr>
          <td><span class="fw-semibold">{{ $bom->rawMaterial->name }}</span><br><small class="font-monospace text-muted">{{ $bom->rawMaterial->code }}</small></td>
          <td>{{ $bom->rawMaterial->color ?? '—' }}</td>
          <td>{{ $bom->rawMaterial->unit }}</td>
          <td class="text-end fw-semibold">{{ number_format($bom->qty_per_unit, 4) }}</td>
          <td class="text-end">{{ number_format($bom->waste_percentage, 2) }}%</td>
          <td><small class="text-muted">{{ $bom->notes ?? '—' }}</small></td>
          <td class="text-nowrap">
            @if(auth()->user()->isSupervisor())
            <button type="button" class="btn btn-sm btn-outline-secondary js-edit-bom" data-bs-toggle="modal" data-bs-target="#editBomModal"
              data-qty="{{ $bom->qty_per_unit }}" data-waste="{{ $bom->waste_percentage }}" data-notes="{{ $bom->notes }}"
              data-material="{{ $bom->rawMaterial->name }}" data-update-url="{{ route('bom.update', $bom) }}"><i class="bi bi-pencil"></i></button>
            <form method="POST" action="{{ route('bom.destroy', $bom) }}" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus material ini dari BOM {{ $sku->sku_code }}?')"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="card-body py-3 text-center text-muted">
    <i class="bi bi-exclamation-circle me-1"></i>SKU ini belum memiliki BOM dan belum dapat disetujui di procurement.
  </div>
  @endif

  @if(auth()->user()->isSupervisor())
  <div class="collapse" id="addForm{{ $sku->id }}">
    <div class="card-footer">
      <form method="POST" action="{{ route('bom.store') }}" class="row g-2 align-items-end">
        @csrf
        <input type="hidden" name="sku_id" value="{{ $sku->id }}">
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Bahan Baku</label>
          <select name="raw_material_id" class="form-select form-select-sm" required>
            <option value="">— Pilih —</option>
            @foreach($materials as $material)
            <option value="{{ $material->id }}">{{ $material->code }} — {{ $material->name }}{{ $material->color ? ' / '.$material->color : '' }} ({{ $material->unit }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small">Qty per pcs</label>
          <input type="number" name="qty_per_unit" class="form-control form-control-sm" step="0.0001" min="0.0001" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small">Waste %</label>
          <input type="number" name="waste_percentage" class="form-control form-control-sm" step="0.1" min="0" max="100" value="5" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Catatan</label>
          <input type="text" name="notes" class="form-control form-control-sm" maxlength="255">
        </div>
        <div class="col-md-1"><button class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i></button></div>
      </form>
    </div>
  </div>
  @endif
</div>
@empty
<div class="card"><div class="card-body text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Tidak ada SKU yang sesuai filter.</div></div>
@endforelse

@if($skus->hasPages())
<div class="mt-3">{{ $skus->links() }}</div>
@endif
@endsection

@if(auth()->user()->isSupervisor())
@push('modals')
<div class="modal fade" id="editBomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><form method="POST" id="editBomForm" class="modal-content">
    @csrf @method('PATCH')
    <div class="modal-header"><h5 class="modal-title">Edit BOM <span id="editBomMaterialName"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="mb-3"><label class="form-label fw-semibold">Qty per pcs</label><input type="number" name="qty_per_unit" id="editBomQty" class="form-control" step="0.0001" min="0.0001" required></div>
      <div class="mb-3"><label class="form-label fw-semibold">Waste %</label><input type="number" name="waste_percentage" id="editBomWaste" class="form-control" step="0.1" min="0" max="100" required></div>
      <div><label class="form-label fw-semibold">Catatan</label><input type="text" name="notes" id="editBomNotes" class="form-control" maxlength="255"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
  </form></div>
</div>

<div class="modal fade" id="copyBomModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><form method="POST" action="{{ route('bom.copy') }}" class="modal-content">
    @csrf
    <input type="hidden" name="source_sku_id" id="copyBomSourceId">
    <div class="modal-header"><h5 class="modal-title">Salin BOM <span id="copyBomSourceCode" class="font-monospace"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <div class="alert alert-warning small">Material yang sama pada SKU tujuan akan diperbarui. Material lain yang sudah ada tidak akan dihapus.</div>
      <label class="form-label fw-semibold">SKU Tujuan</label>
      <select name="target_sku_ids[]" id="copyBomTargets" class="form-select" multiple size="10" required>
        @foreach($allSkus as $targetSku)
        <option value="{{ $targetSku->id }}">{{ $targetSku->sku_code }} — {{ $targetSku->series->name }} / {{ $targetSku->color->name }} / {{ $targetSku->size->name }}</option>
        @endforeach
      </select>
      <div class="form-text">Gunakan Ctrl/Cmd untuk memilih lebih dari satu SKU.</div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary"><i class="bi bi-copy me-1"></i>Salin BOM</button></div>
  </form></div>
</div>
@endpush
@endif

@push('scripts')
<script>
function calculateBom() {
  const skuId = document.getElementById('calcSku').value;
  const qty = document.getElementById('calcQty').value;
  if (!skuId || !qty) return alert('Pilih SKU dan masukkan jumlah order.');

  fetch(`/api/bom/calculate?sku_id=${encodeURIComponent(skuId)}&qty=${encodeURIComponent(qty)}`)
    .then(response => response.json())
    .then(data => {
      const body = document.getElementById('calcTableBody');
      body.innerHTML = data.length ? data.map(item => `
        <tr class="${item.sufficient ? '' : 'table-danger'}">
          <td>${item.material}</td>
          <td class="fw-semibold">${item.qty_needed.toLocaleString('id-ID', {maximumFractionDigits: 4})} ${item.unit}</td>
          <td>${parseFloat(item.stock).toLocaleString('id-ID', {maximumFractionDigits: 2})} ${item.unit}</td>
          <td><span class="badge bg-${item.sufficient ? 'success' : 'danger'}">${item.sufficient ? 'Cukup' : 'Kurang'}</span></td>
          <td>${item.sufficient ? '—' : parseFloat(item.shortage).toLocaleString('id-ID', {maximumFractionDigits: 4}) + ' ' + item.unit}</td>
        </tr>`).join('') : '<tr><td colspan="5" class="text-center text-danger">SKU ini belum memiliki BOM.</td></tr>';
      document.getElementById('calcResult').classList.remove('d-none');
    });
}

document.addEventListener('DOMContentLoaded', function () {
  const editModal = document.getElementById('editBomModal');
  if (editModal) editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    document.getElementById('editBomForm').action = button.dataset.updateUrl;
    document.getElementById('editBomMaterialName').textContent = button.dataset.material;
    document.getElementById('editBomQty').value = button.dataset.qty;
    document.getElementById('editBomWaste').value = button.dataset.waste;
    document.getElementById('editBomNotes').value = button.dataset.notes || '';
  });

  const copyModal = document.getElementById('copyBomModal');
  if (copyModal) copyModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const sourceId = button.dataset.sourceId;
    document.getElementById('copyBomSourceId').value = sourceId;
    document.getElementById('copyBomSourceCode').textContent = button.dataset.sourceCode;
    Array.from(document.getElementById('copyBomTargets').options).forEach(option => {
      option.disabled = option.value === sourceId;
      option.selected = false;
    });
  });
});
</script>
@endpush
