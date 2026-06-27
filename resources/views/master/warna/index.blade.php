@extends('layouts.app')
@section('title', 'Master Warna')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Warna</h4>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        @if(auth()->user()->isAdmin())
        <x-import-button import-route="{{ route('import.warna') }}" template-route="{{ route('import.template.warna') }}" label="Warna" />
        <a href="{{ route('master.warna.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Warna</a>
        <form method="POST" action="{{ route('master.warna.clear-all') }}" onsubmit="return confirm('Hapus SEMUA data warna? Tindakan ini tidak bisa dibatalkan.')">
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
                    <th>Nama Warna</th>
                    <th>Swatch</th>
                    <th>Hex Code</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($colors as $color)
                <tr>
                    <td class="fw-semibold text-primary">{{ $color->code }}</td>
                    <td class="fw-semibold">{{ $color->name }}</td>
                    <td>
                        @if($color->hex_code)
                        <div class="rounded-circle border" style="width:28px;height:28px;background:{{ $color->hex_code }}"></div>
                        @else
                        <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td><code>{{ $color->hex_code ?? '-' }}</code></td>
                    <td><span class="badge bg-{{ $color->is_active ? 'success' : 'secondary' }}">{{ $color->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('master.warna.edit', $color) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.warna.destroy', $color) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan warna ini?')"><i class="bi bi-toggle-off"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-palette"></i><p class="fw-semibold mb-1">Belum ada warna</p><p>Tambahkan warna produk</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
