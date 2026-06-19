@extends('layouts.app')
@section('title','Detail Order')
@section('page-title','Detail Order Produksi')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $order->order_no }} <span class="badge badge-{{ $order->status }} fs-6">{{ $order->status_label }}</span></h4>
    <p class="text-muted mb-0">{{ $order->product->name }} — {{ optional($order->series)->name }}</p>
  </div>
  <div class="d-flex gap-2">
    @if(in_array(auth()->user()->role,['admin','supervisor']))
    <div class="dropdown">
      <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Ubah Status</button>
      <ul class="dropdown-menu">
        @foreach(['draft'=>'Draft','active'=>'Aktif','completed'=>'Selesai','on_hold'=>'Ditahan','cancelled'=>'Dibatalkan'] as $v=>$l)
        @if($v !== $order->status)
        <li>
          <form method="POST" action="{{ route('orders.status',$order) }}">
            @csrf @method('PATCH')
            <input type="hidden" name="status" value="{{ $v }}">
            <button type="submit" class="dropdown-item">{{ $l }}</button>
          </form>
        </li>
        @endif
        @endforeach
      </ul>
    </div>
    @endif
    <a href="{{ route('wip.show',$order) }}" class="btn btn-outline-primary"><i class="bi bi-activity me-1"></i>Lihat WIP</a>
    @if(in_array(auth()->user()->role,['admin','supervisor']) && $order->status === 'active')
    <form method="POST" action="{{ route('orders.send-to-cutting',$order) }}" class="d-inline" onsubmit="return confirm('Kirim semua item order ini ke stasiun Cutting?')">
      @csrf
      <button type="submit" class="btn btn-warning"><i class="bi bi-scissors me-1"></i>Kirim ke Cutting</button>
    </form>
    @endif
    <a href="{{ route('handover.create') }}?order_id={{ $order->id }}" class="btn btn-primary"><i class="bi bi-arrow-left-right me-1"></i>Buat Handover</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fs-2 fw-bold text-primary">{{ number_format($order->getTotalTargetQty()) }}</div>
        <small class="text-muted">Total Target Qty</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        @php $progress = $order->getProgressPercentage(); @endphp
        <div class="fs-2 fw-bold text-{{ $progress>=100?'success':($progress>=50?'primary':'warning') }}">{{ $progress }}%</div>
        <small class="text-muted">Progress</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fs-2 fw-bold {{ $order->isOverdue() ? 'text-danger' : 'text-success' }}">{{ $order->target_date->format('d M Y') }}</div>
        <small class="text-muted">Target Selesai {{ $order->isOverdue() ? '— ⚠️ TERLAMBAT' : '' }}</small>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-center">
      <div class="card-body py-3">
        <div class="fs-2 fw-bold text-info">{{ $order->handovers->count() }}</div>
        <small class="text-muted">Total Handover</small>
      </div>
    </div>
  </div>
</div>

<!-- Pipeline -->
<div class="card mb-4">
  <div class="card-header"><i class="bi bi-diagram-3 me-2 text-primary"></i>Posisi WIP di Pipeline</div>
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-start">
      @foreach($wipByStation as $sid => $data)
      <div class="pipeline-step flex-fill">
        @php $pct = $data['station']->bottleneck_threshold > 0 ? min(100,($data['qty_in_process']/$data['station']->bottleneck_threshold)*100) : 0; @endphp
        <div class="pipeline-dot {{ $data['qty_in_process'] > $data['station']->bottleneck_threshold ? 'red' : ($data['qty_in_process'] > 0 ? 'green' : 'grey') }}">
          {{ $data['qty_in_process'] }}
        </div>
        <div class="fw-semibold" style="font-size:.8rem">{{ $data['station']->name }}</div>
        <div style="font-size:.7rem" class="text-muted">Masuk: {{ $data['qty_in'] }}, Keluar: {{ $data['qty_out'] }}</div>
        @if($data['qty_reject'] > 0)<div class="text-danger" style="font-size:.7rem">Reject: {{ $data['qty_reject'] }}</div>@endif
      </div>
      @endforeach
    </div>
  </div>
</div>

<!-- Cutting Plans -->
<div class="card mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-scissors me-2 text-warning"></i>Cutting Plan</span>
    <a href="{{ route('cutting.create') }}?order_id={{ $order->id }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-plus"></i> Buat Manual</a>
  </div>
  @if($order->cuttingPlans->isEmpty())
  <div class="card-body text-center text-muted py-4">
    <i class="bi bi-scissors display-6 d-block mb-2 opacity-25"></i>
    Belum ada cutting plan. Gunakan tombol <strong>Kirim ke Cutting</strong> untuk membuat otomatis.
  </div>
  @else
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th>No. Plan</th><th>Tanggal</th><th>Target Qty</th><th>Realisasi</th><th>Bundle SKU</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @foreach($order->cuttingPlans->sortByDesc('created_at') as $cp)
        <tr>
          <td class="fw-semibold"><a href="{{ route('cutting.show',$cp) }}">{{ $cp->plan_no }}</a></td>
          <td>{{ $cp->planned_date->format('d M Y') }}</td>
          <td>{{ number_format($cp->planned_qty) }} pcs</td>
          <td class="{{ $cp->actual_qty ? 'text-success fw-semibold' : 'text-muted' }}">{{ $cp->actual_qty ? number_format($cp->actual_qty).' pcs' : '—' }}</td>
          <td><span class="badge bg-secondary bg-opacity-15 text-secondary">{{ $cp->bundles->count() }} SKU</span></td>
          <td><span class="badge bg-{{ $cp->status_color }}">{{ $cp->status_label }}</span></td>
          <td><a href="{{ route('cutting.show',$cp) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

<div class="row g-3">
  <!-- Items -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header"><i class="bi bi-list-ul me-2 text-primary"></i>Item SKU & Target</div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead><tr><th>SKU</th><th>Warna</th><th>Ukuran</th><th>Target</th></tr></thead>
          <tbody>
            @foreach($order->items as $item)
            <tr>
              <td><small class="text-monospace">{{ $item->sku->sku_code }}</small></td>
              <td>{{ optional($item->sku->color)->name }}</td>
              <td>{{ optional($item->sku->size)->name }}</td>
              <td class="fw-semibold">{{ number_format($item->target_qty) }} pcs</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot><tr><th colspan="3">Total</th><th>{{ number_format($order->getTotalTargetQty()) }} pcs</th></tr></tfoot>
        </table>
      </div>
    </div>
  </div>
  <!-- Handover History -->
  <div class="col-md-6">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-arrow-left-right me-2 text-primary"></i>Riwayat Handover</span>
        <a href="{{ route('handover.create') }}?order_id={{ $order->id }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i> Handover</a>
      </div>
      <div class="list-group list-group-flush">
        @forelse($order->handovers->sortByDesc('created_at') as $h)
        <a href="{{ route('handover.show',$h) }}" class="list-group-item list-group-item-action py-3">
          <div class="d-flex justify-content-between">
            <span class="fw-semibold">{{ $h->handover_no }}</span>
            <span class="badge bg-{{ $h->status_color }}">{{ $h->status_label }}</span>
          </div>
          <small class="text-muted">{{ $h->fromStation?->name ?? 'Order Produksi' }} → {{ $h->toStation->name }}</small>
          @if($h->hasDiscrepancy())<div><span class="badge bg-danger" style="font-size:.65rem">Ada Discrepancy</span></div>@endif
        </a>
        @empty
        <div class="list-group-item text-center text-muted py-4">Belum ada handover</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
