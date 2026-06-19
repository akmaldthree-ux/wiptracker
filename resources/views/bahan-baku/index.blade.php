@extends('layouts.app')
@section('title','Bahan Baku')
@section('page-title','Manajemen Bahan Baku')
@section('content')
@if($lowStockCount > 0)
<div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
  <i class="bi bi-exclamation-triangle-fill fs-5"></i>
  <div><strong>{{ $lowStockCount }} bahan baku</strong> di bawah stok minimum! Segera lakukan pembelian.</div>
  <a href="?low_stock=1" class="btn btn-sm btn-danger ms-auto">Lihat Sekarang</a>
</div>
@endif
<div class="d-flex justify-content-between align-items-center mb-4">
  <div><h5 class="mb-0 fw-bold">Daftar Bahan Baku</h5><p class="text-muted small mb-0">{{ $materials->total() }} bahan baku terdaftar</p></div>
  @if(in_array(auth()->user()->role,['admin','supervisor','staff_gudang']))
  <a href="{{ route('bahan-baku.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Bahan Baku</a>
  @endif
</div>
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4"><input type="text" name="search" class="form-control form-control-sm" placeholder="Cari kode / nama..." value="{{ request('search') }}"></div>
      <div class="col-md-2">
        <select name="category" class="form-select form-select-sm">
          <option value="">Semua Kategori</option>
          @foreach(['kain'=>'Kain','benang'=>'Benang','aksesoris'=>'Aksesoris','lainnya'=>'Lainnya'] as $v=>$l)
          <option value="{{ $v }}" {{ request('category')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <div class="form-check form-switch ms-2 mt-1">
          <input class="form-check-input" type="checkbox" name="low_stock" id="lowStock" value="1" {{ request('low_stock')?'checked':'' }}>
          <label class="form-check-label small" for="lowStock">Stok Kritis</label>
        </div>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search me-1"></i>Cari</button></div>
      <div class="col-md-2"><a href="{{ route('bahan-baku.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Kode</th><th>Nama</th><th>Kategori</th><th>Warna</th><th>Satuan</th><th>Stok Min.</th><th>Stok Saat Ini</th><th>Status Stok</th><th>Harga/Unit</th><th>Aksi</th></tr></thead>
      <tbody>
        @forelse($materials as $m)
        @php $lowStock = $m->isBelowMinStock(); @endphp
        <tr class="{{ $lowStock ? 'table-danger' : '' }}">
          <td><span class="font-monospace fw-semibold">{{ $m->code }}</span></td>
          <td><a href="{{ url('bahan-baku/'.$m->id) }}" class="text-decoration-none fw-semibold {{ $lowStock?'text-danger':'' }}">{{ $m->name }}</a></td>
          <td><span class="badge bg-secondary text-white">{{ $m->category_label }}</span></td>
          <td>{{ $m->color ?? '-' }}</td>
          <td>{{ $m->unit }}</td>
          <td>{{ number_format($m->min_stock) }}</td>
          <td class="fw-bold {{ $lowStock ? 'text-danger' : 'text-success' }}">{{ number_format($m->current_stock) }}</td>
          <td>
            @if($lowStock)
            <span class="badge bg-danger"><i class="bi bi-exclamation-triangle me-1"></i>Kritis</span>
            @elseif($m->current_stock < $m->min_stock * 1.2)
            <span class="badge bg-warning text-dark">Rendah</span>
            @else
            <span class="badge bg-success"><i class="bi bi-check me-1"></i>Aman</span>
            @endif
          </td>
          <td>Rp {{ number_format($m->unit_price) }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ url('bahan-baku/'.$m->id) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a>
              <a href="{{ url('bahan-baku/'.$m->id.'/receipt') }}" class="btn btn-sm btn-outline-success py-1"><i class="bi bi-box-arrow-in-down"></i></a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Tidak ada data bahan baku</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($materials->hasPages())
  <div class="card-footer py-3">{{ $materials->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
