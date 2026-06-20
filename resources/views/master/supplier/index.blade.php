@extends('layouts.app')
@section('title','Master Supplier')
@section('page-title','Master Supplier')
@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-truck me-2"></i>Daftar Supplier</span>
    <a href="{{ route('master.supplier.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Tambah Supplier
    </a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem">
      <thead>
        <tr>
          <th>#</th>
          <th>Kode</th>
          <th>Nama</th>
          <th>Telepon</th>
          <th>Email</th>
          <th>PIC</th>
          <th>Transaksi</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($suppliers as $s)
        <tr>
          <td class="text-muted">{{ $suppliers->firstItem() + $loop->index }}</td>
          <td><span class="font-monospace fw-semibold">{{ $s->code }}</span></td>
          <td class="fw-semibold">{{ $s->name }}</td>
          <td>{{ $s->phone ?: '-' }}</td>
          <td>{{ $s->email ?: '-' }}</td>
          <td>{{ $s->contact_person ?: '-' }}</td>
          <td><span class="badge bg-secondary bg-opacity-15 text-secondary">{{ $s->material_receipts_count }} penerimaan</span></td>
          <td>
            @if($s->is_active)
              <span class="badge bg-success">Aktif</span>
            @else
              <span class="badge bg-secondary">Nonaktif</span>
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('master.supplier.edit', $s) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
              <form method="POST" action="{{ route('master.supplier.destroy', $s) }}" onsubmit="return confirm('Hapus supplier {{ $s->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Belum ada data supplier</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($suppliers->hasPages())
  <div class="card-footer">{{ $suppliers->links() }}</div>
  @endif
</div>
@endsection
