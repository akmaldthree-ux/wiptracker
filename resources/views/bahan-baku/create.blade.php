@extends('layouts.app')
@section('title','Tambah Bahan Baku')
@section('page-title','Tambah Bahan Baku')
@section('content')
<div class="card" style="max-width:700px">
  <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Form Bahan Baku Baru</div>
  <div class="card-body">
    <form method="POST" action="{{ route('bahan-baku.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label><input type="text" name="code" class="form-control" value="{{ old('code') }}" required></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Nama Bahan Baku <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name') }}" required></div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
          <select name="category" class="form-select" required>
            @foreach(['kain'=>'Kain','benang'=>'Benang','aksesoris'=>'Aksesoris','lainnya'=>'Lainnya'] as $v=>$l)
            <option value="{{ $v }}" {{ old('category')==$v?'selected':'' }}>{{ $l }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4"><label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label><input type="text" name="unit" class="form-control" value="{{ old('unit') }}" placeholder="meter, pcs, kg, dll" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Warna/Variasi</label><input type="text" name="color" class="form-control" value="{{ old('color') }}" placeholder="Opsional"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Stok Minimum</label><input type="number" name="min_stock" class="form-control" value="{{ old('min_stock',0) }}" min="0" step="0.01"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Stok Awal</label><input type="number" name="current_stock" class="form-control" value="{{ old('current_stock',0) }}" min="0" step="0.01"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Harga/Unit (Rp)</label><input type="number" name="unit_price" class="form-control" value="{{ old('unit_price',0) }}" min="0"></div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Simpan</button>
        <a href="{{ route('bahan-baku.index') }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
