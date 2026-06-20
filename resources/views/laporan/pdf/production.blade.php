<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Produksi</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 20px; }
h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
.subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead th { background: #0d6efd; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; }
tbody td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
tbody tr:nth-child(even) { background: #f8f9fa; }
.badge-active { color: #1d4ed8; } .badge-completed { color: #065f46; } .badge-draft { color: #475569; }
</style>
</head>
<body>
<h1>Laporan Produksi</h1>
<div class="subtitle">Periode: {{ $dateFrom }} s/d {{ $dateTo }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}</div>
<table>
    <thead>
        <tr>
            <th>No. Order</th>
            <th>Produk</th>
            <th>Series</th>
            <th>Target Qty</th>
            <th>Progress</th>
            <th>Deadline</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $order)
        <tr>
            <td>{{ $order->order_no }}</td>
            <td>{{ $order->product->name ?? '-' }}</td>
            <td>{{ $order->series->name ?? '-' }}</td>
            <td>{{ number_format($order->getTotalTargetQty()) }}</td>
            <td>{{ $order->getProgressPercentage() }}%</td>
            <td>{{ $order->target_date ? \Carbon\Carbon::parse($order->target_date)->format('d M Y') : '-' }}</td>
            <td>{{ $order->getStatusLabelAttribute() }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
