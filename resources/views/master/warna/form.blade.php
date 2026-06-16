@extends('layouts.app')
@section('title', isset($color) ? 'Edit Warna' : 'Tambah Warna')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('master.warna.index') }}">Master Warna</a></li><li class="breadcrumb-item active">{{ $color->exists ? 'Edit' : 'Tambah' }}</li></ol></nav>
    <h4 class="mb-0 fw-bold">{{ isset($color) ? 'Edit Warna' : 'Tambah Warna Baru' }}</h4>
</div>

<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ $color->exists ? route('master.warna.update', ['warna' => $color->id]) : route('master.warna.store') }}" method="POST">
                    @csrf
                    @if($color->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode Warna <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $color->code ?? '') }}" required>
                        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Warna <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $color->name ?? '') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hex Code</label>
                        <div class="input-group">
                            <input type="color" name="hex_code" id="hexPicker" class="form-control form-control-color" value="{{ old('hex_code', $color->hex_code ?? '#000000') }}">
                            <input type="text" id="hexText" class="form-control" value="{{ old('hex_code', $color->hex_code ?? '') }}" placeholder="#RRGGBB">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', ($color->is_active ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_active">Warna Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        <a href="{{ route('master.warna.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const picker = document.getElementById('hexPicker');
const text = document.getElementById('hexText');
picker.addEventListener('input', () => text.value = picker.value);
text.addEventListener('input', () => { if(/^#[0-9A-Fa-f]{6}$/.test(text.value)) picker.value = text.value; });
</script>
@endsection
