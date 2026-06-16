@extends('layouts.app')
@section('title', isset($station) ? 'Edit Stasiun' : 'Tambah Stasiun')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('master.stasiun.index') }}">Master Stasiun</a></li><li class="breadcrumb-item active">{{ $station->exists ? 'Edit' : 'Tambah' }}</li></ol></nav>
    <h4 class="mb-0 fw-bold">{{ isset($station) ? 'Edit Stasiun' : 'Tambah Stasiun Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $station->exists ? route('master.stasiun.update', ['stasiun' => $station->id]) : route('master.stasiun.store') }}" method="POST">
                    @csrf
                    @if($station->exists) @method('PUT') @endif

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Stasiun <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $station->name ?? '') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $station->code ?? '') }}" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Proses <span class="text-danger">*</span></label>
                        <input type="number" name="order_sequence" class="form-control @error('order_sequence') is-invalid @enderror" value="{{ old('order_sequence', $station->order_sequence ?? '') }}" required min="1">
                        <div class="form-text">Urutan dalam alur produksi (Cutting=1, Sewing=2, dst.)</div>
                        @error('order_sequence')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $station->description ?? '') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', ($station->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Stasiun Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.stasiun.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
