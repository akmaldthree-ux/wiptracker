@component('mail::message')
# Review Kebutuhan Bahan Baku

Halo **{{ $recipient->name }}**,

Order produksi baru telah diaktifkan dan membutuhkan review ketersediaan bahan baku sebelum dapat dikirim ke stasiun Cutting.

---

@component('mail::panel')
**No. Order:** {{ $order->order_no }}

**Produk:** {{ $order->product->name }} — {{ optional($order->series)->name }}

**Total Qty:** {{ number_format($order->getTotalTargetQty()) }} pcs

**Target Selesai:** {{ $order->target_date->format('d M Y') }}
@endcomponent

Silakan buka halaman Procurement untuk mengecek ketersediaan stok dan menyetujui kebutuhan bahan.

@component('mail::button', ['url' => url('/procurement/'.$order->id), 'color' => 'primary'])
Review Kebutuhan Bahan
@endcomponent

Terima kasih,<br>
**DPIS — DTHREE Production Integration System**
@endcomponent
