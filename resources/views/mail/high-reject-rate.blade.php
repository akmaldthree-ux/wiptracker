<!DOCTYPE html>
<html><head><meta charset="UTF-8"><style>
body{font-family:Arial,sans-serif;font-size:14px;color:#222;background:#f5f5f5;margin:0;padding:20px}
.card{background:#fff;border-radius:8px;padding:24px;max-width:560px;margin:0 auto;box-shadow:0 2px 8px rgba(0,0,0,.08)}
.header{background:#222831;color:#fff;border-radius:6px 6px 0 0;padding:16px 24px;margin:-24px -24px 20px}
.logo{color:#00ADB5;font-weight:bold;font-size:18px}
.alert{background:#fee2e2;border-left:4px solid #ef4444;padding:12px 16px;border-radius:4px;margin:16px 0}
.big-rate{font-size:40px;font-weight:bold;color:#ef4444;text-align:center;margin:16px 0}
.footer{font-size:11px;color:#999;margin-top:20px;text-align:center}
</style></head>
<body>
<div class="card">
  <div class="header"><span class="logo">DPIS</span> — Dthree Production Integration System</div>
  <div class="alert"><strong>Peringatan Kualitas!</strong> Reject rate bulan ini melebihi batas aman (5%).</div>
  <div class="big-rate">{{ $rejectRate }}%</div>
  <table style="width:100%;border-collapse:collapse;margin:12px 0">
    <tr><td style="padding:6px;color:#666;width:50%">Periode</td><td style="padding:6px;font-weight:bold">{{ $month }}</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Total Reject</td><td style="padding:6px;font-weight:bold;color:#ef4444">{{ number_format($totalReject) }} pcs</td></tr>
    <tr><td style="padding:6px;color:#666">Reject Rate</td><td style="padding:6px;font-weight:bold;color:#ef4444">{{ $rejectRate }}%</td></tr>
    <tr style="background:#f9fafb"><td style="padding:6px;color:#666">Batas Aman</td><td style="padding:6px">5%</td></tr>
  </table>
  <p>Mohon segera lakukan evaluasi dan tindakan korektif untuk menekan angka reject.</p>
  <div class="footer">Email ini dikirim otomatis oleh sistem DPIS. Jangan balas email ini.</div>
</div>
</body></html>
