@extends('layouts.app')
@section('title','Detail Budget')
@section('page-title','Detail Budget Produksi')
@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ $order->order_no }}</h4>
    <p class="text-muted mb-0">{{ $order->product->name }} — {{ optional($order->series)->name }}</p>
  </div>
  <div class="d-flex gap-2">
    <a href="{{ route('budget.edit',$order) }}" class="btn btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit Budget</a>
    <a href="{{ route('budget.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>
</div>
@if($budget)
<div class="row g-3 mb-4">
  @foreach([['label'=>'Material','plan'=>$budget->material_cost_plan,'actual'=>$budget->material_cost_actual],['label'=>'Proses','plan'=>$budget->process_cost_plan,'actual'=>$budget->process_cost_actual],['label'=>'Overhead','plan'=>$budget->overhead_cost_plan,'actual'=>$budget->overhead_cost_actual],['label'=>'Total','plan'=>$budget->total_plan,'actual'=>$budget->total_actual]] as $item)
  @php $pct = $item['plan']>0?($item['actual']/$item['plan'])*100:0; $over=$item['actual']>$item['plan']; @endphp
  <div class="col-md-3">
    <div class="card text-center {{ $over?'border-danger':'' }}">
      <div class="card-body">
        <div class="text-muted small mb-1">{{ $item['label'] }}</div>
        <div class="fw-bold text-muted">Plan: Rp {{ number_format($item['plan']) }}</div>
        <div class="fs-5 fw-bold {{ $over?'text-danger':'text-success' }}">Aktual: Rp {{ number_format($item['actual']) }}</div>
        <div class="progress mt-2"><div class="progress-bar {{ $over?'bg-danger':($pct>80?'bg-warning':'bg-success') }}" style="width:{{ min(100,$pct) }}%"></div></div>
        <small class="{{ $over?'text-danger fw-bold':'text-muted' }}">{{ number_format($pct,1) }}%</small>
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Belum ada budget untuk order ini. <a href="{{ route('budget.edit',$order) }}">Buat budget sekarang</a></div>
@endif

<!-- Input Biaya -->
<div class="row g-3">
  <div class="col-md-5">
    <div class="card">
      <div class="card-header"><i class="bi bi-plus-circle me-2 text-primary"></i>Input Realisasi Biaya</div>
      <div class="card-body">
        <form method="POST" action="{{ route('budget.cost.store',$order) }}">
          @csrf
          <div class="mb-3">
            <label class="form-label fw-semibold">Tipe Biaya</label>
            <select name="type" class="form-select form-select-sm" required>
              <option value="material">Bahan Baku</option>
              <option value="process">Proses Produksi</option>
              <option value="overhead">Overhead</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label fw-semibold">Deskripsi</label><input type="text" name="description" class="form-control form-control-sm" required></div>
          <div class="mb-3"><label class="form-label fw-semibold">Jumlah (Rp)</label><input type="number" name="amount" class="form-control form-control-sm" min="0" required></div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Stasiun (Opsional)</label>
            <select name="station_id" class="form-select form-select-sm">
              <option value="">-- Pilih Stasiun --</option>
              @foreach($stations as $st)<option value="{{ $st->id }}">{{ $st->name }}</option>@endforeach
            </select>
          </div>
          <div class="mb-3"><label class="form-label fw-semibold">Tanggal</label><input type="date" name="entry_date" class="form-control form-control-sm" value="{{ today()->toDateString() }}" required></div>
          <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-check me-1"></i>Input Biaya</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header"><i class="bi bi-list-ul me-2"></i>Riwayat Biaya</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead><tr><th>Tanggal</th><th>Tipe</th><th>Deskripsi</th><th>Jumlah</th></tr></thead>
          <tbody>
            @forelse($costEntries as $ce)
            <tr>
              <td><small>{{ $ce->entry_date->format('d M Y') }}</small></td>
              <td><span class="badge bg-secondary text-white" style="font-size:.7rem">{{ $ce->type_label }}</span></td>
              <td>{{ $ce->description }}</td>
              <td class="fw-semibold">Rp {{ number_format($ce->amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada entri biaya</td></tr>
            @endforelse
          </tbody>
          @if($costEntries->count() > 0)
          <tfoot><tr><th colspan="3">Total</th><th>Rp {{ number_format($costEntries->sum('amount')) }}</th></tr></tfoot>
          @endif
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
