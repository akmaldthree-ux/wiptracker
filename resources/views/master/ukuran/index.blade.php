@extends('layouts.app')
@section('title', 'Master Ukuran')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Ukuran</h4>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        @if(auth()->user()->isAdmin())
        <x-import-button import-route="{{ route('import.ukuran') }}" template-route="{{ route('import.template.ukuran') }}" label="Ukuran" />
        <a href="{{ route('master.ukuran.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Ukuran</a>
        <form method="POST" action="{{ route('master.ukuran.clear-all') }}" onsubmit="return confirm('Hapus SEMUA data ukuran? Tindakan ini tidak bisa dibatalkan.')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash me-1"></i>Hapus Semua</button>
        </form>
        @endif
    </div>
</div>

<x-import-result />
@if(session('success') && !session('import_errors'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Ukuran</th>
                    <th>Urutan</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($sizes as $size)
                <tr>
                    <td class="fw-semibold text-primary">{{ $size->code }}</td>
                    <td class="fw-semibold">{{ $size->name }}</td>
                    <td>{{ $size->sort_order ?? '-' }}</td>
                    <td><span class="badge bg-{{ $size->is_active ? 'success' : 'secondary' }}">{{ $size->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('master.ukuran.edit', $size) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.ukuran.destroy', $size) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan ukuran ini?')"><i class="bi bi-toggle-off"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="bi bi-rulers"></i><p class="fw-semibold mb-1">Belum ada ukuran</p><p>Tambahkan ukuran produk</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
