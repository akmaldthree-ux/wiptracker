@extends('layouts.app')
@section('title','Handover')
@section('page-title','Manajemen Handover')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h5 class="mb-0 fw-bold">Daftar Handover</h5>
    <p class="text-muted small mb-0">Serah terima digital antar stasiun produksi</p>
  </div>
  @if(!auth()->user()->isManager())
  <a href="{{ route('handover.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Buat Handover</a>
  @endif
</div>
<div class="card mb-4">
  <div class="card-body py-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          @foreach(['pending'=>'Pending','confirmed'=>'Dikonfirmasi','discrepancy'=>'Ada Selisih','approved'=>'Disetujui'] as $v=>$l)
          <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select name="station_id" class="form-select form-select-sm">
          <option value="">Semua Stasiun</option>
          @foreach($stations as $st)<option value="{{ $st->id }}" {{ request('station_id')==$st->id?'selected':'' }}>{{ $st->name }}</option>@endforeach
        </select>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-filter me-1"></i>Filter</button></div>
      <div class="col-md-2"><a href="{{ route('handover.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>No. Handover</th><th>Order</th><th>Dari → Ke</th><th>Total Kirim</th><th>Total Terima</th><th>Discrepancy</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
      <tbody>
        @forelse($handovers as $h)
        <tr>
          <td><a href="{{ route('handover.show',$h) }}" class="fw-bold text-primary text-decoration-none">{{ $h->handover_no }}</a></td>
          <td><small>{{ $h->order->order_no }}</small><br><small class="text-muted">{{ $h->order->product->name }}</small></td>
          <td>{{ $h->fromStation?->name ?? 'Order Produksi' }} <i class="bi bi-arrow-right text-muted"></i> {{ $h->toStation->name }}</td>
          <td class="fw-semibold">{{ $h->total_sent }}</td>
          <td class="fw-semibold">{{ $h->total_received ?? '-' }}</td>
          <td>
            @if($h->total_discrepancy != 0)
            <span class="text-danger fw-bold">{{ $h->total_discrepancy }}</span>
            @else<span class="text-muted">—</span>@endif
          </td>
          <td><span class="badge bg-{{ $h->status_color }}">{{ $h->status_label }}</span></td>
          <td><small class="text-muted">{{ $h->initiated_at ? $h->initiated_at->format('d M Y') : $h->created_at->format('d M Y') }}</small></td>
          <td><a href="{{ route('handover.show',$h) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a></td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Belum ada handover</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($handovers->hasPages())
  <div class="card-footer py-3">{{ $handovers->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
