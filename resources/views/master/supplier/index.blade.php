@extends('layouts.app')
@section('title','Master Supplier')
@section('page-title','Master Supplier')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Daftar Supplier</h4>
        <p class="text-muted mb-0 small">Kelola data supplier bahan baku</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <x-import-button import-route="{{ route('import.supplier') }}" template-route="{{ route('import.template.supplier') }}" label="Supplier" />
        <a href="{{ route('master.supplier.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Supplier
        </a>
    </div>
</div>

<x-import-result />
<x-search-bar placeholder="Cari kode atau nama supplier..." />
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Contact Person</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $s)
                <tr>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td><code>{{ $s->code }}</code></td>
                    <td>{{ $s->phone ?? '-' }}</td>
                    <td>{{ $s->email ?? '-' }}</td>
                    <td>{{ $s->contact_person ?? '-' }}</td>
                    <td>
                        @if($s->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('master.supplier.edit', $s) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('master.supplier.destroy', $s) }}"
                                  onsubmit="return confirm('Yakin hapus supplier ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-building"></i><p class="fw-semibold mb-1">Belum ada supplier</p><p>Tambahkan supplier pertama</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($suppliers->hasPages())
    <div class="card-footer">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
