@component('mail::message')
# Alert: Handover Pending Lebih dari 24 Jam

Terdapat **{{ count($handovers) }}** handover yang masih pending lebih dari 24 jam dan memerlukan perhatian segera.

@component('mail::table')
| No. Handover | Order | Dari Stasiun | Dibuat |
|:------------|:------|:-------------|:-------|
@foreach($handovers as $h)
| {{ $h->handover_no }} | {{ $h->order->order_no ?? '-' }} | {{ $h->fromStation->name ?? '-' }} | {{ \Carbon\Carbon::parse($h->created_at)->format('d M Y H:i') }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url('/handover'), 'color' => 'primary'])
Lihat Handover
@endcomponent

Segera konfirmasi handover yang masih pending untuk menjaga kelancaran produksi.

Salam,<br>
{{ config('app.name') }}
@endcomponent
