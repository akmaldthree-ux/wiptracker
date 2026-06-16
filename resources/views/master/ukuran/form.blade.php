@extends('layouts.app')
@section('title', isset($size) ? 'Edit Ukuran' : 'Tambah Ukuran')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('master.ukuran.index') }}">Master Ukuran</a></li><li class="breadcrumb-item active">{{ $size->exists ? 'Edit' : 'Tambah' }}</li></ol></nav>
    <h4 class="mb-0 fw-bold">{{ isset($size) ? 'Edit Ukuran' : 'Tambah Ukuran Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $size->exists ? route('master.ukuran.update', ['ukuran' => $size->id]) : route('master.ukuran.store') }}" method="POST">
                    @csrf
                    @if($size->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Ukuran <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $size->code ?? '') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ukuran <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $size->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $size->sort_order ?? '') }}" min="1">
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', ($size->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Ukuran Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.ukuran.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
