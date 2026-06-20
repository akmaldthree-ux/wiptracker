<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;font-size:14px;color:#222;background:#f5f5f5;margin:0;padding:20px}
.card{background:#fff;border-radius:8px;padding:24px;max-width:560px;margin:0 auto;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.header{background:#222831;color:#fff;border-radius:6px 6px 0 0;padding:16px 24px;margin:-24px -24px 20px}
.logo{color:#00ADB5;font-weight:bold;font-size:18px}
.badge{display:inline-block;background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:4px;font-size:12px;font-weight:bold}
.btn{display:inline-block;background:#00ADB5;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-weight:bold;margin-top:16px}
.footer{font-size:11px;color:#999;margin-top:20px;text-align:center}
</style></head>
<body>
<div class="card">
  <div class="header"><span class="logo">DPIS</span> — Dthree Production Integration System</div>
  <p>Halo,</p>
  <p>Terdapat handover yang sudah <strong>pending lebih dari 24 jam</strong> dan belum dikonfirmasi:</p>
  <table style="width:100%;border-collapse:collapse;margin:12px 0">
    <tr><td style="padding:6px;color:#666;width:40%">No. Handover</td><td style="padding:6px;font-weight:bold">{{ $handover->handover_no }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">No. Order</td><td style="padding:6px">{{ $handover->order?->order_no ?? '-' }}</td></tr>
    <tr><td style="padding:6px;color:#666">Dari Stasiun</td><td style="padding:6px">{{ $handover->fromStation?->name ?? '-' }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Ke Stasiun</td><td style="padding:6px">{{ $handover->toStation?->name ?? '-' }}</td></tr>
    <tr><td style="padding:6px;color:#666">Dikirim oleh</td><td style="padding:6px">{{ $handover->initiatedBy?->name ?? '-' }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Waktu Pengiriman</td><td style="padding:6px">{{ $handover->initiated_at?->format('d M Y H:i') ?? '-' }}</td></tr>
  </table>
  <p><span class="badge">Tindakan Diperlukan</span></p>
  <p>Mohon segera konfirmasi penerimaan handover ini di sistem DPIS.</p>
  <div class="footer">Email ini dikirim otomatis oleh sistem DPIS. Jangan balas email ini.</div>
</div>
</body></html>
