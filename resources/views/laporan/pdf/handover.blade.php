<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Handover</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 20px; }
h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
.subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead th { background: #0d6efd; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; }
tbody td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
tbody tr:nth-child(even) { background: #f8f9fa; }
</style>
</head>
<body>
<h1>Laporan Handover</h1>
<div class="subtitle">Periode: {{ $dateFrom }} s/d {{ $dateTo }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}</div>
<table>
    <thead>
        <tr>
            <th>No. Handover</th>
            <th>No. Order</th>
            <th>Dari → Ke</th>
            <th>Tgl Handover</th>
            <th>Dikirim</th>
            <th>Diterima</th>
            <th>Selisih</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($handovers as $h)
        <tr>
            <td>{{ $h->handover_number }}</td>
            <td>{{ $h->order->order_number ?? '-' }}</td>
            <td>{{ $h->fromStation->name ?? '-' }} → {{ $h->toStation->name ?? '-' }}</td>
            <td>{{ $h->handover_date ? \Carbon\Carbon::parse($h->handover_date)->format('d M Y') : '-' }}</td>
            <td>{{ number_format($h->getTotalSentAttribute()) }}</td>
            <td>{{ number_format($h->getTotalReceivedAttribute()) }}</td>
            <td>{{ $h->getTotalDiscrepancyAttribute() }}</td>
            <td>{{ $h->getStatusLabelAttribute() }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
