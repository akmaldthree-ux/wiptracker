@component('mail::message')
# Barang Rework Masuk — Perlu Ditindaklanjuti

Halo,

Ada barang rework yang dikirim ke stasiun Anda dan memerlukan perbaikan sebelum dilanjutkan ke proses berikutnya.

---

@component('mail::panel')
**No. Handover Rework:** {{ $rework->handover_no }}

**Order Produksi:** {{ $rework->order->order_no }} — {{ $rework->order->product->name }}

**Dari Stasiun:** {{ $rework->fromStation?->name ?? '-' }}

**Ke Stasiun (Anda):** {{ $rework->toStation->name }}

**Rujukan Handover Asal:** {{ $rework->parentHandover?->handover_no ?? '-' }}

**Total Qty Rework:** {{ $rework->items->sum('qty_sent') }} pcs
@endcomponent

Setelah selesai diperbaiki, buat handover baru untuk mengirim kembali ke stasiun selanjutnya.

@component('mail::button', ['url' => url('/handover/'.$rework->id), 'color' => 'primary'])
Lihat Detail Rework
@endcomponent

Terima kasih,<br>
**DPIS — DTHREE Production Integration System**
@endcomponent
