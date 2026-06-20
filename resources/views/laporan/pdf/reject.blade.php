<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Reject</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 20px; }
h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
.subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
thead th { background: #dc3545; color: #fff; padding: 7px 10px; text-align: left; font-size: 11px; }
tbody td { padding: 6px 10px; border-bottom: 1px solid #eee; font-size: 11px; }
tbody tr:nth-child(even) { background: #f8f9fa; }
</style>
</head>
<body>
<h1>Laporan Reject</h1>
<div class="subtitle">Dicetak: {{ now()->format('d M Y H:i') }}</div>
<table>
    <thead>
        <tr>
            <th>No. Handover</th>
            <th>No. Order</th>
            <th>SKU</th>
            <th>Qty Reject</th>
            <th>Tipe</th>
            <th>Tanggal</th>
            <th>Alasan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rejectItems as $item)
        <tr>
            <td>{{ $item->handover->handover_number ?? '-' }}</td>
            <td>{{ $item->handover->order->order_number ?? '-' }}</td>
            <td>{{ $item->sku->sku_code ?? '-' }}</td>
            <td>{{ number_format($item->qty_reject) }}</td>
            <td>{{ $item->reject_type ?? '-' }}</td>
            <td>{{ $item->handover->confirmed_at ? \Carbon\Carbon::parse($item->handover->confirmed_at)->format('d M Y') : '-' }}</td>
            <td>{{ $item->reject_notes ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
