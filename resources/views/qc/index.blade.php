@extends('layouts.app')
@section('title','QC Inspeksi')
@section('page-title','QC Inspeksi')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold">QC Inspeksi</h4>
        <p class="text-muted mb-0 small">Checkpoint pemeriksaan kualitas produksi</p>
    </div>
    <a href="{{ route('qc.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Inspeksi Baru
    </a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Order</th>
                    <th>Inspektor</th>
                    <th>Tanggal</th>
                    <th class="text-end">Total Diperiksa</th>
                    <th class="text-end">Defect Rate</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspections as $insp)
                <tr>
                    <td>{{ $insp->id }}</td>
                    <td>{{ $insp->order->order_no ?? '-' }}<div class="small text-muted">{{ $insp->order->product->name ?? '' }}</div></td>
                    <td>{{ $insp->inspector->name ?? '-' }}</td>
                    <td>{{ $insp->inspected_at->format('d M Y H:i') }}</td>
                    <td class="text-end">{{ number_format($insp->total_checked) }}</td>
                    <td class="text-end fw-semibold {{ $insp->defect_rate > 5 ? 'text-danger' : ($insp->defect_rate > 0 ? 'text-warning' : 'text-success') }}">
                        {{ number_format($insp->defect_rate, 2) }}%
                    </td>
                    <td>
                        @if($insp->status === 'pass')
                            <span class="badge bg-success">Pass</span>
                        @elseif($insp->status === 'fail')
                            <span class="badge bg-danger">Fail</span>
                        @else
                            <span class="badge bg-warning">Conditional</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('qc.show', $insp) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Belum ada data inspeksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($inspections->hasPages())
    <div class="card-footer">{{ $inspections->links() }}</div>
    @endif
</div>
@endsection
