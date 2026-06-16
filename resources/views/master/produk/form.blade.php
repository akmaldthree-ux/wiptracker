@extends('layouts.app')
@section('title', isset($product) ? 'Edit Produk' : 'Tambah Produk')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('master.produk.index') }}">Master Produk</a></li><li class="breadcrumb-item active">{{ isset($product) ? 'Edit' : 'Tambah' }}</li></ol></nav>
    <h4 class="mb-0 fw-bold">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($product) ? route('master.produk.update', $product) : route('master.produk.store') }}" method="POST">
                    @csrf
                    @if(isset($product)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Produk <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $product->code ?? '') }}" required placeholder="Contoh: PRD-001">
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', ($product->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Produk Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.produk.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
