@extends('layouts.app')
@section('title','Budget & Biaya')
@section('page-title','Budget & Biaya Produksi')
@section('content')
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="kpi-card kpi-blue"><div class="kpi-icon"><i class="bi bi-wallet2"></i></div><div class="kpi-value">Rp {{ number_format($totalPlan/1000000,1) }}jt</div><div class="kpi-label">Total Budget Rencana</div></div></div>
  <div class="col-md-4"><div class="kpi-card {{ $totalActual>$totalPlan?'kpi-red':'kpi-teal' }}"><div class="kpi-icon"><i class="bi bi-cash-coin"></i></div><div class="kpi-value">Rp {{ number_format($totalActual/1000000,1) }}jt</div><div class="kpi-label">Total Realisasi</div></div></div>
  <div class="col-md-4"><div class="kpi-card {{ $totalActual>$totalPlan?'kpi-red':'kpi-green' }}"><div class="kpi-icon"><i class="bi bi-graph-up"></i></div><div class="kpi-value">{{ $totalPlan>0?number_format(($totalActual/$totalPlan)*100,1):0 }}%</div><div class="kpi-label">Utilisasi Budget</div></div></div>
</div>
<div class="card">
  <div class="card-header"><i class="bi bi-wallet2 me-2 text-primary"></i>Budget per Order Produksi</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>Order</th><th>Produk</th><th>Budget Plan</th><th>Realisasi</th><th>Variance</th><th>Utilisasi</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse($orders as $o)
        @php $b = $o->budget; $util = $b ? $b->utilisasi_percent : null; $isOver = $b ? $b->isOverBudget() : false; @endphp
        <tr class="{{ $isOver ? 'table-danger' : '' }}">
          <td><a href="{{ route('budget.show',$o) }}" class="fw-bold text-primary text-decoration-none">{{ $o->order_no }}</a></td>
          <td>{{ $o->product->name }}</td>
          <td>Rp {{ $b ? number_format($b->total_plan) : '-' }}</td>
          <td>Rp {{ $b ? number_format($b->total_actual) : '-' }}</td>
          <td class="{{ $isOver?'text-danger fw-bold':'text-success fw-bold' }}">{{ $b ? 'Rp '.number_format($b->variance) : '-' }}</td>
          <td>
            @if($b)
            <div class="d-flex align-items-center gap-2">
              <div class="progress flex-grow-1" style="min-width:80px"><div class="progress-bar {{ $util>100?'bg-danger':($util>80?'bg-warning':'bg-success') }}" style="width:{{ min(100,$util) }}%"></div></div>
              <small class="fw-semibold" style="min-width:40px">{{ number_format($util,0) }}%</small>
            </div>
            @else<span class="text-muted">-</span>@endif
          </td>
          <td>
            @if($b)
              @if($isOver)<span class="badge bg-danger">Over Budget</span>
              @elseif($util>80)<span class="badge bg-warning text-dark">Hampir Penuh</span>
              @else<span class="badge bg-success">On Track</span>@endif
            @else<span class="badge bg-secondary">Belum Ada Budget</span>@endif
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('budget.show',$o) }}" class="btn btn-sm btn-outline-primary py-1">Detail</a>
              <a href="{{ route('budget.edit',$o) }}" class="btn btn-sm btn-outline-secondary py-1"><i class="bi bi-pencil"></i></a>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted py-5"><i class="bi bi-inbox display-5 d-block mb-2"></i>Belum ada data budget</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
