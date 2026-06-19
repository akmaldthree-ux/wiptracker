@extends('layouts.app')
@section('title','WIP Detail Order')
@section('page-title','WIP Detail Order')
@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
  <div>
    <h5 class="mb-1 fw-bold">{{ $order->order_no }}</h5>
    <p class="text-muted mb-0">{{ $order->product->name }} — {{ optional($order->series)->name }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('wip.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Input WIP</a>
    <a href="{{ route('orders.show',$order) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali ke Order</a>
  </div>
</div>
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-diagram-3 me-2"></i>Pipeline Produksi — {{ $order->order_no }}</div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start py-2">
      @foreach($stations as $st)
      @php
        $stEntries = $entries->where('station_id',$st->id);
        $in = $stEntries->sum('qty_in'); $out = $stEntries->sum('qty_out'); $rej = $stEntries->sum('qty_reject');
        $wip = max(0,$in-$out-$rej); $hasData = $in > 0;
      @endphp
      <div class="pipeline-step flex-fill">
        <div class="pipeline-dot {{ $hasData ? ($wip>$st->bottleneck_threshold?'red':'green') : 'grey' }}">
          {{ $wip }}
        </div>
        <div class="fw-semibold" style="font-size:.8rem">{{ $st->name }}</div>
        @if($hasData)
        <small class="text-muted d-block">↓{{ $in }} ↑{{ $out }}</small>
        @if($rej>0)<small class="text-danger">✕{{ $rej }}</small>@endif
        @endif
      </div>
      @endforeach
    </div>
  </div>
</div>
<div class="card">
  <div class="card-header">Detail Entri WIP</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Tanggal</th><th>SKU</th><th>Stasiun</th><th>Masuk</th><th>Keluar</th><th>Reject</th><th>WIP</th><th>Catatan</th></tr></thead>
      <tbody>
        @forelse($entries->sortByDesc('input_date') as $e)
        <tr>
          <td>{{ $e->input_date->format('d M Y') }}</td>
          <td><small class="font-monospace">{{ $e->sku->sku_code }}</small></td>
          <td><span class="badge bg-primary text-white">{{ $e->station->name }}</span></td>
          <td class="text-success fw-semibold">{{ $e->qty_in }}</td>
          <td class="text-primary fw-semibold">{{ $e->qty_out }}</td>
          <td class="text-danger fw-semibold">{{ $e->qty_reject }}</td>
          <td><span class="badge bg-info text-white">{{ $e->qty_in_process }}</span></td>
          <td><small>{{ $e->notes }}</small></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data WIP</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
