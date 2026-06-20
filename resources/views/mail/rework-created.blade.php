@component('mail::message')
# Rework Order Dibuat

Sebuah rework order telah dibuat untuk ditindaklanjuti.

@component('mail::panel')
**Handover:** {{ $rework->handover_no ?? '-' }}
**Order:** {{ $rework->order->order_no ?? '-' }}
**Dibuat:** {{ \Carbon\Carbon::parse($rework->created_at)->format('d M Y H:i') }}
@endcomponent

@component('mail::button', ['url' => url('/handover'), 'color' => 'primary'])
Lihat Handover
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
