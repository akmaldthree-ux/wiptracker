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
<div class="card mb-4">
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

{{-- Handover History --}}
<div class="card">
  <div class="card-header d-flex align-items-center gap-2">
    <i class="bi bi-arrow-left-right"></i>
    <span>History Handover & Foto Inspeksi</span>
    <span class="badge bg-secondary ms-auto">{{ $handovers->count() }} handover</span>
  </div>
  @forelse($handovers as $ho)
  <div class="border-bottom px-3 py-3">
    {{-- Header baris handover --}}
    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
      <a href="{{ route('handover.show', $ho) }}" class="fw-bold text-decoration-none">{{ $ho->handover_no }}</a>
      <span class="badge bg-{{ $ho->status_color }}">{{ $ho->status_label }}</span>
      <span class="text-muted" style="font-size:.8rem">
        <i class="bi bi-arrow-right me-1"></i>
        {{ $ho->fromStation?->name ?? 'Order Produksi' }} → {{ $ho->toStation->name }}
      </span>
      <span class="ms-auto text-muted" style="font-size:.78rem">{{ $ho->initiated_at?->format('d M Y H:i') }}</span>
    </div>

    {{-- Foto --}}
    <div class="d-flex gap-2 flex-wrap align-items-start">
      {{-- Foto kirim --}}
      <div class="text-center">
        <div class="text-muted mb-1" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px">
          <i class="bi bi-send me-1"></i>Kirim
        </div>
        @if($ho->photo_sent)
        <a href="{{ asset($ho->photo_sent) }}" target="_blank">
          <img src="{{ asset($ho->photo_sent) }}" alt="Foto Kirim"
               style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:2px solid #e2e8f0;cursor:zoom-in"
               loading="lazy">
        </a>
        @else
        <div style="width:72px;height:72px;border-radius:8px;border:2px dashed #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1">
          <i class="bi bi-image"></i>
        </div>
        @endif
      </div>

      {{-- Foto terima --}}
      <div class="text-center">
        <div class="text-muted mb-1" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px">
          <i class="bi bi-box-arrow-in-down me-1"></i>Terima
        </div>
        @if($ho->photo_received)
        <a href="{{ asset($ho->photo_received) }}" target="_blank">
          <img src="{{ asset($ho->photo_received) }}" alt="Foto Terima"
               style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:2px solid #bbf7d0;cursor:zoom-in"
               loading="lazy">
        </a>
        @else
        <div style="width:72px;height:72px;border-radius:8px;border:2px dashed #e2e8f0;display:flex;align-items:center;justify-content:center;color:#cbd5e1">
          <i class="bi bi-image"></i>
        </div>
        @endif
      </div>

      {{-- Foto reject per item (jika ada) --}}
      @foreach($ho->items->where('photo_reject', '!=', null) as $item)
      <div class="text-center">
        <div class="text-muted mb-1" style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px">
          <i class="bi bi-exclamation-triangle me-1 text-danger"></i>Reject
        </div>
        <a href="{{ asset($item->photo_reject) }}" target="_blank" title="{{ $item->sku->sku_code }} — {{ $item->qty_reject }} pcs reject">
          <img src="{{ asset($item->photo_reject) }}" alt="Foto Reject {{ $item->sku->sku_code }}"
               style="width:72px;height:72px;object-fit:cover;border-radius:8px;border:2px solid #fca5a5;cursor:zoom-in"
               loading="lazy">
        </a>
        <div style="font-size:.65rem;color:#ef4444;margin-top:2px">{{ $item->sku->sku_code }}</div>
      </div>
      @endforeach

      {{-- Info qty --}}
      <div class="ms-auto d-flex gap-3 align-items-center">
        <div class="text-center">
          <div class="fw-bold text-primary" style="font-size:1rem">{{ $ho->total_sent }}</div>
          <div class="text-muted" style="font-size:.7rem">Kirim</div>
        </div>
        @if($ho->total_received)
        <div class="text-center">
          <div class="fw-bold text-success" style="font-size:1rem">{{ $ho->total_received }}</div>
          <div class="text-muted" style="font-size:.7rem">Terima</div>
        </div>
        @endif
        @if($ho->initiatedBy)
        <div class="text-center">
          <div style="font-size:.78rem;font-weight:600">{{ $ho->initiatedBy->name }}</div>
          <div class="text-muted" style="font-size:.7rem">PIC Kirim</div>
        </div>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="text-center text-muted py-4"><i class="bi bi-inbox me-2"></i>Belum ada handover untuk order ini</div>
  @endforelse
</div>
@endsection
