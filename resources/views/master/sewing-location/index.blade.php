@extends('layouts.app')
@section('title', 'Master Tempat Sewing')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-building me-2 text-primary"></i>Master Tempat Sewing</h4>
        <p class="text-muted mb-0">Kelola data lokasi / tempat sewing yang tersedia</p>
    </div>
    @if(in_array(auth()->user()->role, ['admin', 'supervisor']))
    <div class="d-flex gap-2 align-items-center">
        <x-import-button import-route="{{ route('import.tempat-sewing') }}" template-route="{{ route('import.template.tempat-sewing') }}" label="Tempat Sewing" />
        <a href="{{ route('master.sewing-location.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah Tempat Sewing
        </a>
    </div>
    @endif
</div>

<x-import-result />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<x-search-bar placeholder="Cari kode atau nama tempat sewing..." />
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Tempat Sewing</th>
                    <th>Alamat</th>
                    <th class="text-center">Kapasitas/Hari</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $loc)
                <tr>
                    <td><span class="badge bg-secondary font-monospace">{{ $loc->code }}</span></td>
                    <td class="fw-semibold">{{ $loc->name }}</td>
                    <td class="text-muted small">{{ $loc->address ?? '-' }}</td>
                    <td class="text-center">
                        @if($loc->capacity)
                        <span class="badge bg-info bg-opacity-15 text-info">{{ number_format($loc->capacity) }} pcs</span>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($loc->is_active)
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-secondary">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(in_array(auth()->user()->role, ['admin', 'supervisor']))
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('master.sewing-location.edit', $loc) }}" class="btn btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('master.sewing-location.destroy', $loc) }}" onsubmit="return confirm('Hapus tempat sewing ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-building display-6 d-block mb-2 opacity-25"></i>
                        Belum ada data tempat sewing
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locations->hasPages())
    <div class="card-footer">{{ $locations->links() }}</div>
    @endif
</div>
@endsection
