@component('mail::message')
# 🚨 QC Inspeksi Gagal — Tindakan Diperlukan

Halo **{{ $recipient->name }}**,

Hasil inspeksi QC untuk order **{{ $inspection->order->order_no }}** menunjukkan hasil **GAGAL** dan memerlukan tindak lanjut segera.

@component('mail::panel')
**Order:** {{ $inspection->order->order_no }} — {{ $inspection->order->product->name }}

**Inspektor:** {{ $inspection->inspector->name }}

**Waktu Inspeksi:** {{ $inspection->inspected_at->format('d M Y, H:i') }} WIB

**Total Diperiksa:** {{ number_format($inspection->total_checked) }} pcs

**Total Defect:** {{ number_format($inspection->total_defect) }} pcs

**Defect Rate:** 🔴 {{ $inspection->defect_rate }}% *(batas maks: 5%)*

@if($inspection->notes)
**Catatan Inspektor:** {{ $inspection->notes }}
@endif
@endcomponent

@if($inspection->handover)
Inspeksi ini terkait dengan Handover **{{ $inspection->handover->handover_no }}** dari stasiun **{{ $inspection->handover->fromStation?->name }}**.
@endif

Segera koordinasikan penanganan barang defect dengan tim produksi.

@component('mail::button', ['url' => url("/qc/{$inspection->id}"), 'color' => 'red'])
Lihat Detail Inspeksi QC
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
