@extends('layouts.app')
@section('title', 'Cutting Plan Schedule')
@section('page-title', 'Cutting Plan Schedule')
@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-start mb-4">
  <div>
    <h4 class="fw-bold mb-1"><i class="bi bi-scissors me-2 text-primary"></i>Cutting Plan Schedule</h4>
    <p class="text-muted mb-0">Perencanaan dan jadwal pemotongan kain di stasiun Cutting</p>
  </div>
  <a href="{{ route('cutting.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Buat Cutting Plan
  </a>
</div>

{{-- View Toggle --}}
<div class="d-flex align-items-center gap-3 mb-3">
  <div class="btn-group">
    <a href="{{ request()->fullUrlWithQuery(['view'=>'list']) }}" class="btn btn-sm {{ $view=='list' ? 'btn-primary' : 'btn-outline-primary' }}">
      <i class="bi bi-list-ul me-1"></i>List
    </a>
    <a href="{{ request()->fullUrlWithQuery(['view'=>'calendar']) }}" class="btn btn-sm {{ $view=='calendar' ? 'btn-primary' : 'btn-outline-primary' }}">
      <i class="bi bi-calendar3 me-1"></i>Kalender
    </a>
  </div>

  {{-- Status Legend --}}
  <div class="d-flex gap-2 flex-wrap ms-2">
    <span class="badge bg-secondary">Draft</span>
    <span class="badge bg-primary">Terjadwal</span>
    <span class="badge bg-warning text-dark">Sedang Dipotong</span>
    <span class="badge bg-success">Selesai</span>
    <span class="badge bg-danger">Dibatalkan</span>
  </div>
</div>

{{-- ===== CALENDAR VIEW ===== --}}
@if($view === 'calendar')
<div class="card border-0 shadow-sm mb-3">
  <div class="card-header d-flex justify-content-between align-items-center py-3">
    <a href="{{ request()->fullUrlWithQuery(['month' => $monthDate->copy()->subMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-chevron-left"></i>
    </a>
    <h5 class="mb-0 fw-bold">{{ $monthDate->translatedFormat('F Y') }}</h5>
    <a href="{{ request()->fullUrlWithQuery(['month' => $monthDate->copy()->addMonth()->format('Y-m')]) }}" class="btn btn-sm btn-outline-secondary">
      <i class="bi bi-chevron-right"></i>
    </a>
  </div>
  <div class="card-body p-0">
    {{-- Day headers --}}
    <div class="row g-0 border-bottom bg-light">
      @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
      <div class="col text-center py-2 fw-semibold small text-muted border-end">{{ $day }}</div>
      @endforeach
    </div>

    {{-- Calendar grid --}}
    @php
      $startDay = $monthDate->copy()->startOfMonth()->dayOfWeekIso; // 1=Mon, 7=Sun
      $daysInMonth = $monthDate->daysInMonth;
      $today = now()->format('Y-m-d');
      $cellNum = 0;
    @endphp

    <div class="row g-0">
      {{-- Empty cells before first day --}}
      @for($i = 1; $i < $startDay; $i++)
        <div class="col border-end border-bottom calendar-cell bg-light opacity-50" style="min-height:110px"></div>
        @php $cellNum++ @endphp
      @endfor

      {{-- Day cells --}}
      @for($d = 1; $d <= $daysInMonth; $d++)
        @php
          $dateKey = $monthDate->copy()->startOfMonth()->addDays($d-1)->format('Y-m-d');
          $dayPlans = $calendarPlans[$dateKey] ?? collect();
          $isToday = $dateKey === $today;
          $cellNum++;
        @endphp
        <div class="col border-end border-bottom calendar-cell {{ $isToday ? 'bg-primary bg-opacity-5' : '' }}" style="min-height:110px">
          <div class="p-2">
            <div class="d-flex align-items-center mb-1">
              <span class="calendar-day-num {{ $isToday ? 'bg-primary text-white rounded-circle d-flex align-items-center justify-content-center' : 'text-muted' }}"
                style="width:26px;height:26px;font-size:0.85rem;font-weight:600">{{ $d }}</span>
              @if($dayPlans->count() > 0)
              <span class="ms-auto badge bg-secondary bg-opacity-25 text-secondary" style="font-size:0.65rem">
                {{ $dayPlans->count() }} plan
              </span>
              @endif
            </div>
            @foreach($dayPlans->take(3) as $p)
            <a href="{{ route('cutting.show', $p) }}" class="text-decoration-none d-block mb-1">
              <div class="rounded px-2 py-1 bg-{{ $p->status_color }} bg-opacity-15 border border-{{ $p->status_color }} border-opacity-25"
                style="font-size:0.72rem;line-height:1.3">
                <div class="fw-semibold text-{{ $p->status_color === 'warning' ? 'dark' : $p->status_color }} text-truncate">
                  {{ $p->plan_no }}
                </div>
                <div class="text-muted text-truncate">{{ $p->order->product->name }}</div>
                <div><i class="bi bi-scissors" style="font-size:0.65rem"></i> {{ number_format($p->planned_qty) }} pcs</div>
              </div>
            </a>
            @endforeach
            @if($dayPlans->count() > 3)
            <div class="text-muted" style="font-size:0.72rem">+{{ $dayPlans->count()-3 }} lainnya</div>
            @endif
          </div>
        </div>

        {{-- New row every 7 cells --}}
        @if($cellNum % 7 === 0 && $d < $daysInMonth)
    </div><div class="row g-0">
        @endif
      @endfor

      {{-- Fill remaining cells --}}
      @php $remaining = 7 - ($cellNum % 7 === 0 ? 7 : $cellNum % 7); @endphp
      @for($i = 0; $i < $remaining && $remaining < 7; $i++)
        <div class="col border-end border-bottom calendar-cell bg-light opacity-50" style="min-height:110px"></div>
      @endfor
    </div>
  </div>
