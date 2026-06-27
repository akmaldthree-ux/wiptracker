@extends('layouts.app')
@section('title', 'Master Produk')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Produk</h4>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        @if(auth()->user()->isAdmin())
        <x-import-button import-route="{{ route('import.produk') }}" template-route="{{ route('import.template.produk') }}" label="Produk" />
        <a href="{{ route('master.produk.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Produk</a>
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
                    <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Jumlah Series</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td class="fw-semibold text-primary">{{ $product->code }}</td>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td class="text-muted">{{ Str::limit($product->description, 50) ?? '-' }}</td>
                    <td><span class="badge bg-info">{{ $product->series->count() }} series</span></td>
                    <td>
                        @if($product->is_active)
                        <span class="badge bg-success">Aktif</span>
                        @else
                        <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('master.produk.edit', $product) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.produk.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus produk {{ $product->name }}?')"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="6"><div class="empty-state"><i class="bi bi-tag"></i><p class="fw-semibold mb-1">Belum ada produk</p><p>Tambahkan produk pertama</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
