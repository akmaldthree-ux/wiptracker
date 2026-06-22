@component('mail::message')
# {{ $alertType === 'over' ? '🚨 Over Budget' : '⚠ Budget Hampir Penuh' }}

Halo **{{ $recipient->name }}**,

@php $util = $budget->total_plan > 0 ? round(($budget->total_actual / $budget->total_plan) * 100, 1) : 0; @endphp

@if($alertType === 'over')
Realisasi biaya untuk order **{{ $budget->order->order_no }}** telah **melampaui budget** yang ditetapkan.
@else
Realisasi biaya untuk order **{{ $budget->order->order_no }}** telah mencapai **{{ $util }}%** dari budget — mendekati batas maksimum.
@endif

@component('mail::panel')
**Order:** {{ $budget->order->order_no }} — {{ $budget->order->product->name }}

**Budget Rencana:** Rp {{ number_format($budget->total_plan) }}

**Realisasi Saat Ini:** Rp {{ number_format($budget->total_actual) }}

**Utilisasi:** {{ $alertType === 'over' ? '🔴' : '🟡' }} **{{ $util }}%**

@if($alertType === 'over')
**Selisih Over:** 🔴 Rp {{ number_format($budget->total_actual - $budget->total_plan) }}
@else
**Sisa Budget:** Rp {{ number_format($budget->total_plan - $budget->total_actual) }}
@endif
@endcomponent

@component('mail::button', ['url' => url("/budget/{$budget->order_id}"), 'color' => $alertType === 'over' ? 'red' : 'yellow'])
Lihat Detail Budget
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
