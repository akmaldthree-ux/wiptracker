@extends('layouts.app')
@section('title','Detail Handover')
@section('page-title','Detail Handover')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $handover->handover_no }} <span class="badge bg-{{ $handover->status_color }}">{{ $handover->status_label }}</span></h4>
    <p class="text-muted mb-0">{{ $handover->fromStation ? $handover->fromStation->name : 'Production Order' }} → {{ $handover->toStation->name }} | Order: {{ $handover->order->order_no }}</p>
  </div>
  <div class="d-flex gap-2">
    @if(in_array(auth()->user()->role,['admin','supervisor']) && $handover->status === 'discrepancy')
    <form method="POST" action="{{ route('handover.approve',$handover) }}">
      @csrf
      <button type="submit" class="btn btn-success" onclick="return confirm('Setujui discrepancy ini?')"><i class="bi bi-check-circle me-2"></i>Setujui Discrepancy</button>
    </form>
    @endif
    <a href="{{ route('handover.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Handover</div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" width="45%">No. Handover</td><td class="fw-semibold">{{ $handover->handover_no }}</td></tr>
          <tr><td class="text-muted">Order</td><td><a href="{{ route('orders.show',$handover->order) }}">{{ $handover->order->order_no }}</a></td></tr>
          <tr><td class="text-muted">Dari</td><td class="fw-semibold">{{ $handover->fromStation ? $handover->fromStation->name : '📋 Production Order' }}</td></tr>
          <tr><td class="text-muted">Ke Stasiun</td><td class="fw-semibold">{{ $handover->toStation->name }}</td></tr>
          @if($handover->sewingLocation)
          <tr>
            <td class="text-muted">Tempat Sewing</td>
            <td>
              <span class="badge bg-primary bg-opacity-15 text-primary border border-primary border-opacity-25">
                <i class="bi bi-building me-1"></i>{{ $handover->sewingLocation->name }}
              </span>
              @if($handover->sewingLocation->address)
              <br><small class="text-muted">{{ $handover->sewingLocation->address }}</small>
              @endif
            </td>
          </tr>
          @endif
          <tr><td class="text-muted">Diinisiasi Oleh</td><td>{{ $handover->initiatedBy->name }}</td></tr>
          <tr><td class="text-muted">Waktu Kirim</td><td>{{ $handover->initiated_at ? $handover->initiated_at->format('d M Y H:i') : '-' }}</td></tr>
          @if($handover->confirmedBy)
          <tr><td class="text-muted">Dikonfirmasi Oleh</td><td>{{ $handover->confirmedBy->name }}</td></tr>
          <tr><td class="text-muted">Waktu Konfirmasi</td><td>{{ $handover->confirmed_at ? $handover->confirmed_at->format('d M Y H:i') : '-' }}</td></tr>
          @endif
          @if($handover->approvedBy)
          <tr><td class="text-muted">Disetujui Oleh</td><td>{{ $handover->approvedBy->name }}</td></tr>
          @endif
          <tr><td class="text-muted">Kondisi Barang</td><td>{{ $handover->condition_notes ?? '-' }}</td></tr>
          <tr><td class="text-muted">Catatan</td><td>{{ $handover->notes ?? '-' }}</td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Ringkasan Qty</div>
      <div class="card-body">
        <div class="row g-3 text-center">
          <div class="col-4">
            <div class="p-3 bg-primary bg-opacity-10 rounded">
              <div class="fs-2 fw-bold text-primary">{{ $handover->total_sent }}</div>
              <small class="text-muted">Total Dikirim</small>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 bg-success bg-opacity-10 rounded">
              <div class="fs-2 fw-bold text-success">{{ $handover->total_received ?? '-' }}</div>
              <small class="text-muted">Total Diterima</small>
            </div>
          </div>
          <div class="col-4">
            <div class="p-3 {{ $handover->total_discrepancy != 0 ? 'bg-danger' : 'bg-success' }} bg-opacity-10 rounded">
              <div class="fs-2 fw-bold {{ $handover->total_discrepancy != 0 ? 'text-danger' : 'text-success' }}">{{ $handover->total_discrepancy ?? '0' }}</div>
              <small class="text-muted">Selisih</small>
            </div>
          </div>
        </div>
        @if($handover->hasDiscrepancy())
        <div class="alert alert-danger mt-3 mb-0 py-2"><i class="bi bi-exclamation-triangle me-2"></i>Ada discrepancy pada handover ini!</div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Items -->
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-list-ul me-2"></i>Detail Item Handover</div>
  @if($handover->status === 'pending' && auth()->user()->station_id == $handover->to_station_id)
  <form method="POST" action="{{ route('handover.confirm',$handover) }}">
    @csrf
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead><tr><th>SKU</th><th>Warna</th><th>Ukuran</th><th>Qty Kirim</th><th>Qty Terima</th><th>Catatan Selisih</th></tr></thead>
        <tbody>
          @foreach($handover->items as $item)
          <tr>
            <td><small class="font-monospace">{{ $item->sku->sku_code }}</small></td>
            <td>{{ optional($item->sku->color)->name }}</td>
            <td>{{ optional($item->sku->size)->name }}</td>
            <td class="fw-semibold text-primary">{{ $item->qty_sent }}</td>
            <td style="width:140px"><input type="number" name="items[{{ $item->id }}][qty_received]" class="form-control form-control-sm" value="{{ $item->qty_sent }}" min="0" required></td>
            <td><input type="text" name="items[{{ $item->id }}][discrepancy_notes]" class="form-control form-control-sm" placeholder="Alasan jika ada selisih..."></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer"><button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-2"></i>Konfirmasi Penerimaan</button></div>
  </form>
  @else
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>SKU</th><th>Warna</th><th>Ukuran</th><th>Qty Kirim</th><th>Qty Terima</th><th>Selisih</th><th>Catatan Selisih</th></tr></thead>
      <tbody>
        @foreach($handover->items as $item)
        <tr class="{{ $item->discrepancy != 0 && $item->discrepancy !== null ? 'table-warning' : '' }}">
          <td><small class="font-monospace">{{ $item->sku->sku_code }}</small></td>
          <td>{{ optional($item->sku->color)->name }}</td>
          <td>{{ optional($item->sku->size)->name }}</td>
          <td class="fw-semibold">{{ $item->qty_sent }}</td>
          <td class="fw-semibold {{ $item->qty_received !== null ? 'text-success' : 'text-muted' }}">{{ $item->qty_received ?? '-' }}</td>
          <td class="{{ $item->discrepancy != 0 && $item->discrepancy !== null ? 'text-danger fw-bold' : 'text-muted' }}">{{ $item->discrepancy !== null ? $item->discrepancy : '-' }}</td>
          <td><small class="text-muted">{{ $item->discrepancy_notes ?? '-' }}</small></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>
@endsection