</div>

{{-- Upcoming & today plans below calendar --}}
<div class="row g-3">
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold"><i class="bi bi-clock me-2 text-warning"></i>Hari Ini & Terjadwal</div>
      <div class="card-body p-0">
        @php
          $upcoming = collect($calendarPlans->flatten())->filter(fn($p) => in_array($p->status, ['scheduled','in_progress']))->sortBy('planned_date')->take(8);
        @endphp
        @forelse($upcoming as $p)
        <div class="d-flex align-items-center px-3 py-2 border-bottom">
          <div class="me-3 text-center" style="min-width:40px">
            <div class="fw-bold text-primary" style="font-size:1.1rem">{{ $p->planned_date->format('d') }}</div>
            <div class="text-muted" style="font-size:0.7rem">{{ $p->planned_date->format('M') }}</div>
          </div>
          <div class="flex-grow-1">
            <div class="fw-semibold small">{{ $p->plan_no }}</div>
            <div class="text-muted" style="font-size:0.78rem">{{ $p->order->product->name }}</div>
          </div>
          <span class="badge bg-{{ $p->status_color }}">{{ $p->status_label }}</span>
        </div>
        @empty
        <div class="text-center py-4 text-muted small">Tidak ada plan terjadwal bulan ini</div>
        @endforelse
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header fw-semibold"><i class="bi bi-bar-chart me-2 text-success"></i>Ringkasan Bulan Ini</div>
      <div class="card-body">
        @php
          $allPlans = collect($calendarPlans->flatten());
          $totalPlanned = $allPlans->sum('planned_qty');
          $totalActual  = $allPlans->whereNotNull('actual_qty')->sum('actual_qty');
        @endphp
        <div class="row g-3 text-center">
          <div class="col-6">
            <div class="p-3 bg-primary bg-opacity-10 rounded">
              <div class="fs-3 fw-bold text-primary">{{ $allPlans->count() }}</div>
              <small class="text-muted">Total Plan</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-success bg-opacity-10 rounded">
              <div class="fs-3 fw-bold text-success">{{ $allPlans->where('status','completed')->count() }}</div>
              <small class="text-muted">Selesai</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-info bg-opacity-10 rounded">
              <div class="fs-4 fw-bold text-info">{{ number_format($totalPlanned) }}</div>
              <small class="text-muted">Target Qty</small>
            </div>
          </div>
          <div class="col-6">
            <div class="p-3 bg-warning bg-opacity-10 rounded">
              <div class="fs-4 fw-bold text-warning">{{ number_format($totalActual) }}</div>
              <small class="text-muted">Realisasi Qty</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== LIST VIEW ===== --}}
