@extends('layouts.app')
@section('title','QC Inspection')
@section('page-title','QC / Inspeksi Kualitas')
@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-shield-check me-2"></i>Riwayat Inspeksi QC</span>
    <a href="{{ route('qc.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Buat Inspeksi</a>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem">
      <thead>
        <tr>
          <th>No</th>
          <th>Order</th>
          <th>Produk</th>
          <th>Inspector</th>
          <th>Tanggal</th>
          <th class="text-center">Total Cek</th>
          <th class="text-center">Defect</th>
          <th class="text-center">Defect Rate</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($inspections as $insp)
        <tr>
          <td class="text-muted">{{ $inspections->firstItem() + $loop->index }}</td>
          <td><span class="fw-semibold font-monospace" style="font-size:.8rem">{{ $insp->order?->order_no }}</span></td>
          <td>{{ $insp->order?->product?->name ?? '-' }}</td>
          <td>{{ $insp->inspector?->name ?? '-' }}</td>
          <td>{{ $insp->inspected_at?->format('d M Y H:i') }}</td>
          <td class="text-center">{{ number_format($insp->total_checked) }}</td>
          <td class="text-center {{ $insp->total_defect > 0 ? 'text-danger fw-bold' : '' }}">{{ $insp->total_defect }}</td>
          <td class="text-center">
            <span class="badge {{ $insp->defect_rate == 0 ? 'bg-success' : ($insp->defect_rate > 5 ? 'bg-danger' : 'bg-warning text-dark') }}">
              {{ $insp->defect_rate }}%
            </span>
          </td>
          <td>
            @if($insp->status === 'pass')
              <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>PASS</span>
            @elseif($insp->status === 'fail')
              <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>FAIL</span>
            @else
              <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>CONDITIONAL</span>
            @endif
          </td>
          <td>
            <a href="{{ route('qc.show', $insp) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Belum ada data inspeksi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($inspections->hasPages())
  <div class="card-footer">{{ $inspections->links() }}</div>
  @endif
</div>
@endsection
