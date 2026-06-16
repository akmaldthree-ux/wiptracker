@extends('layouts.app')
@section('title', 'Laporan Produksi')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li><li class="breadcrumb-item active">Produksi</li></ol></nav>
        <h4 class="mb-0 fw-bold">Laporan Produksi</h4>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Cetak</button>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Produk</label>
                <select name="product_id" class="form-select">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id')==$p->id?'selected':'' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                    <option value="active" {{ request('status')=='active'?'selected':'' }}>Aktif</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Selesai</option>
                    <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('laporan.produksi') }}" class="btn btn-outline-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Daftar Order Produksi</h6>
        <span class="badge bg-secondary">{{ $orders->count() }} order</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Order</th>
                    <th>Produk / Series</th>
                    <th>Target Qty</th>
                    <th>Progress</th>
                    <th>Deadline</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" class="fw-semibold text-decoration-none">{{ $order->order_number }}</a>
                        <div class="small text-muted">{{ $order->created_at->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $order->product->name ?? '-' }}</div>
                        <div class="small text-muted">{{ $order->series->name ?? '-' }}</div>
                    </td>
                    <td>{{ number_format($order->getTotalTargetQty()) }} pcs</td>
                    <td style="min-width:160px">
                        @php $pct = $order->getProgressPercentage() @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar {{ $pct>=100?'bg-success':($pct>=50?'bg-primary':'bg-warning') }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="fw-semibold">{{ $pct }}%</small>
                        </div>
                    </td>
                    <td>
                        {{ $order->target_date ? \Carbon\Carbon::parse($order->target_date)->format('d M Y') : '-' }}
                        @if($order->isOverdue())
                        <span class="badge bg-danger ms-1">Terlambat</span>
                        @endif
                    </td>
                    <td><span class="badge bg-{{ $order->getStatusColorAttribute() }}">{{ $order->getStatusLabelAttribute() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
