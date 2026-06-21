@extends('layouts.app')
@section('title','Bill of Materials')
@section('page-title','Bill of Materials')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="mb-0 fw-bold">Bill of Materials (BOM)</h5>
    <p class="text-muted small mb-0">Kebutuhan bahan baku per 1 pcs setiap produk</p>
  </div>
</div>

{{-- Kalkulator BOM --}}
<div class="card mb-4">
  <div class="card-header d-flex align-items-center gap-2">
    <i class="bi bi-calculator text-primary"></i>
    <span>Kalkulator Kebutuhan Material</span>
  </div>
  <div class="card-body">
    <div class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label fw-semibold">Produk</label>
        <select id="calcProduct" class="form-select">
          <option value="">— Pilih Produk —</option>
          @foreach($products as $p)
          <option value="{{ $p->id }}">{{ $p->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Jumlah Order (pcs)</label>
        <input type="number" id="calcQty" class="form-control" min="1" value="100">
      </div>
      <div class="col-md-2">
        <button onclick="calculateBom()" class="btn btn-primary w-100"><i class="bi bi-calculator me-1"></i>Hitung</button>
      </div>
    </div>
    <div id="calcResult" class="mt-3" style="display:none">
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

{{-- BOM per Produk --}}
@foreach($products as $product)
<div class="card mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-layers text-primary"></i>
      <span>{{ $product->name }}</span>
      <span class="badge bg-secondary">{{ $product->category }}</span>
    </div>
    @if(in_array(auth()->user()->role,['admin','supervisor']))
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#addForm{{ $product->id }}">
      <i class="bi bi-plus me-1"></i>Tambah Material
    </button>
    @endif
  </div>

  @if($product->bomItems->count())
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Material</th><th>Satuan</th><th class="text-end">Qty/pcs</th><th class="text-end">Waste %</th><th>Catatan</th><th>Stok Saat Ini</th><th></th></tr></thead>
      <tbody>
        @foreach($product->bomItems as $bom)
        <tr>
          <td><span class="fw-semibold">{{ $bom->rawMaterial->name }}</span><br><small class="text-muted">{{ $bom->rawMaterial->code }}</small></td>
          <td>{{ $bom->rawMaterial->unit }}</td>
          <td class="text-end fw-semibold">{{ $bom->qty_per_unit }}</td>
          <td class="text-end">{{ $bom->waste_percentage }}%</td>
          <td><small class="text-muted">{{ $bom->notes ?? '-' }}</small></td>
          <td>
            @php $ok = $bom->rawMaterial->current_stock >= 0; @endphp
            <span class="{{ $bom->rawMaterial->isBelowMinStock() ? 'text-danger fw-semibold' : 'text-success' }}">
              {{ number_format($bom->rawMaterial->current_stock, 2) }} {{ $bom->rawMaterial->unit }}
              @if($bom->rawMaterial->isBelowMinStock())<i class="bi bi-exclamation-triangle-fill ms-1" title="Stok rendah"></i>@endif
            </span>
          </td>
          <td>
            @if(in_array(auth()->user()->role,['admin','supervisor']))
            <form method="POST" action="{{ route('bom.destroy', $bom) }}" class="d-inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus BOM item ini?')"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @else
  <div class="empty-state py-3"><i class="bi bi-box-seam"></i><p class="fw-semibold mb-1">Belum ada BOM</p><p>Tambahkan kebutuhan bahan baku untuk produk ini</p></div>
  @endif

  {{-- Form tambah item --}}
  @if(in_array(auth()->user()->role,['admin','supervisor']))
  <div class="collapse" id="addForm{{ $product->id }}">
    <div class="card-footer">
      <form method="POST" action="{{ route('bom.store') }}" class="row g-2 align-items-end">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <div class="col-md-4">
          <label class="form-label fw-semibold small">Bahan Baku</label>
          <select name="raw_material_id" class="form-select form-select-sm" required>
            <option value="">— Pilih —</option>
            @foreach(\App\Models\RawMaterial::where('is_active',true)->orderBy('name')->get() as $rm)
            <option value="{{ $rm->id }}">{{ $rm->name }} ({{ $rm->unit }})</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small">Qty per pcs</label>
          <input type="number" name="qty_per_unit" class="form-control form-control-sm" step="0.0001" min="0.0001" placeholder="0.5" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold small">Waste %</label>
          <input type="number" name="waste_percentage" class="form-control form-control-sm" step="0.1" min="0" max="100" value="5" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold small">Catatan</label>
          <input type="text" name="notes" class="form-control form-control-sm" placeholder="Opsional...">
        </div>
        <div class="col-md-1">
          <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-save"></i></button>
        </div>
      </form>
    </div>
  </div>
  @endif
</div>
@endforeach
@endsection

@push('scripts')
<script>
function calculateBom() {
  const productId = document.getElementById('calcProduct').value;
  const qty = document.getElementById('calcQty').value;
  if (!productId || !qty) return alert('Pilih produk dan masukkan jumlah order.');

  fetch(`/api/bom/calculate?product_id=${productId}&qty=${qty}`)
    .then(r => r.json())
    .then(data => {
      const tbody = document.getElementById('calcTableBody');
      if (!data.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Tidak ada BOM untuk produk ini</td></tr>'; }
      else {
        tbody.innerHTML = data.map(d => `
          <tr class="${d.sufficient ? '' : 'table-danger'}">
            <td>${d.material}</td>
            <td class="fw-semibold">${d.qty_needed.toLocaleString('id-ID', {maximumFractionDigits:4})} ${d.unit}</td>
            <td>${parseFloat(d.stock).toLocaleString('id-ID', {maximumFractionDigits:2})} ${d.unit}</td>
            <td><span class="badge bg-${d.sufficient ? 'success' : 'danger'}">${d.sufficient ? '✓ Cukup' : '✗ Kurang'}</span></td>
            <td class="${d.sufficient ? 'text-muted' : 'text-danger fw-bold'}">${d.sufficient ? '—' : parseFloat(d.shortage).toLocaleString('id-ID', {maximumFractionDigits:4}) + ' ' + d.unit}</td>
          </tr>`).join('');
      }
      document.getElementById('calcResult').style.display = 'block';
    });
}
</script>
@endpush
