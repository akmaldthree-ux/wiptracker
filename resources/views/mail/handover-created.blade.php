@component('mail::message')
# Handover Masuk — Perlu Konfirmasi Anda

Halo **{{ $recipient->name }}**,

Terdapat handover baru yang masuk ke stasiun **{{ $handover->toStation->name }}** dan memerlukan konfirmasi Anda.

---

@component('mail::panel')
**No. Handover:** {{ $handover->handover_no }}

**Order Produksi:** {{ $handover->order->order_no }} — {{ $handover->order->product->name }}

**Dari Stasiun:** {{ $handover->fromStation->name ?? '-' }}

**Ke Stasiun:** {{ $handover->toStation->name }}

**Dikirim Oleh:** {{ $handover->initiatedBy->name }}

**Waktu:** {{ $handover->initiated_at->format('d M Y, H:i') }} WIB
@endcomponent

**Rincian Item:**

@component('mail::table')
| SKU | Warna | Ukuran | Qty Dikirim |
|:----|:------|:-------|------------:|
@foreach($handover->items as $item)
| {{ $item->sku->sku_code }} | {{ optional($item->sku->color)->name ?? '-' }} | {{ optional($item->sku->size)->name ?? '-' }} | {{ number_format($item->qty_sent) }} pcs |
@endforeach
@endcomponent

Segera konfirmasi penerimaan barang untuk menjaga kelancaran alur produksi.

@component('mail::button', ['url' => url("/handover/{$handover->id}"), 'color' => 'primary'])
Konfirmasi Sekarang
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
