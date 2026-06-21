@extends('layouts.app')
@section('title','Edit Budget')
@section('page-title','Edit Budget Produksi')
@section('content')
<div class="card form-card-container mx-auto" style="max-width:600px">
  <div class="card-header"><i class="bi bi-wallet2 me-2 text-primary"></i>Budget Plan — {{ $order->order_no }}</div>
  <div class="card-body">
    <form method="POST" action="{{ route('budget.update',$order) }}">
      @csrf @method('PUT')
      <div class="row g-3">
        <div class="col-12"><label class="form-label fw-semibold">Biaya Bahan Baku (Rp)</label><input type="number" name="material_cost_plan" class="form-control" value="{{ old('material_cost_plan',optional($budget)->material_cost_plan??0) }}" min="0" required></div>
        <div class="col-12"><label class="form-label fw-semibold">Biaya Proses (Rp)</label><input type="number" name="process_cost_plan" class="form-control" value="{{ old('process_cost_plan',optional($budget)->process_cost_plan??0) }}" min="0" required></div>
        <div class="col-12"><label class="form-label fw-semibold">Biaya Overhead (Rp)</label><input type="number" name="overhead_cost_plan" class="form-control" value="{{ old('overhead_cost_plan',optional($budget)->overhead_cost_plan??0) }}" min="0" required></div>
        <div class="col-12"><div class="alert alert-info py-2 mb-0"><small><i class="bi bi-info-circle me-1"></i>Total budget akan dihitung otomatis dari ketiga komponen di atas.</small></div></div>
      </div>
      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-2"></i>Simpan Budget</button>
        <a href="{{ route('budget.show',$order) }}" class="btn btn-outline-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection
