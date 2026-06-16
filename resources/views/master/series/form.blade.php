@extends('layouts.app')
@section('title', isset($series) ? 'Edit Series' : 'Tambah Series')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('master.series.index') }}">Master Series</a></li><li class="breadcrumb-item active">{{ isset($series) && $series->exists ? 'Edit' : 'Tambah' }}</li></ol></nav>
    <h4 class="mb-0 fw-bold">{{ isset($series) ? 'Edit Series' : 'Tambah Series Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ isset($series) && $series->exists ? route('master.series.update', ['series' => ($series ?? null)?->id]) : route('master.series.store') }}" method="POST">
                    @csrf
                    @if(isset($series) && $series->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Produk --</option>
                            @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id', ($series ?? null)?->product_id ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Series <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', ($series ?? null)?->code ?? '') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Series <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', ($series ?? null)?->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', ($series ?? null)?->description ?? '') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', (($series ?? null)?->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Series Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.series.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
