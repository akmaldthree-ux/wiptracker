<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Reject</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 20px; }
  h1 { font-size: 16px; color: #ef4444; margin-bottom: 4px; }
  .meta { font-size: 10px; color: #666; margin-bottom: 12px; }
  .kpi-row { display: flex; gap: 16px; margin-bottom: 16px; }
  .kpi { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 14px; }
  .kpi .val { font-size: 18px; font-weight: bold; color: #ef4444; }
  .kpi .lbl { font-size: 9px; color: #666; }
  table { width: 100%; border-collapse: collapse; }
  thead th { background: #ef4444; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tbody tr:nth-child(even) { background: #fef9f9; }
  .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: right; }
</style>
</head>
<body>
<h1>Laporan Reject & Kualitas — DPIS</h1>
<div class="meta">Periode: {{ $month }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>

<table>
  <thead>
    <tr><th>#</th><th>Tanggal</th><th>Handover</th><th>Order</th><th>Stasiun</th><th>SKU</th><th>Qty Reject</th><th>Tipe</th><th>Alasan</th></tr>
  </thead>
  <tbody>
    @forelse($recentRejects as $i => $item)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $item->handover?->confirmed_at?->format('d/m/Y') ?? '-' }}</td>
      <td>{{ $item->handover?->handover_no ?? '-' }}</td>
      <td>{{ $item->handover?->order?->order_no ?? '-' }}</td>
      <td>{{ $item->handover?->toStation?->name ?? '-' }}</td>
      <td>{{ $item->sku?->sku_code ?? '-' }}</td>
      <td><strong>{{ $item->qty_reject }}</strong></td>
      <td>{{ strtoupper($item->reject_type ?? '-') }}</td>
      <td>{{ $item->reject_notes ?? '-' }}</td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;padding:20px;color:#999">Tidak ada data reject</td></tr>
    @endforelse
  </tbody>
</table>
<div class="footer">DPIS &copy; {{ date('Y') }} — Dthree Production Integration System</div>
</body>
</html>
