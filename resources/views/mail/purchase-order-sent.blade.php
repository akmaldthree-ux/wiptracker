@component('mail::message')
# Purchase Order dari DTHREE Production

Yth. **{{ $po->supplier->contact_person }}**,
**{{ $po->supplier->name }}**

Bersama email ini kami menyampaikan bahwa kami telah menerbitkan **Purchase Order** kepada perusahaan Anda. Mohon untuk segera dikonfirmasi dan diproses.

@component('mail::panel')
**No. PO:** {{ $po->po_no }}

**Tanggal PO:** {{ $po->created_at->format('d M Y') }}

**Batas Pengiriman:** {{ $po->expected_date?->format('d M Y') ?? '-' }}

@if($po->notes)
**Catatan:** {{ $po->notes }}
@endif
@endcomponent

**Rincian Item:**

@component('mail::table')
| Bahan Baku | Qty | Satuan | Harga/Unit | Total |
|:-----------|:---:|:------:|:----------:|------:|
@foreach($po->items as $item)
| {{ $item->rawMaterial->name }} | {{ number_format($item->qty_ordered) }} | {{ $item->rawMaterial->unit }} | Rp {{ number_format($item->unit_price) }} | Rp {{ number_format($item->total_price) }} |
@endforeach
@endcomponent

**Total Nilai PO: Rp {{ number_format($po->items->sum('total_price')) }}**

Mohon konfirmasi penerimaan PO ini dengan membalas email ini. Pastikan pengiriman sesuai dengan batas tanggal yang tertera.

Terima kasih atas kerjasamanya.

Hormat kami,<br>
Tim Pengadaan<br>
{{ config('app.name') }}
@endcomponent
