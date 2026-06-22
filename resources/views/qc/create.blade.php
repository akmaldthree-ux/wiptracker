@extends('layouts.app')
@section('title','Inspeksi QC Baru')
@section('page-title','Inspeksi QC Baru')
@section('content')
<div class="card form-card-container mx-auto" style="max-width:800px">
    <div class="card-header"><i class="bi bi-shield-check me-2 text-primary"></i>Form Inspeksi QC</div>
    <div class="card-body">

        {{-- Info konteks dari handover --}}
        @if($handover)
        <div class="alert alert-primary d-flex align-items-start gap-3 mb-4">
            <i class="bi bi-link-45deg fs-4"></i>
            <div>
                <div class="fw-semibold">Inspeksi untuk Handover {{ $handover->handover_no }}</div>
                <small class="text-muted">{{ $handover->order->order_no }} — {{ $handover->order->product->name }} | Diterima di: {{ $handover->toStation->name }}</small>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('qc.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="handover_id" value="{{ $handover?->id }}">

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Order Produksi <span class="text-danger">*</span></label>
                    @if($handover)
                    <input type="hidden" name="production_order_id" value="{{ $handover->production_order_id }}">
                    <input type="text" class="form-control" value="{{ $handover->order->order_no }} — {{ $handover->order->product->name }}" disabled>
                    @else
                    <select name="production_order_id" class="form-select" required>
                        <option value="">-- Pilih Order --</option>
                        @foreach($orders as $order)
                        <option value="{{ $order->id }}" {{ old('production_order_id')==$order->id?'selected':'' }}>
                            {{ $order->order_no }} — {{ $order->product->name ?? '' }}
                        </option>
                        @endforeach
                    </select>
                    @endif
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Diperiksa <span class="text-danger">*</span></label>
                    <input type="number" name="total_checked" class="form-control" min="1"
                           value="{{ old('total_checked', $handover?->total_received ?? '') }}" required>
                    @if($handover)<small class="text-muted">Qty diterima: {{ $handover->total_received }}</small>@endif
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Total Defect <span class="text-danger">*</span></label>
                    <input type="number" name="total_defect" class="form-control" min="0" value="{{ old('total_defect',0) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Foto Bukti</label>
                    <input type="file" name="photo_evidence" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Checklist Pemeriksaan</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:200px">Item Pemeriksaan</th>
                            <th class="text-center" style="width:80px">OK</th>
                            <th class="text-center" style="width:80px">Fail</th>
                            <th class="text-center" style="width:80px">N/A</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checklist as $i => $item)
                        <tr>
                            <td>{{ $item }}</td>
                            <td class="text-center"><input type="radio" name="checklist[{{ $i }}][result]" value="ok" checked class="form-check-input"></td>
                            <td class="text-center"><input type="radio" name="checklist[{{ $i }}][result]" value="fail" class="form-check-input"></td>
                            <td class="text-center"><input type="radio" name="checklist[{{ $i }}][result]" value="na" class="form-check-input"></td>
                            <td><input type="text" name="checklist[{{ $i }}][notes]" class="form-control form-control-sm" placeholder="Opsional"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Simpan Inspeksi</button>
                @if($handover)
                <a href="{{ route('handover.show', $handover) }}" class="btn btn-outline-secondary">Batal</a>
                @else
                <a href="{{ route('qc.index') }}" class="btn btn-outline-secondary">Batal</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
