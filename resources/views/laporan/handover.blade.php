@extends('layouts.app')
@section('title', 'Laporan Handover')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li><li class="breadcrumb-item active">Handover</li></ol></nav>
        <h4 class="mb-0 fw-bold">Laporan Handover</h4>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('laporan.handover.excel', request()->query()) }}" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
      <a href="{{ route('laporan.handover.pdf', request()->query()) }}" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
      <button onclick="window.print()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-printer me-1"></i>Cetak</button>
    </div>
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
                <label class="form-label small fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Dikonfirmasi</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Disetujui</option>
                    <option value="discrepancy" {{ request('status')=='discrepancy'?'selected':'' }}>Selisih</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="{{ route('laporan.handover') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-primary">{{ $handovers->count() }}</div>
            <div class="small text-muted">Total Handover</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-success">{{ $handovers->where('status','approved')->count() }}</div>
            <div class="small text-muted">Disetujui</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-warning">{{ $handovers->where('status','pending')->count() }}</div>
            <div class="small text-muted">Pending</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-danger">{{ $handovers->where('status','discrepancy')->count() }}</div>
            <div class="small text-muted">Selisih</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Handover</th>
                    <th>No. Order</th>
                    <th>Dari → Ke Stasiun</th>
                    <th>Tgl Handover</th>
                    <th class="text-end">Dikirim</th>
                    <th class="text-end">Diterima</th>
                    <th class="text-end">Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($handovers as $h)
                <tr>
                    <td><a href="{{ route('handover.show', $h) }}" class="fw-semibold text-decoration-none">{{ $h->handover_number }}</a></td>
                    <td>{{ $h->order->order_number ?? '-' }}</td>
                    <td>{{ $h->fromStation->name ?? '-' }} → {{ $h->toStation->name ?? '-' }}</td>
                    <td>{{ $h->handover_date ? \Carbon\Carbon::parse($h->handover_date)->format('d M Y') : '-' }}</td>
                    <td class="text-end">{{ number_format($h->getTotalSentAttribute()) }}</td>
                    <td class="text-end">{{ number_format($h->getTotalReceivedAttribute()) }}</td>
                    <td class="text-end {{ $h->getTotalDiscrepancyAttribute() > 0 ? 'text-danger fw-bold' : '' }}">
                        {{ $h->getTotalDiscrepancyAttribute() > 0 ? number_format($h->getTotalDiscrepancyAttribute()) : '-' }}
                    </td>
                    <td><span class="badge bg-{{ $h->getStatusColorAttribute() }}">{{ $h->getStatusLabelAttribute() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
