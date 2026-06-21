@extends('layouts.app')
@section('title','Handover')
@section('page-title','Manajemen Handover')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <div>
    <h5 class="mb-0 fw-bold">Daftar Handover</h5>
    <p class="text-muted small mb-0">Serah terima digital antar stasiun produksi</p>
  </div>
  @if(!auth()->user()->isManager())
  <a href="{{ route('handover.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Buat Handover</a>
  @endif
</div>

<!-- Filter -->
<div class="card mb-3">
  <div class="card-body py-2 px-3">
    <form method="GET" class="row g-2 align-items-center">
      <div class="col-auto">
        <small class="text-muted fw-semibold"><i class="bi bi-funnel me-1"></i>Filter:</small>
      </div>
      <div class="col">
        <select name="status" class="form-select form-select-sm" style="max-width:180px">
          <option value="">Semua Status</option>
          @foreach(['pending'=>'⏳ Pending','confirmed'=>'✅ Dikonfirmasi','discrepancy'=>'⚠️ Ada Selisih','approved'=>'✔ Disetujui'] as $v=>$l)
          <option value="{{ $v }}" {{ request('status')==$v?'selected':'' }}>{{ $l }}</option>
          @endforeach
        </select>
      </div>
      <div class="col">
        <select name="station_id" class="form-select form-select-sm" style="max-width:200px">
          <option value="">Semua Stasiun</option>
          @foreach($stations as $st)<option value="{{ $st->id }}" {{ request('station_id')==$st->id?'selected':'' }}>{{ $st->name }}</option>@endforeach
        </select>
      </div>
      <div class="col-auto d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Cari</button>
        <a href="{{ route('handover.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead>
        <tr>
          <th>No. Handover</th>
          <th>Order</th>
          <th>Rute</th>
          <th class="text-center">Kirim</th>
          <th class="text-center">Terima</th>
          <th class="text-center">Selisih</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($handovers as $h)
        <tr>
          <td>
            <a href="{{ route('handover.show',$h) }}" class="fw-semibold text-primary text-decoration-none" style="font-size:.82rem;font-family:ui-monospace,monospace">{{ $h->handover_no }}</a>
          </td>
          <td>
            <div class="fw-semibold" style="font-size:.82rem">{{ $h->order->order_no }}</div>
            <div class="text-muted" style="font-size:.75rem">{{ Str::limit($h->order->product->name, 22) }}</div>
          </td>
          <td>
            <div class="d-flex align-items-center gap-1" style="font-size:.8rem">
              <span class="text-muted">{{ $h->fromStation?->name ?? 'Order Produksi' }}</span>
              <i class="bi bi-arrow-right text-muted" style="font-size:.7rem"></i>
              <span class="fw-semibold">{{ $h->toStation->name }}</span>
            </div>
          </td>
          <td class="text-center fw-semibold">{{ $h->total_sent }}</td>
          <td class="text-center {{ $h->total_received !== null ? 'fw-semibold text-success' : 'text-muted' }}">{{ $h->total_received ?? '—' }}</td>
          <td class="text-center">
            @if($h->total_discrepancy != 0)
            <span class="badge bg-danger">{{ $h->total_discrepancy }}</span>
            @else<span class="text-muted">—</span>@endif
          </td>
          <td><span class="badge bg-{{ $h->status_color }}">{{ $h->status_label }}</span></td>
          <td><small class="text-muted">{{ $h->initiated_at ? $h->initiated_at->format('d M Y') : $h->created_at->format('d M Y') }}</small></td>
          <td><a href="{{ route('handover.show',$h) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a></td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <i class="bi bi-arrow-left-right"></i>
              <p class="fw-semibold mb-1">Belum ada handover</p>
              <p>Buat handover pertama untuk mulai melacak perpindahan barang</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($handovers->hasPages())
  <div class="card-footer py-2">{{ $handovers->appends(request()->query())->links() }}</div>
  @endif
</div>
@endsection
