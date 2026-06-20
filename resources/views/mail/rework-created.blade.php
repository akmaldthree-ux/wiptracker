<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;font-size:14px;color:#222;background:#f5f5f5;margin:0;padding:20px}
.card{background:#fff;border-radius:8px;padding:24px;max-width:560px;margin:0 auto;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.header{background:#222831;color:#fff;border-radius:6px 6px 0 0;padding:16px 24px;margin:-24px -24px 20px}
.logo{color:#00ADB5;font-weight:bold;font-size:18px}
.badge{display:inline-block;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:4px;font-size:12px;font-weight:bold}
.footer{font-size:11px;color:#999;margin-top:20px;text-align:center}
</style></head>
<body>
<div class="card">
  <div class="header"><span class="logo">DPIS</span> — Dthree Production Integration System</div>
  <p>Halo,</p>
  <p>Sebuah <strong>Rework Handover</strong> telah dibuat dan dikirimkan kembali ke stasiun Anda untuk perbaikan:</p>
  <table style="width:100%;border-collapse:collapse;margin:12px 0">
    <tr><td style="padding:6px;color:#666;width:45%">No. Rework Handover</td><td style="padding:6px;font-weight:bold">{{ $reworkHandover->handover_no }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Sumber Handover</td><td style="padding:6px">{{ $sourceHandover->handover_no }}</td></tr>
    <tr><td style="padding:6px;color:#666">No. Order</td><td style="padding:6px">{{ $reworkHandover->order?->order_no ?? '-' }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Dikirim ke</td><td style="padding:6px">{{ $reworkHandover->toStation?->name ?? '-' }}</td></tr>
    <tr><td style="padding:6px;color:#666">Dibuat pada</td><td style="padding:6px">{{ $reworkHandover->created_at->format('d M Y H:i') }}</td></tr>
  </table>
  <p><span class="badge">Tindakan Diperlukan</span></p>
  <p>Mohon segera proses ulang item reject dan kirimkan kembali setelah selesai diperbaiki.</p>
  <div class="footer">Email ini dikirim otomatis oleh sistem DPIS. Jangan balas email ini.</div>
</div>
</body></html>
