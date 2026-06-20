<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Handover</title>
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 20px; }
  h1 { font-size: 16px; color: #00ADB5; margin-bottom: 4px; }
  .meta { font-size: 10px; color: #666; margin-bottom: 16px; }
  table { width: 100%; border-collapse: collapse; }
  thead th { background: #00ADB5; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #eee; }
  tbody tr:nth-child(even) { background: #f9fafb; }
  .badge { display: inline-block; padding: 2px 7px; border-radius: 4px; font-size: 9px; font-weight: bold; }
  .footer { margin-top: 20px; font-size: 9px; color: #999; text-align: right; }
</style>
</head>
<body>
<h1>Laporan Handover — DPIS</h1>
<div class="meta">Periode: {{ $dateFrom }} s/d {{ $dateTo }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>No. Handover</th>
      <th>No. Order</th>
      <th>Dari Stasiun</th>
      <th>Ke Stasiun</th>
      <th>Tanggal</th>
      <th>Qty Kirim</th>
      <th>Qty Terima</th>
      <th>Reject</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($handovers as $i => $h)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td><strong>{{ $h->handover_no }}</strong></td>
      <td>{{ $h->order?->order_no ?? '-' }}</td>
      <td>{{ $h->fromStation?->name ?? '-' }}</td>
      <td>{{ $h->toStation?->name ?? '-' }}</td>
      <td>{{ $h->initiated_at?->format('d/m/Y') ?? '-' }}</td>
      <td>{{ number_format($h->items->sum('qty_sent')) }}</td>
      <td>{{ number_format($h->items->sum('qty_received')) }}</td>
      <td>{{ number_format($h->items->sum('qty_reject')) }}</td>
      <td>{{ strtoupper($h->status) }}</td>
    </tr>
    @empty
    <tr><td colspan="10" style="text-align:center;padding:20px;color:#999">Tidak ada data</td></tr>
    @endforelse
  </tbody>
</table>
<div class="footer">DPIS &copy; {{ date('Y') }} — Dthree Production Integration System</div>
</body>
</html>
