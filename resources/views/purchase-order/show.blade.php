@extends('layouts.app')
@section('title','Detail PO')
@section('page-title','Detail Purchase Order')
@section('content')
<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb small mb-0">
    <li class="breadcrumb-item"><a href="{{ route('purchase-order.index') }}">Purchase Order</a></li>
    <li class="breadcrumb-item active">{{ $purchaseOrder->po_no }}</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
  <div>
    <h4 class="fw-bold mb-1">{{ $purchaseOrder->po_no }} <span class="badge bg-{{ $purchaseOrder->status_color }}">{{ $purchaseOrder->status_label }}</span></h4>
    <p class="text-muted mb-0">{{ $purchaseOrder->supplier->name }} | Dibuat oleh {{ $purchaseOrder->creator->name }}</p>
  </div>
  <div class="d-flex gap-2 flex-wrap">
    @if($purchaseOrder->status === 'draft' && auth()->user()->isSupervisor())
    <form method="POST" action="{{ route('purchase-order.send',$purchaseOrder) }}">
      @csrf
      <button type="submit" class="btn btn-primary" onclick="return confirm('Kirim PO ini ke supplier?')"><i class="bi bi-send me-2"></i>Kirim ke Supplier</button>
    </form>
    @endif
    @if(in_array($purchaseOrder->status,['draft','sent']) && auth()->user()->isSupervisor())
    <form method="POST" action="{{ route('purchase-order.cancel',$purchaseOrder) }}">
      @csrf
      <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Batalkan PO ini?')"><i class="bi bi-x-circle me-1"></i>Batalkan</button>
    </form>
    @endif
    <a href="{{ route('purchase-order.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-building me-2"></i>Informasi Supplier</div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted">Supplier</td><td class="fw-semibold">{{ $purchaseOrder->supplier->name }}</td></tr>
          <tr><td class="text-muted">Kontak</td><td>{{ $purchaseOrder->supplier->contact_person ?? '-' }}</td></tr>
          <tr><td class="text-muted">Telepon</td><td>{{ $purchaseOrder->supplier->phone ?? '-' }}</td></tr>
          <tr><td class="text-muted">Tgl Order</td><td>{{ $purchaseOrder->order_date->format('d M Y') }}</td></tr>
          <tr><td class="text-muted">Est. Tiba</td><td>{{ $purchaseOrder->expected_date?->format('d M Y') ?? '—' }}</td></tr>
          @if($purchaseOrder->sent_at)<tr><td class="text-muted">Dikirim</td><td>{{ $purchaseOrder->sent_at->format('d M Y H:i') }}</td></tr>@endif
          @if($purchaseOrder->received_at)<tr><td class="text-muted">Diterima</td><td>{{ $purchaseOrder->received_at->format('d M Y H:i') }}</td></tr>@endif
        </table>
        @if($purchaseOrder->notes)
        <div class="mt-2 p-2 rounded" style="background:#f8fafc;font-size:.82rem">{{ $purchaseOrder->notes }}</div>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-8">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Ringkasan</div>
      <div class="card-body">
        <div class="row g-3 text-center">
          <div class="col-4">
            <div class="fs-2 fw-bold text-primary">{{ $purchaseOrder->items->count() }}</div>
            <div class="small text-muted">Item</div>
          </div>
          <div class="col-4">
            <div class="fs-2 fw-bold text-success">Rp {{ number_format($purchaseOrder->total_amount,0,',','.') }}</div>
            <div class="small text-muted">Total PO</div>
          </div>
          <div class="col-4">
            @php $pct = $purchaseOrder->receive_progress @endphp
            <div class="fs-2 fw-bold {{ $pct>=100?'text-success':($pct>0?'text-warning':'text-muted') }}">{{ $pct }}%</div>
            <div class="small text-muted">Diterima</div>
          </div>
        </div>
        <div class="progress mt-3" style="height:10px">
          <div class="progress-bar bg-{{ $pct>=100?'success':'primary' }}" style="width:{{ $pct }}%"></div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Item Table + Receive Form --}}
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <span><i class="bi bi-list-ul me-2"></i>Detail Item</span>
    @php $pendingItems = $purchaseOrder->items->filter(fn($i) => $i->qty_ordered - $i->qty_received > 0)->count(); @endphp
    @if($pendingItems > 0 && !in_array($purchaseOrder->status,['received','cancelled']))
    <span class="badge bg-warning"><i class="bi bi-clock me-1"></i>{{ $pendingItems }} item belum lengkap</span>
    @elseif($purchaseOrder->status === 'received')
    <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Semua item diterima lengkap</span>
    @endif
  </div>
  @if(in_array($purchaseOrder->status,['sent','partial']) && auth()->user()->isStaffOrAbove())
  <form method="POST" action="{{ route('purchase-order.receive',$purchaseOrder) }}">
    @csrf
  @endif
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>Material</th><th>Satuan</th>
          <th class="text-end">Dipesan</th><th class="text-end">Diterima</th><th class="text-end">Sisa</th>
          <th class="text-end">Harga Satuan</th><th class="text-end">Total</th>
          @if(in_array($purchaseOrder->status,['sent','partial']) && auth()->user()->isStaffOrAbove())
          <th style="width:130px">Terima Sekarang</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($purchaseOrder->items as $item)
        @php $remaining = $item->qty_ordered - $item->qty_received; @endphp
        <tr class="{{ $remaining <= 0 ? 'table-success' : '' }}">
          <td><div class="fw-semibold">{{ $item->rawMaterial->name }}</div><small class="text-muted font-monospace">{{ $item->rawMaterial->code }}</small></td>
          <td>{{ $item->rawMaterial->unit }}</td>
          <td class="text-end">{{ number_format($item->qty_ordered, 2) }}</td>
          <td class="text-end fw-semibold {{ $item->qty_received > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($item->qty_received, 2) }}</td>
          <td class="text-end {{ $remaining > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">{{ $remaining > 0 ? number_format($remaining, 2) : '✓' }}</td>
          <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
          <td class="text-end fw-semibold">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
          @if(in_array($purchaseOrder->status,['sent','partial']) && auth()->user()->isStaffOrAbove())
          <td>
            @if($remaining > 0)
            <input type="number" name="items[{{ $item->id }}][qty_receive]" class="form-control form-control-sm" step="0.01" min="0" max="{{ $remaining }}" placeholder="0" value="0">
            @else
            <span class="badge bg-success">Lengkap</span>
            @endif
          </td>
          @endif
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="6" class="text-end fw-bold">TOTAL:</td>
          <td class="text-end fw-bold text-primary">Rp {{ number_format($purchaseOrder->total_amount, 0, ',', '.') }}</td>
          @if(in_array($purchaseOrder->status,['sent','partial']))<td></td>@endif
        </tr>
      </tfoot>
    </table>
  </div>
  @if(in_array($purchaseOrder->status,['sent','partial']) && auth()->user()->isStaffOrAbove())
  <div class="card-footer d-flex justify-content-end">
    <button type="submit" class="btn btn-success" id="receiveBtn" onclick="this.disabled=true;this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\' role=\'status\'></span>Memproses...';this.form.submit()"><i class="bi bi-box-arrow-in-down me-2"></i>Catat Penerimaan & Update Stok</button>
  </div>
  </form>
  @endif
</div>
@endsection
