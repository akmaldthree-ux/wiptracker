@extends('layouts.app')
@section('title','Edit Bahan Baku')
@section('page-title','Edit Bahan Baku')
@section('content')
<div class="card" style="max-width:700px">
  <div class="card-header"><i class="bi bi-pencil me-2 text-primary"></i>Edit Bahan Baku</div>
  <div class="card-body">
    <form method="POST" action="{{ route('bahan-baku.update',$rawMaterial) }}">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label fw-semibold">Kode</label><input type="text" class="form-control" value="{{ $rawMaterial->code }}" disabled></div>
        <div class="col-md-6"><label class="form-label fw-semibold">Nama <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" value="{{ old('name',$rawMaterial->name) }}" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Kategori</label><select name="category" class="form-select">@foreach(['kain'=>'Kain','benang'=>'Benang','aksesoris'=>'Aksesoris','lainnya'=>'Lainnya'] as $v=>$l)<option value="{{ $v }}" {{ old('category',$rawMaterial->category)==$v?'selected':'' }}>{{ $l }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Satuan</label><input type="text" name="unit" class="form-control" value="{{ old('unit',$rawMaterial->unit) }}" required></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Warna</label><input type="text" name="color" class="form-control" value="{{ old('color',$rawMaterial->color) }}"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Stok Minimum</label><input type="number" name="min_stock" class="form-control" value="{{ old('min_stock',$rawMaterial->min_stock) }}" min="0" step="0.01"></div>
        <div class="col-md-4"><label class="form-label fw-semibold">Harga/Unit (Rp)</label><input type="number" name="unit_price" class="form-control" value="{{ old('unit_price',$rawMaterial->unit_price) }}" min="0"></div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Perbarui</button>
        <a href="{{ route('bahan-baku.show',$rawMaterial) }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
