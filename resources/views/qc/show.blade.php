@extends('layouts.app')
@section('title','Detail Inspeksi QC')
@section('page-title','Detail Inspeksi QC')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('qc.index') }}">QC Inspeksi</a></li>
            <li class="breadcrumb-item active">Detail #{{ $qc->id }}</li>
        </ol></nav>
        <h4 class="mb-0 fw-bold">Inspeksi #{{ $qc->id }}</h4>
    </div>
    <a href="{{ route('qc.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Inspeksi</div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr><th class="text-muted fw-normal" style="width:40%">No. Order</th><td class="fw-semibold">{{ $qc->order->order_no ?? '-' }}</td></tr>
                    <tr><th class="text-muted fw-normal">Produk</th><td>{{ $qc->order->product->name ?? '-' }}</td></tr>
                    <tr><th class="text-muted fw-normal">Inspektor</th><td>{{ $qc->inspector->name ?? '-' }}</td></tr>
                    <tr><th class="text-muted fw-normal">Tanggal</th><td>{{ $qc->inspected_at->format('d M Y H:i') }}</td></tr>
                    <tr><th class="text-muted fw-normal">Total Diperiksa</th><td>{{ number_format($qc->total_checked) }} pcs</td></tr>
                    <tr><th class="text-muted fw-normal">Total Defect</th><td>{{ number_format($qc->total_defect) }} pcs</td></tr>
                    <tr>
                        <th class="text-muted fw-normal">Defect Rate</th>
                        <td class="fw-bold {{ $qc->defect_rate > 5 ? 'text-danger' : ($qc->defect_rate > 0 ? 'text-warning' : 'text-success') }}">
                            {{ number_format($qc->defect_rate, 2) }}%
                        </td>
                    </tr>
                    <tr>
                        <th class="text-muted fw-normal">Status</th>
                        <td>
                            @if($qc->status === 'pass')
                                <span class="badge bg-success fs-6">Pass</span>
                            @elseif($qc->status === 'fail')
                                <span class="badge bg-danger fs-6">Fail</span>
                            @else
                                <span class="badge bg-warning fs-6">Conditional</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @if($qc->notes)
                <div class="mt-2 p-2 bg-light rounded small">{{ $qc->notes }}</div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-7">
        @if($qc->photo_evidence)
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-image me-2"></i>Foto Bukti</div>
            <div class="card-body text-center">
                <img src="{{ asset($qc->photo_evidence) }}" alt="Foto Bukti" class="img-fluid rounded" style="max-height:300px">
            </div>
        </div>
        @endif
        <div class="card">
            <div class="card-header"><i class="bi bi-list-check me-2"></i>Hasil Checklist</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-center">Hasil</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qc->checklistItems as $item)
                        <tr>
                            <td>{{ $item->checklist_item }}</td>
                            <td class="text-center">
                                @if($item->result === 'ok')
                                    <span class="badge bg-success">OK</span>
                                @elseif($item->result === 'fail')
                                    <span class="badge bg-danger">Fail</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