@else
{{-- Filter --}}
<div class="card border-0 shadow-sm mb-3">
  <div class="card-body py-2">
    <form method="GET" action="{{ route('cutting.index') }}" class="row g-2 align-items-end">
      <input type="hidden" name="view" value="list">
      <div class="col-md-4">
        <label class="form-label small mb-1 fw-semibold">Order</label>
        <select name="order_id" class="form-select form-select-sm">
          <option value="">Semua Order</option>
          @foreach($orders as $o)
          <option value="{{ $o->id }}" {{ request('order_id')==$o->id?'selected':'' }}>{{ $o->order_no }} — {{ $o->product->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1 fw-semibold">Status</label>
        <select name="status" class="form-select form-select-sm">
          <option value="">Semua Status</option>
          @foreach(['draft'=>'Draft','scheduled'=>'Terjadwal','in_progress'=>'Sedang Dipotong','completed'=>'Selesai','cancelled'=>'Dibatalkan'] as $val=>$lbl)
          <option value="{{ $val }}" {{ request('status')==$val?'selected':'' }}>{{ $lbl }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label small mb-1 fw-semibold">Bulan</label>
        <input type="month" name="month_filter" class="form-control form-control-sm" value="{{ request('month_filter') }}">
      </div>
      <div class="col-md-2 d-flex gap-1">
        <button type="submit" class="btn btn-sm btn-primary flex-fill"><i class="bi bi-search"></i></button>
        <a href="{{ route('cutting.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>No. Plan</th>
          <th>Order / Produk</th>
          <th class="text-center">Tanggal</th>
          <th class="text-center">Target Qty</th>
          <th class="text-center">Realisasi</th>
          <th class="text-center">Progress</th>
          <th class="text-center">Bundle</th>
          <th class="text-center">Status</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($plans as $p)
        <tr>
          <td>
            <a href="{{ route('cutting.show', $p) }}" class="fw-semibold text-decoration-none font-monospace">{{ $p->plan_no }}</a>
            @if($p->shift)<br><small class="text-muted"><i class="bi bi-clock me-1"></i>{{ ucfirst($p->shift) }}</small>@endif
          </td>
          <td>
            <div class="fw-semibold small">{{ $p->order->order_no }}</div>
            <small class="text-muted">{{ $p->order->product->name }}</small>
          </td>
          <td class="text-center">
            <div class="fw-semibold">{{ $p->planned_date->format('d M Y') }}</div>
            @if($p->planned_date->isToday())<span class="badge bg-info bg-opacity-15 text-info" style="font-size:0.65rem">Hari Ini</span>@endif
          </td>
          <td class="text-center fw-semibold">{{ number_format($p->planned_qty) }}</td>
          <td class="text-center {{ $p->actual_qty ? 'text-success fw-semibold' : 'text-muted' }}">{{ $p->actual_qty ? number_format($p->actual_qty) : '-' }}</td>
          <td class="text-center" style="min-width:100px">
            <div class="progress" style="height:6px">
              <div class="progress-bar bg-{{ $p->status_color }}" style="width:{{ $p->progress }}%"></div>
            </div>
            <small class="text-muted">{{ $p->progress }}%</small>
          </td>
          <td class="text-center">
            <span class="badge bg-secondary bg-opacity-15 text-secondary">{{ $p->bundles_count ?? $p->bundles->count() }} bundle</span>
          </td>
          <td class="text-center"><span class="badge bg-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
          <td class="text-center">
            <div class="btn-group btn-group-sm">
              <a href="{{ route('cutting.show', $p) }}" class="btn btn-outline-primary" title="Detail"><i class="bi bi-eye"></i></a>
              <a href="{{ route('cutting.edit', $p) }}" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
              @if(!in_array($p->status, ['in_progress','completed']))
              <form method="POST" action="{{ route('cutting.destroy', $p) }}" onsubmit="return confirm('Hapus cutting plan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="text-center py-5 text-muted">
            <i class="bi bi-scissors display-6 d-block mb-2 opacity-25"></i>
            Belum ada cutting plan
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($plans->hasPages())
  <div class="card-footer">{{ $plans->links() }}</div>
  @endif
</div>
@endif

<style>
.calendar-cell { transition: background 0.15s; }
.calendar-cell:hover { background: rgba(var(--bs-primary-rgb), 0.03) !important; }
</style>
@endsection
