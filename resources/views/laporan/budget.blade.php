@extends('layouts.app')
@section('title', 'Laporan Budget')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="{{ route('laporan.index') }}">Laporan</a></li><li class="breadcrumb-item active">Budget</li></ol></nav>
        <h4 class="mb-0 fw-bold">Laporan Budget Produksi</h4>
    </div>
    <button onclick="window.print()" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i>Cetak</button>
</div>

<div class="row g-3 mb-4">
    @php
        $totalPlan = $budgets->sum('total_budget_plan');
        $totalActual = $budgets->sum('total_budget_actual');
        $totalVariance = $totalPlan - $totalActual;
        $overCount = $budgets->filter(fn($b)=>$b->isOverBudget())->count();
    @endphp
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-5 fw-bold text-primary">Rp {{ number_format($totalPlan,0,',','.') }}</div>
            <div class="small text-muted">Total Anggaran</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-5 fw-bold text-warning">Rp {{ number_format($totalActual,0,',','.') }}</div>
            <div class="small text-muted">Total Realisasi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-5 fw-bold {{ $totalVariance >= 0 ? 'text-success' : 'text-danger' }}">Rp {{ number_format(abs($totalVariance),0,',','.') }}</div>
            <div class="small text-muted">{{ $totalVariance >= 0 ? 'Sisa Anggaran' : 'Over Budget' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fs-2 fw-bold text-danger">{{ $overCount }}</div>
            <div class="small text-muted">Order Over Budget</div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Order</th>
                    <th>Produk</th>
                    <th class="text-end">Anggaran</th>
                    <th class="text-end">Realisasi</th>
                    <th class="text-end">Varians</th>
                    <th>Utilisasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($budgets as $b)
                @php
                    $variance = $b->getVarianceAttribute();
                    $utilPct = $b->getUtilisasiPercentAttribute();
                @endphp
                <tr class="{{ $b->isOverBudget() ? 'table-danger' : '' }}">
                    <td>
                        <a href="{{ route('budget.show', $b->order) }}" class="fw-semibold text-decoration-none">
                            {{ $b->order->order_number ?? '-' }}
                        </a>
                    </td>
                    <td>{{ $b->order->product->name ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($b->total_budget_plan,0,',','.') }}</td>
                    <td class="text-end">Rp {{ number_format($b->total_budget_actual,0,',','.') }}</td>
                    <td class="text-end fw-semibold {{ $variance >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $variance >= 0 ? '+' : '' }}Rp {{ number_format($variance,0,',','.') }}
                    </td>
                    <td style="min-width:140px">
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height:8px">
                                <div class="progress-bar {{ $utilPct > 100 ? 'bg-danger' : ($utilPct > 80 ? 'bg-warning' : 'bg-success') }}" style="width:{{ min($utilPct,100) }}%"></div>
                            </div>
                            <small class="fw-semibold">{{ $utilPct }}%</small>
                        </div>
                    </td>
                    <td>
                        @if($b->isOverBudget())
                        <span class="badge bg-danger">Over Budget</span>
                        @else
                        <span class="badge bg-success">Dalam Anggaran</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
