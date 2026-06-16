@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
<div class="mb-4">
    <h4 class="mb-0 fw-bold">Profil Saya</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3" style="width:80px;height:80px;background:var(--primary);font-size:2rem">
                {{ strtoupper(substr(auth()->user()->name,0,1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
            <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
            <span class="badge bg-primary">{{ auth()->user()->getRoleLabelAttribute() }}</span>
            @if(auth()->user()->station)
            <p class="text-muted small mt-2 mb-0"><i class="bi bi-geo-alt me-1"></i>{{ auth()->user()->station->name }}</p>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Edit Profil</h6></div>
            <div class="card-body p-4">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->user()->phone) }}">
                        </div>
                        <div class="col-12"><hr class="my-1"><p class="small text-muted mb-0">Kosongkan jika tidak ingin mengubah password.</p></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" minlength="8">
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
