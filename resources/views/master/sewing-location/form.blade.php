@extends('layouts.app')
@section('title', $location->exists ? 'Edit Tempat Sewing' : 'Tambah Tempat Sewing')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('master.sewing-location.index') }}">Master Tempat Sewing</a></li>
            <li class="breadcrumb-item active">{{ $location->exists ? 'Edit' : 'Tambah' }}</li>
        </ol>
    </nav>
    <h4 class="mb-0 fw-bold">{{ $location->exists ? 'Edit Tempat Sewing' : 'Tambah Tempat Sewing Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $location->exists ? route('master.sewing-location.update', $location) : route('master.sewing-location.store') }}" method="POST">
                    @csrf
                    @if($location->exists) @method('PUT') @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror"
                                value="{{ old('code', $location->code ?? '') }}" required maxlength="20"
                                style="text-transform:uppercase" placeholder="SW-01">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Tempat Sewing <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $location->name ?? '') }}" required placeholder="Contoh: Sewing A - Lantai 1">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat / Lokasi</label>
                        <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                            value="{{ old('address', $location->address ?? '') }}" placeholder="Contoh: Gedung B, Lantai 2, Jl. Industri No. 5">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kapasitas Produksi / Hari <small class="text-muted">(pcs)</small></label>
                        <div class="input-group">
                            <input type="number" name="capacity" class="form-control @error('capacity') is-invalid @enderror"
                                value="{{ old('capacity', $location->capacity ?? '') }}" min="1" placeholder="Contoh: 500">
                            <span class="input-group-text text-muted">pcs/hari</span>
                        </div>
                        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Opsional. Digunakan sebagai referensi kapasitas tempat sewing.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                            placeholder="Keterangan tambahan tentang tempat sewing ini...">{{ old('description', $location->description ?? '') }}</textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                {{ old('is_active', ($location->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Tempat Sewing Aktif</label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.sewing-location.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
