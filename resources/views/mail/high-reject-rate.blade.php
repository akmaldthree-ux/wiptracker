@component('mail::message')
# Alert: Tingkat Reject Melebihi Batas

Tingkat reject pada bulan **{{ $month }}** telah melebihi batas yang ditetapkan.

@component('mail::panel')
**Tingkat Reject Bulan Ini: {{ number_format($rejectRate, 2) }}%**
Batas maksimum yang diizinkan: **5%**
@endcomponent

Segera lakukan investigasi dan tindakan perbaikan untuk menurunkan tingkat reject.

@component('mail::button', ['url' => url('/dashboard/reject'), 'color' => 'error'])
Lihat Dashboard Reject
@endcomponent

Salam,<br>
{{ config('app.name') }}
@endcomponent
