@extends('layouts.app')
@section('title', 'Detail Cutting Plan')
@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">
      <i class="bi bi-scissors me-2 text-primary"></i>{{ $cutting->plan_no }}
      <span class="badge bg-{{ $cutting->status_color }} ms-1">{{ $cutting->status_label }}</span>
    </h4>
    <p class="text-muted mb-0">{{ $cutting->order->order_no }} — {{ $cutting->order->product->name }} | {{ $cutting->planned_date->format('d F Y') }}</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    {{-- Quick status update --}}
    @if(!in_array($cutting->status, ['completed','cancelled']))
    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-arrow-repeat me-1"></i>Ubah Status
      </button>
      <ul class="dropdown-menu">
        @foreach(['draft'=>['Draft','secondary'],'scheduled'=>['Terjadwal','primary'],'in_progress'=>['Sedang Dipotong','warning'],'completed'=>['Selesai','success'],'cancelled'=>['Dibatalkan','danger']] as $val=>[$lbl,$col])
        @if($val !== $cutting->status)
        <li>
          <form method="POST" action="{{ route('cutting.status', $cutting) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $val }}">
            <button type="submit" class="dropdown-item">
              <span class="badge bg-{{ $col }} me-2">{{ $lbl }}</span>
            </button>
          </form>
        </li>
        @endif
        @endforeach
      </ul>
    </div>
    @endif
    <a href="{{ route('cutting.edit', $cutting) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    <a href="{{ route('cutting.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="fs-2 fw-bold text-primary">{{ number_format($cutting->planned_qty) }}</div>
        <small class="text-muted">Target Qty</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="fs-2 fw-bold {{ $cutting->actual_qty ? 'text-success' : 'text-muted' }}">
          {{ $cutting->actual_qty ? number_format($cutting->actual_qty) : '—' }}
        </div>
        <small class="text-muted">Realisasi Qty</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="fs-2 fw-bold text-info">{{ $cutting->fabric_needed ? $cutting->fabric_needed.' m' : '—' }}</div>
        <small class="text-muted">Estimasi Kain</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-body text-center">
        <div class="fs-2 fw-bold text-warning">{{ $cutting->efficiency ? $cutting->efficiency.'%' : '—' }}</div>
        <small class="text-muted">Efisiensi Marker</small>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mb-4">
  {{-- Info Detail --}}
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold"><i class="bi bi-info-circle me-2"></i>Informasi Plan</div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" width="45%">No. Plan</td><td class="fw-semibold font-monospace">{{ $cutting->plan_no }}</td></tr>
          <tr><td class="text-muted">Order</td><td><a href="{{ route('orders.show', $cutting->order) }}">{{ $cutting->order->order_no }}</a></td></tr>
          <tr><td class="text-muted">Produk</td><td>{{ $cutting->order->product->name }}</td></tr>
          <tr><td class="text-muted">Tanggal Potong</td><td class="fw-semibold">{{ $cutting->planned_date->format('d M Y') }}</td></tr>
          <tr><td class="text-muted">Shift</td><td>{{ $cutting->shift ? ucfirst($cutting->shift) : '-' }}</td></tr>
          <tr><td class="text-muted">Dibuat Oleh</td><td>{{ $cutting->creator->name }}</td></tr>
          <tr><td class="text-muted">Dibuat Pada</td><td>{{ $cutting->created_at->format('d M Y H:i') }}</td></tr>
          @if($cutting->notes)
          <tr><td class="text-muted">Catatan</td><td>{{ $cutting->notes }}</td></tr>
          @endif
        </table>
      </div>
    </div>
  </div>

  {{-- Marker Data --}}
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold"><i class="bi bi-rulers me-2 text-info"></i>Data Marker & Kain</div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" width="50%">Panjang Marker</td><td class="fw-semibold">{{ $cutting->marker_length ? $cutting->marker_length.' m' : '—' }}</td></tr>
          <tr><td class="text-muted">Lebar Kain</td><td class="fw-semibold">{{ $cutting->fabric_width ? $cutting->fabric_width.' cm' : '—' }}</td></tr>
          <tr><td class="text-muted">Jumlah Layer</td><td class="fw-semibold">{{ $cutting->total_layers ? number_format($cutting->total_layers).' layer' : '—' }}</td></tr>
          <tr>
            <td class="text-muted">Total Kain Dibutuhkan</td>
            <td class="fw-bold text-info fs-5">{{ $cutting->fabric_needed ? $cutting->fabric_needed.' m' : '—' }}</td>
          </tr>
          <tr><td class="text-muted">Efisiensi Marker</td><td class="fw-semibold">{{ $cutting->efficiency ? $cutting->efficiency.'%' : '—' }}</td></tr>
        </table>

        @if($cutting->planned_qty > 0)
        <div class="mt-3">
          <div class="d-flex justify-content-between small text-muted mb-1">
            <span>Progress Realisasi</span>
            <span>{{ $cutting->progress }}%</span>
          </div>
          <div class="progress" style="height:10px">
            <div class="progress-bar bg-{{ $cutting->status_color }}" style="width:{{ $cutting->progress }}%"></div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

{{-- Bundle Table --}}
<div class="card border-0 shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span class="fw-semibold"><i class="bi bi-boxes me-2 text-success"></i>Bundle SKU ({{ $cutting->bundles->count() }} bundle)</span>
    @if(!in_array($cutting->status,['cancelled']))
    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#addBundleModal">
      <i class="bi bi-plus me-1"></i>Tambah Bundle
    </button>
    @endif
  </div>
  @if($cutting->bundles->count() > 0)
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No. Bundle</th>
          <th>SKU Code</th>
          <th>Warna</th>
          <th>Ukuran</th>
          <th class="text-center">Qty</th>
          <th class="text-center">Status</th>
          <th class="text-center">Ubah Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($cutting->bundles as $bundle)
        <tr>
          <td class="font-monospace fw-semibold small">{{ $bundle->bundle_no }}</td>
          <td><span class="badge bg-secondary bg-opacity-15 text-dark font-monospace">{{ $bundle->sku->sku_code }}</span></td>
          <td>{{ optional($bundle->sku->color)->name ?? '-' }}</td>
          <td>{{ optional($bundle->sku->size)->name ?? '-' }}</td>
          <td class="text-center fw-semibold">{{ number_format($bundle->qty) }}</td>
          <td class="text-center"><span class="badge bg-{{ $bundle->status_color }}">{{ $bundle->status_label }}</span></td>
          <td class="text-center">
            <form method="POST" action="{{ route('cutting.bundle.status', $bundle) }}" class="d-inline">
              @csrf @method('PATCH')
              <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width:auto;display:inline-block">
                @foreach(['cut'=>'Dipotong','bundled'=>'Di-bundle','sent_to_sewing'=>'Ke Sewing'] as $val=>$lbl)
                <option value="{{ $val }}" {{ $bundle->status==$val?'selected':'' }}>{{ $lbl }}</option>
                @endforeach
              </select>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot class="table-light">
        <tr>
          <td colspan="4" class="fw-semibold text-end">Total</td>
          <td class="text-center fw-bold">{{ number_format($cutting->bundles->sum('qty')) }}</td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
    </table>
  </div>
  @else
  <div class="card-body text-center py-5 text-muted">
    <i class="bi bi-boxes display-6 d-block mb-2 opacity-25"></i>
    Belum ada bundle. Tambah bundle untuk mencatat hasil pemotongan per SKU.
  </div>
  @endif
</div>

{{-- Add Bundle Modal --}}
<div class="modal fade" id="addBundleModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('cutting.bundle.store', $cutting) }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>Tambah Bundle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">SKU <span class="text-danger">*</span></label>
            <select name="sku_id" class="form-select" required id="modalSkuSel">
              <option value="">Memuat SKU...</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Qty (pcs) <span class="text-danger">*</span></label>
            <input type="number" name="qty" class="form-control" min="1" required>
          </div>
          <div>
            <label class="form-label fw-semibold">Catatan</label>
            <input type="text" name="notes" class="form-control" placeholder="Opsional">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i>Simpan Bundle</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection
@push('scripts')
<script>
// Load SKUs for modal
const orderId = {{ $cutting->production_order_id }};
fetch(`/api/order-skus/${orderId}`).then(r => r.json()).then(data => {
  const sel = document.getElementById('modalSkuSel');
  sel.innerHTML = '<option value="">-- Pilih SKU --</option>';
  data.forEach(item => {
    if (item.sku) sel.innerHTML += `<option value="${item.sku.id}">${item.sku.sku_code}</option>`;
  });
});
</script>
@endpush
