@extends('layouts.app')
@section('title','Edit Budget')
@section('page-title','Edit Budget Produksi')
@section('content')
<div class="card form-card-container mx-auto" style="max-width:640px">
  <div class="card-header"><i class="bi bi-wallet2 me-2 text-primary"></i>Budget Plan — {{ $order->order_no }}</div>
  <div class="card-body">

    {{-- BOM Estimate Panel --}}
    @if($bomItems->count() > 0)
    <div class="alert alert-primary border-primary mb-4 p-0 overflow-hidden">
      <div class="px-3 py-2 bg-primary bg-opacity-10 d-flex align-items-center justify-content-between">
        <span class="fw-semibold"><i class="bi bi-calculator me-2"></i>Estimasi dari Bill of Materials</span>
        <span class="badge bg-primary">{{ $totalQty }} unit</span>
      </div>
      <div class="px-3 py-2">
        <table class="table table-sm mb-2" style="font-size:.82rem">
          <thead><tr><th>Bahan Baku</th><th class="text-center">Qty Dibutuhkan</th><th class="text-end">Harga/Unit</th><th class="text-end">Estimasi Biaya</th></tr></thead>
          <tbody>
            @foreach($bomItems as $b)
            @php $qtyNeeded = round($b->getQtyNeeded($totalQty), 2); $cost = $qtyNeeded * $b->rawMaterial->unit_price; @endphp
            <tr>
              <td>{{ $b->rawMaterial->name }} <small class="text-muted">({{ $b->rawMaterial->unit }})</small></td>
              <td class="text-center">{{ number_format($qtyNeeded, 2) }}</td>
              <td class="text-end">Rp {{ number_format($b->rawMaterial->unit_price) }}</td>
              <td class="text-end fw-semibold">Rp {{ number_format($cost) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="table-primary">
              <td colspan="3" class="fw-bold">Total Estimasi Bahan Baku</td>
              <td class="text-end fw-bold">Rp {{ number_format($bomEstimate) }}</td>
            </tr>
          </tfoot>
        </table>
        <button type="button" class="btn btn-sm btn-primary w-100" onclick="applyBomEstimate()">
          <i class="bi bi-arrow-down-circle me-1"></i>Gunakan Estimasi Ini untuk Biaya Bahan Baku
        </button>
      </div>
    </div>
    @else
    <div class="alert alert-warning py-2 mb-4"><i class="bi bi-exclamation-triangle me-1"></i>
      <small>BOM untuk produk <strong>{{ $order->product->name }}</strong> belum didefinisikan. <a href="{{ route('bom.index') }}">Tambah BOM</a> untuk mendapatkan estimasi otomatis.</small>
    </div>
    @endif

    <form method="POST" action="{{ route('budget.update',$order) }}">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-12">
          <label class="form-label fw-semibold">Biaya Bahan Baku (Rp)</label>
          <input type="number" name="material_cost_plan" id="material_cost_plan" class="form-control" value="{{ old('material_cost_plan',optional($budget)->material_cost_plan??0) }}" min="0" required>
          @if($bomEstimate > 0)
          <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Estimasi BOM: <strong>Rp {{ number_format($bomEstimate) }}</strong></small>
          @endif
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Biaya Proses (Rp)</label>
          <input type="number" name="process_cost_plan" class="form-control" value="{{ old('process_cost_plan',optional($budget)->process_cost_plan??0) }}" min="0" required>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Biaya Overhead (Rp)</label>
          <input type="number" name="overhead_cost_plan" class="form-control" value="{{ old('overhead_cost_plan',optional($budget)->overhead_cost_plan??0) }}" min="0" required>
        </div>
        <div class="col-12">
          <div class="alert alert-info py-2 mb-0">
            <small><i class="bi bi-info-circle me-1"></i>Total budget akan dihitung otomatis dari ketiga komponen di atas.</small>
          </div>
        </div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Simpan Budget</button>
        <a href="{{ route('budget.show',$order) }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
@push('scripts')
<script>
function applyBomEstimate() {
  document.getElementById('material_cost_plan').value = {{ round($bomEstimate) }};
  document.getElementById('material_cost_plan').dispatchEvent(new Event('input'));
}
</script>
@endpush
