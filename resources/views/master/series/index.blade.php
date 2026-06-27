@extends('layouts.app')
@section('title', 'Master Series')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Series</h4>
    @if(auth()->user()->isAdmin())
    <div class="d-flex gap-2 align-items-center">
        <x-import-button import-route="{{ route('import.series') }}" template-route="{{ route('import.template.series') }}" label="Series" />
        <a href="{{ route('master.series.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Series</a>
    </div>
    @endif
</div>

<x-import-result />

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Series</th>
                    <th>Produk</th>
                    <th>Jumlah SKU</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($series as $s)
                <tr>
                    <td class="fw-semibold text-primary">{{ $s->code }}</td>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td>{{ $s->product->name ?? '-' }}</td>
                    <td><span class="badge bg-info">{{ $s->skus->count() }} SKU</span></td>
                    <td>
                        <span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('master.series.edit', $s) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.series.destroy', $s) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan series ini?')"><i class="bi bi-toggle-off"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-collection"></i><p class="fw-semibold mb-1">Belum ada series</p><p>Tambahkan series pertama</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
