@extends('layouts.app')
@section('title', 'Laporan Bahan Baku')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li><li class="breadcrumb-item active">Bahan Baku</li></ol></nav>
        <h4 class="mb-0 fw-bold">Laporan Bahan Baku</h4>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Cetak</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-primary">{{ $materials->count() }}</div>
            <div class="small text-muted">Total Item</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-danger">{{ $materials->filter(fn($m)=>$m->isBelowMinStock())->count() }}</div>
            <div class="small text-muted">Stok Kritis</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ number_format($materials->sum('current_stock')) }}</div>
            <div class="small text-muted">Total Stok</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-warning">Rp {{ number_format($materials->sum(fn($m)=>$m->current_stock * $m->unit_price),0,',','.') }}</div>
            <div class="small text-muted">Nilai Total Stok</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Status Stok Bahan Baku</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama Bahan</th>
                    <th>Kategori</th>
                    <th class="text-end">Stok Saat Ini</th>
                    <th class="text-end">Stok Minimum</th>
                    <th>Status Stok</th>
                    <th class="text-end">Harga Satuan</th>
                    <th class="text-end">Nilai Stok</th>
                </tr>
            </thead>
            <tbody>
                @forelse($materials as $m)
                @php $critical = $m->isBelowMinStock() @endphp
                <tr class="{{ $critical ? 'table-danger' : '' }}">
                    <td class="fw-semibold">{{ $m->code }}</td>
                    <td>
                        <a href="{{ route('bahan-baku.show', $m) }}" class="text-decoration-none">{{ $m->name }}</a>
                        <div class="small text-muted">{{ $m->supplier ?? '-' }}</div>
                    </td>
                    <td><span class="badge bg-secondary">{{ $m->getCategoryLabelAttribute() }}</span></td>
                    <td class="text-end fw-semibold {{ $critical ? 'text-danger' : '' }}">{{ number_format($m->current_stock, 2) }} {{ $m->unit }}</td>
                    <td class="text-end">{{ number_format($m->min_stock, 2) }} {{ $m->unit }}</td>
                    <td>
                        @if($critical)
                        <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>Kritis</span>
                        @else
                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Aman</span>
                        @endif
                    </td>
                    <td class="text-end">Rp {{ number_format($m->unit_price, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($m->current_stock * $m->unit_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
