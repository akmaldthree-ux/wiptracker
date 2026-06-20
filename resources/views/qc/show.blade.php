@extends('layouts.app')
@section('title','Detail Inspeksi QC')
@section('page-title','Detail Inspeksi QC')
@section('content')

<div class="row g-3">
  {{-- Summary Card --}}
  <div class="col-12 col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-shield-check me-2"></i>Ringkasan Inspeksi</span>
        @if($qc->status === 'pass')
          <span class="badge bg-success fs-6"><i class="bi bi-check-circle me-1"></i>PASS</span>
        @elseif($qc->status === 'fail')
          <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>FAIL</span>
        @else
          <span class="badge bg-warning text-dark fs-6"><i class="bi bi-exclamation-circle me-1"></i>CONDITIONAL</span>
        @endif
      </div>
      <div class="card-body">
        <dl class="row mb-0" style="font-size:.875rem">
          <dt class="col-5 text-muted">Order</dt>
          <dd class="col-7 fw-semibold">{{ $qc->order?->order_no }}</dd>
          <dt class="col-5 text-muted">Produk</dt>
          <dd class="col-7">{{ $qc->order?->product?->name ?? '-' }}</dd>
          <dt class="col-5 text-muted">Inspector</dt>
          <dd class="col-7">{{ $qc->inspector?->name }}</dd>
          <dt class="col-5 text-muted">Tanggal</dt>
          <dd class="col-7">{{ $qc->inspected_at?->format('d M Y H:i') }}</dd>
          <dt class="col-5 text-muted">Total Diperiksa</dt>
          <dd class="col-7 fw-bold">{{ number_format($qc->total_checked) }} pcs</dd>
          <dt class="col-5 text-muted">Total Defect</dt>
          <dd class="col-7 fw-bold text-{{ $qc->total_defect > 0 ? 'danger' : 'success' }}">{{ $qc->total_defect }} pcs</dd>
          <dt class="col-5 text-muted">Defect Rate</dt>
          <dd class="col-7">
            <span class="badge {{ $qc->defect_rate == 0 ? 'bg-success' : ($qc->defect_rate > 5 ? 'bg-danger' : 'bg-warning text-dark') }} fs-6">
              {{ $qc->defect_rate }}%
            </span>
          </dd>
          @if($qc->notes)
          <dt class="col-5 text-muted">Catatan</dt>
          <dd class="col-7">{{ $qc->notes }}</dd>
          @endif
        </dl>

        @if($qc->photo_evidence)
        <div class="mt-3">
          <p class="fw-semibold mb-2" style="font-size:.85rem">Foto Bukti:</p>
          <a href="{{ asset($qc->photo_evidence) }}" target="_blank">
            <img src="{{ asset($qc->photo_evidence) }}" alt="Foto Inspeksi"
                 style="max-width:100%;max-height:200px;border-radius:8px;border:2px solid var(--primary);object-fit:cover">
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Checklist --}}
  <div class="col-12 col-lg-7">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-list-check me-2"></i>Hasil Checklist</div>
      <div class="table-responsive">
        <table class="table mb-0" style="font-size:.875rem">
          <thead>
            <tr>
              <th>Item Pemeriksaan</th>
              <th class="text-center">Hasil</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($qc->items as $item)
            <tr>
              <td class="fw-semibold">{{ $item->checklist_item }}</td>
              <td class="text-center">
                @if($item->result === 'ok')
                  <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>OK</span>
                @elseif($item->result === 'fail')
                  <span class="badge bg-danger"><i class="bi bi-x-lg me-1"></i>FAIL</span>
                @else
                  <span class="badge bg-secondary">N/A</span>
                @endif
              </td>
              <td class="text-muted">{{ $item->notes ?: '-' }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12">
    <a href="{{ route('qc.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>
@endsection
