@extends('layouts.app')
@section('title', 'Master Stasiun')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Master Stasiun Produksi</h4>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('master.stasiun.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah Stasiun</a>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Urutan</th>
                    <th>Kode</th>
                    <th>Nama Stasiun</th>
                    <th>Deskripsi</th>
                    <th>WIP Aktif</th>
                    <th>Status</th>
                    @if(auth()->user()->isAdmin())<th class="text-end">Aksi</th>@endif
                </tr>
            </thead>
            <tbody>
                @forelse($stations as $station)
                <tr>
                    <td>
                        <span class="badge rounded-pill" style="background:var(--primary);font-size:.85rem;min-width:28px">{{ $station->order_sequence }}</span>
                    </td>
                    <td class="fw-semibold text-primary">{{ $station->code }}</td>
                    <td class="fw-semibold">{{ $station->name }}</td>
                    <td class="text-muted">{{ Str::limit($station->description, 50) ?? '-' }}</td>
                    <td>
                        @php $wip = $station->getCurrentWipCount() @endphp
                        <span class="badge {{ $wip > 0 ? 'bg-info' : 'bg-secondary' }}">{{ $wip }} pcs</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $station->is_active ? 'success' : 'secondary' }}">{{ $station->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        @if($station->is_final)<span class="badge bg-primary ms-1"><i class="bi bi-flag-fill me-1"></i>Akhir</span>@endif
                    </td>
                    @if(auth()->user()->isAdmin())
                    <td class="text-end">
                        <a href="{{ route('master.stasiun.edit', $station) }}" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('master.stasiun.destroy', $station) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan stasiun ini?')"><i class="bi bi-toggle-off"></i></button>
                        </form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="7"><div class="empty-state"><i class="bi bi-geo-alt"></i><p class="fw-semibold mb-1">Belum ada stasiun</p><p>Tambahkan stasiun produksi</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
