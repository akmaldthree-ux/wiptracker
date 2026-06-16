@extends('layouts.app')
@section('title', 'Detail Pengguna')
@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li><li class="breadcrumb-item active">{{ $user->name }}</li></ol></nav>
    <div class="d-flex justify-content-between align-items-center">
        <h4 class="mb-0 fw-bold">Detail Pengguna</h4>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Edit</a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white mx-auto mb-3" style="width:80px;height:80px;background:var(--primary);font-size:2rem">
                {{ strtoupper(substr($user->name,0,1)) }}
            </div>
            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
            <p class="text-muted mb-2">{{ $user->email }}</p>
            @php
                $roleColors = ['admin'=>'danger','supervisor'=>'primary','pic_stasiun'=>'info','manager'=>'purple','staff_gudang'=>'success'];
                $rc = $roleColors[$user->role] ?? 'secondary';
            @endphp
            <span class="badge bg-{{ $rc }} mb-3">{{ $user->getRoleLabelAttribute() }}</span>
            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Informasi Akun</h6></div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Nama Lengkap</dt>
                    <dd class="col-sm-8 fw-semibold">{{ $user->name }}</dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8">{{ $user->email }}</dd>
                    <dt class="col-sm-4 text-muted">Role</dt>
                    <dd class="col-sm-8">{{ $user->getRoleLabelAttribute() }}</dd>
                    <dt class="col-sm-4 text-muted">Stasiun</dt>
                    <dd class="col-sm-8">{{ $user->station->name ?? '-' }}</dd>
                    <dt class="col-sm-4 text-muted">No. Telepon</dt>
                    <dd class="col-sm-8">{{ $user->phone ?? '-' }}</dd>
                    <dt class="col-sm-4 text-muted">Bergabung</dt>
                    <dd class="col-sm-8">{{ $user->created_at->format('d M Y') }}</dd>
                    <dt class="col-sm-4 text-muted">WIP Entries</dt>
                    <dd class="col-sm-8">{{ $user->wipEntries->count() }} entri</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
