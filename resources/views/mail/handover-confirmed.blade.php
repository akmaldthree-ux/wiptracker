@component('mail::message')
# Handover {{ $handover->status === 'discrepancy' ? 'Ada Selisih (Discrepancy)' : 'Telah Dikonfirmasi' }}

Halo **{{ $recipient->name }}**,

@if($handover->status === 'discrepancy')
Handover **{{ $handover->handover_no }}** telah dikonfirmasi namun terdapat **selisih quantity** yang memerlukan persetujuan Anda.
@else
Handover **{{ $handover->handover_no }}** yang Anda kirimkan telah **dikonfirmasi** oleh penerima.
@endif

@component('mail::panel')
**No. Handover:** {{ $handover->handover_no }}

**Order:** {{ $handover->order->order_no }} — {{ $handover->order->product->name }}

**Dari:** {{ $handover->fromStation?->name ?? 'Order Produksi' }} → **{{ $handover->toStation->name }}**

**Dikonfirmasi Oleh:** {{ $handover->confirmedBy?->name }}

**Waktu Konfirmasi:** {{ $handover->confirmed_at?->format('d M Y, H:i') }} WIB

**Status:** {{ $handover->status === 'discrepancy' ? '⚠ Ada Selisih' : '✓ Dikonfirmasi' }}
@endcomponent

@component('mail::table')
| SKU | Qty Kirim | Qty Terima | Selisih |
|:----|----------:|-----------:|--------:|
@foreach($handover->items as $item)
| {{ $item->sku->sku_code }} | {{ number_format($item->qty_sent) }} | {{ $item->qty_received ?? '-' }} | {{ $item->discrepancy !== null ? ($item->discrepancy != 0 ? '⚠ '.$item->discrepancy : '✓ 0') : '-' }} |
@endforeach
@endcomponent

@component('mail::button', ['url' => url("/handover/{$handover->id}"), 'color' => $handover->status === 'discrepancy' ? 'red' : 'primary'])
{{ $handover->status === 'discrepancy' ? 'Tinjau Discrepancy' : 'Lihat Detail Handover' }}
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
