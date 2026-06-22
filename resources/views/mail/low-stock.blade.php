@component('mail::message')
# ⚠ Peringatan Stok Kritis Bahan Baku

Terdapat **{{ $materials->count() }} bahan baku** yang stoknya di bawah batas minimum dan perlu segera dilakukan pembelian.

@component('mail::table')
| Kode | Nama Bahan | Stok Saat Ini | Stok Minimum | Kekurangan |
|:-----|:-----------|:-------------:|:------------:|:----------:|
@foreach($materials as $m)
| {{ $m->code }} | {{ $m->name }} | {{ number_format($m->current_stock) }} {{ $m->unit }} | {{ number_format($m->min_stock) }} {{ $m->unit }} | 🔴 {{ number_format($m->min_stock - $m->current_stock) }} {{ $m->unit }} |
@endforeach
@endcomponent

Segera buat Purchase Order untuk bahan baku di atas agar proses produksi tidak terganggu.

@component('mail::button', ['url' => url('/bahan-baku?low_stock=1'), 'color' => 'red'])
Lihat Stok Kritis
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
