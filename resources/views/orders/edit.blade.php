@extends('layouts.app')
@section('title', 'Edit Order Produksi')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Order Produksi</a></li><li class="breadcrumb-item active">Edit</li></ol></nav>
    <h4 class="mb-0 fw-bold">Edit Order: {{ $order->order_number }}</h4>
</div>

@if($order->status !== 'draft')
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Order ini berstatus <strong>{{ $order->getStatusLabelAttribute() }}</strong>. Hanya beberapa field yang dapat diubah.</div>
@endif

<form action="{{ route('orders.update', $order) }}" method="POST">
    @csrf @method('PUT')
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Informasi Order</h6></div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                            <select name="product_id" id="productSelect" class="form-select @error('product_id') is-invalid @enderror" required {{ $order->status !== 'draft' ? 'disabled' : '' }}>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id',$order->product_id)==$p->id?'selected':'' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @if($order->status !== 'draft')<input type="hidden" name="product_id" value="{{ $order->product_id }}">@endif
                            @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Series <span class="text-danger">*</span></label>
                            <select name="series_id" id="seriesSelect" class="form-select @error('series_id') is-invalid @enderror" required {{ $order->status !== 'draft' ? 'disabled' : '' }}>
                                <option value="{{ $order->series_id }}">{{ $order->series->name ?? '' }}</option>
                            </select>
                            @if($order->status !== 'draft')<input type="hidden" name="series_id" value="{{ $order->series_id }}">@endif
                            @error('series_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Order <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control @error('order_date') is-invalid @enderror" value="{{ old('order_date', $order->order_date) }}" required>
                            @error('order_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Target Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="target_date" class="form-control @error('target_date') is-invalid @enderror" value="{{ old('target_date', $order->target_date) }}" required>
                            @error('target_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->status === 'draft')
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Item SKU</h6></div>
                <div class="card-body p-4">
                    <div id="skuContainer">
                        @foreach($order->items as $i => $item)
                        <div class="row g-2 mb-3 sku-row" data-index="{{ $i }}">
                            <div class="col-md-7">
                                <select name="skus[{{ $i }}][sku_id]" class="form-select sku-select" required>
                                    <option value="{{ $item->sku_id }}">{{ $item->sku->getFullNameAttribute() }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="skus[{{ $i }}][target_qty]" class="form-control" value="{{ $item->target_qty }}" placeholder="Qty" required min="1">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 remove-sku"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" id="addSku" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Tambah SKU</button>
                </div>
            </div>
            @else
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Item SKU (Hanya Baca)</h6></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light"><tr><th>SKU</th><th class="text-end">Target Qty</th></tr></thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr><td>{{ $item->sku->getFullNameAttribute() }}</td><td class="text-end">{{ number_format($item->target_qty) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Status</h6></div>
                <div class="card-body p-4">
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" {{ old('status',$order->status)=='draft'?'selected':'' }}>Draft</option>
                        <option value="active" {{ old('status',$order->status)=='active'?'selected':'' }}>Aktif</option>
                        <option value="completed" {{ old('status',$order->status)=='completed'?'selected':'' }}>Selesai</option>
                        <option value="cancelled" {{ old('status',$order->status)=='cancelled'?'selected':'' }}>Dibatalkan</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

@if($order->status === 'draft')
<script>
let skuIndex = {{ $order->items->count() }};
const productSelect = document.getElementById('productSelect');
const seriesSelect = document.getElementById('seriesSelect');

productSelect?.addEventListener('change', () => {
    fetch(`/api/series-by-product/${productSelect.value}`)
        .then(r => r.json())
        .then(data => {
            seriesSelect.innerHTML = '<option value="">-- Pilih Series --</option>';
            data.forEach(s => seriesSelect.innerHTML += `<option value="${s.id}">${s.name} (${s.code})</option>`);
            document.querySelectorAll('.sku-select').forEach(s => s.innerHTML = '<option value="">-- Pilih SKU --</option>');
        });
});

seriesSelect?.addEventListener('change', () => loadSkus());

function loadSkus() {
    if (!seriesSelect.value) return;
    fetch(`/api/skus-by-series/${seriesSelect.value}`)
        .then(r => r.json())
        .then(data => {
            window._skus = data;
            document.querySelectorAll('.sku-select').forEach(s => {
                const cur = s.value;
                s.innerHTML = '<option value="">-- Pilih SKU --</option>';
                data.forEach(sk => s.innerHTML += `<option value="${sk.id}" ${sk.id==cur?'selected':''}>${sk.color?.name} - ${sk.size?.name}</option>`);
            });
        });
}

document.getElementById('addSku')?.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'row g-2 mb-3 sku-row';
    row.innerHTML = `
        <div class="col-md-7"><select name="skus[${skuIndex}][sku_id]" class="form-select sku-select" required>
            <option value="">-- Pilih SKU --</option>
            ${(window._skus||[]).map(sk=>`<option value="${sk.id}">${sk.color?.name} - ${sk.size?.name}</option>`).join('')}
        </select></div>
        <div class="col-md-4"><input type="number" name="skus[${skuIndex}][target_qty]" class="form-control" placeholder="Qty" required min="1"></div>
        <div class="col-md-1"><button type="button" class="btn btn-outline-danger w-100 remove-sku"><i class="bi bi-trash"></i></button></div>`;
    document.getElementById('skuContainer').appendChild(row);
    skuIndex++;
});

document.getElementById('skuContainer')?.addEventListener('click', e => {
    if (e.target.closest('.remove-sku')) e.target.closest('.sku-row').remove();
});
</script>
@endif
@endsection
