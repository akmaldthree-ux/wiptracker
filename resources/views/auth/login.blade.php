<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — DPIS</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --primary: #2563eb;
  --primary-dark: #1d4ed8;
  --primary-darker: #1e3a8a;
  --bg-dark: #0f172a;
  --radius: 12px;
}

body {
  font-family: 'Inter', system-ui, sans-serif;
  background: var(--bg-dark);
  min-height: 100vh;
  display: flex;
  align-items: stretch;
  -webkit-font-smoothing: antialiased;
}

/* ── LEFT PANEL ── */
.left-panel {
  flex: 1;
  background: linear-gradient(145deg, #0f172a 0%, #1e3a8a 50%, #1d4ed8 100%);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 3rem;
  position: relative;
  overflow: hidden;
  min-height: 100vh;
}

/* Geometric decoration */
.left-panel::before {
  content: '';
  position: absolute;
  top: -80px; right: -80px;
  width: 400px; height: 400px;
  border-radius: 50%;
  background: rgba(255,255,255,.04);
  pointer-events: none;
}
.left-panel::after {
  content: '';
  position: absolute;
  bottom: -120px; left: -60px;
  width: 500px; height: 500px;
  border-radius: 50%;
  background: rgba(255,255,255,.03);
  pointer-events: none;
}

.geo-ring {
  position: absolute;
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 50%;
  pointer-events: none;
}
.geo-ring-1 { width: 320px; height: 320px; top: 15%; right: -100px; }
.geo-ring-2 { width: 180px; height: 180px; bottom: 25%; left: -40px; }
.geo-ring-3 { width: 80px; height: 80px; top: 45%; right: 80px; }

.left-brand { position: relative; z-index: 1; }
.brand-badge {
  display: inline-flex; align-items: center; gap: .5rem;
  background: rgba(255,255,255,.1);
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 50px;
  padding: .35rem .9rem;
  margin-bottom: 2rem;
  font-size: .72rem; font-weight: 600; color: rgba(255,255,255,.8);
  letter-spacing: .5px; text-transform: uppercase;
}
.brand-badge i { font-size: .8rem; color: #60a5fa; }

.left-brand h1 {
  font-size: 3rem; font-weight: 800; color: #fff;
  line-height: 1.1; letter-spacing: -.5px; margin-bottom: .75rem;
}
.left-brand h1 span { color: #60a5fa; }
.left-brand p { color: rgba(255,255,255,.5); font-size: .9rem; line-height: 1.6; max-width: 340px; }

.left-features { position: relative; z-index: 1; }
.feature-item {
  display: flex; align-items: center; gap: .75rem;
  margin-bottom: .85rem;
}
.feature-icon {
  width: 34px; height: 34px; flex-shrink: 0;
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: .9rem; color: #93c5fd;
}
.feature-text { color: rgba(255,255,255,.65); font-size: .82rem; font-weight: 500; }

.left-footer {
  position: relative; z-index: 1;
  color: rgba(255,255,255,.25); font-size: .72rem;
}

/* ── RIGHT PANEL ── */
.right-panel {
  width: 460px;
  flex-shrink: 0;
  background: #f8fafc;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2.5rem 3rem;
  min-height: 100vh;
}

.login-box { width: 100%; max-width: 360px; }

.login-box .login-title {
  font-size: 1.5rem; font-weight: 800; color: #0f172a;
  margin-bottom: .35rem;
}
.login-box .login-sub {
  color: #64748b; font-size: .875rem; margin-bottom: 2rem;
}

/* Form */
.field-group { margin-bottom: 1.1rem; }
.field-label {
  display: block; font-size: .75rem; font-weight: 700;
  color: #475569; letter-spacing: .5px; text-transform: uppercase;
  margin-bottom: .45rem;
}
.input-wrap {
  position: relative;
}
.input-wrap i {
  position: absolute; left: .9rem; top: 50%; transform: translateY(-50%);
  color: #94a3b8; font-size: .9rem; pointer-events: none;
}
.input-wrap input {
  width: 100%;
  padding: .75rem .9rem .75rem 2.5rem;
  border: 1.5px solid #e2e8f0;
  border-radius: var(--radius);
  font-size: .9rem; font-family: inherit;
  background: #fff; color: #0f172a;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
}
.input-wrap input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.input-wrap input::placeholder { color: #cbd5e1; }

/* Password toggle */
.pw-toggle {
  position: absolute; right: .9rem; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; font-size: .9rem; padding: 0; line-height: 1;
  transition: color .15s;
}
.pw-toggle:hover { color: var(--primary); }

/* Remember */
.remember-row {
  display: flex; align-items: center; gap: .5rem;
  margin-bottom: 1.5rem;
}
.remember-row input[type="checkbox"] {
  width: 16px; height: 16px; accent-color: var(--primary); cursor: pointer;
}
.remember-row label {
  font-size: .82rem; color: #64748b; cursor: pointer; user-select: none;
}

/* Submit button */
.btn-submit {
  width: 100%;
  padding: .8rem;
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  border: none; border-radius: var(--radius);
  color: #fff; font-size: .9rem; font-weight: 700;
  font-family: inherit; letter-spacing: .3px;
  cursor: pointer; transition: all .2s;
  box-shadow: 0 4px 14px rgba(37,99,235,.3);
  display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-submit:hover {
  background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-darker) 100%);
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(37,99,235,.4);
}
.btn-submit:active { transform: translateY(0); box-shadow: 0 2px 8px rgba(37,99,235,.3); }

/* Alert */
.alert-box {
  padding: .7rem 1rem; border-radius: var(--radius);
  font-size: .82rem; display: flex; align-items: center; gap: .5rem;
  margin-bottom: 1.25rem;
}
.alert-box.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-box.success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .75rem;
  margin: 1.5rem 0;
  color: #cbd5e1; font-size: .75rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px;
}
.divider::before, .divider::after {
  content: ''; flex: 1; height: 1px; background: #e2e8f0;
}

/* Demo accounts */
.demo-section {}
.demo-label { font-size: .72rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .5px; margin-bottom: .6rem; }
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; }
.demo-account {
  padding: .55rem .7rem;
  border-radius: 8px;
  border: 1.5px solid transparent;
  cursor: pointer; transition: all .15s;
  background: #fff;
  text-align: left;
}
.demo-account:hover { transform: translateY(-1px); }
.demo-account.blue   { border-color: #bfdbfe; background: #eff6ff; }
.demo-account.green  { border-color: #bbf7d0; background: #f0fdf4; }
.demo-account.cyan   { border-color: #a5f3fc; background: #ecfeff; }
.demo-account.amber  { border-color: #fde68a; background: #fffbeb; }
.demo-account:hover.blue  { background: #dbeafe; }
.demo-account:hover.green { background: #dcfce7; }
.demo-account:hover.cyan  { background: #cffafe; }
.demo-account:hover.amber { background: #fef9c3; }
.demo-account .da-email { font-size: .73rem; font-weight: 700; color: #1e293b; display: block; }
.demo-account .da-role  { font-size: .65rem; color: #64748b; display: block; margin-top: 1px; }
.demo-pw { font-size: .73rem; color: #94a3b8; margin-top: .6rem; text-align: center; }
.demo-pw code { background: #f1f5f9; padding: .15em .4em; border-radius: 4px; color: #475569; font-family: ui-monospace, monospace; }

/* ── MOBILE ── */
@media (max-width: 768px) {
  body { flex-direction: column; }
  .left-panel {
    min-height: auto;
    padding: 2rem 1.5rem;
  }
  .left-brand h1 { font-size: 2rem; }
  .left-features { display: none; }
  .left-footer { display: none; }
  .geo-ring { display: none; }
  .right-panel {
    width: 100%;
    padding: 2rem 1.5rem;
    min-height: auto;
  }
}

/* ── ENTRY ANIMATION ── */
@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}
.login-box { animation: slideUp .35s ease both; }
.left-brand { animation: slideUp .3s ease both; }
.left-features { animation: slideUp .35s .08s ease both; }
</style>
</head>
<body>

<!-- Left Brand Panel -->
<div class="left-panel">
  <div class="geo-ring geo-ring-1"></div>
  <div class="geo-ring geo-ring-2"></div>
  <div class="geo-ring geo-ring-3"></div>

  <div class="left-brand">
    <div class="brand-badge">
      <i class="bi bi-factory"></i>
      PT DTHREE SUKSES MULIA
    </div>
    <h1>DPIS<br><span>Production</span><br>System</h1>
    <p>Platform terintegrasi untuk monitoring WIP, handover digital, dan manajemen produksi garmen secara real-time.</p>
  </div>

  <div class="left-features">
    <div class="feature-item">
      <div class="feature-icon"><i class="bi bi-activity"></i></div>
      <span class="feature-text">Real-time WIP tracking antar stasiun</span>
    </div>
    <div class="feature-item">
      <div class="feature-icon"><i class="bi bi-arrow-left-right"></i></div>
      <span class="feature-text">Handover digital dengan validasi qty</span>
    </div>
    <div class="feature-item">
      <div class="feature-icon"><i class="bi bi-speedometer2"></i></div>
      <span class="feature-text">Dashboard eksekutif & operasional</span>
    </div>
    <div class="feature-item">
      <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
      <span class="feature-text">Kontrol akses berbasis peran</span>
    </div>
  </div>

  <div class="left-footer">
    &copy; {{ date('Y') }} PT DTHREE SUKSES MULIA. All rights reserved.
  </div>
</div>

<!-- Right Login Panel -->
<div class="right-panel">
  <div class="login-box">

    <div class="login-title">Selamat datang</div>
    <div class="login-sub">Masuk ke akun Anda untuk melanjutkan</div>

    @if(session('success'))
    <div class="alert-box success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-box error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="field-group">
        <label class="field-label">Email</label>
        <div class="input-wrap">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" value="{{ old('email','admin@dpis.com') }}" placeholder="email@perusahaan.com" required autocomplete="email">
        </div>
      </div>

      <div class="field-group">
        <label class="field-label">Password</label>
        <div class="input-wrap">
          <i class="bi bi-lock"></i>
          <input type="password" name="password" id="pwInput" placeholder="Masukkan password" required autocomplete="current-password">
          <button type="button" class="pw-toggle" onclick="togglePw()" id="pwToggle">
            <i class="bi bi-eye" id="pwEyeIcon"></i>
          </button>
        </div>
      </div>

      <div class="remember-row">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Ingat saya di perangkat ini</label>
      </div>

      <button type="submit" class="btn-submit">
        <i class="bi bi-box-arrow-in-right"></i>
        Masuk ke Sistem
      </button>
    </form>

    <div class="divider">Akun Demo</div>

    <div class="demo-section">
      <div class="demo-grid">
        <button type="button" class="demo-account blue" onclick="fillDemo('admin@dpis.com')">
          <span class="da-email">admin@dpis.com</span>
          <span class="da-role">Admin</span>
        </button>
        <button type="button" class="demo-account green" onclick="fillDemo('supervisor@dpis.com')">
          <span class="da-email">supervisor@dpis.com</span>
          <span class="da-role">Supervisor</span>
        </button>
        <button type="button" class="demo-account cyan" onclick="fillDemo('cutting@dpis.com')">
          <span class="da-email">cutting@dpis.com</span>
          <span class="da-role">PIC Cutting</span>
        </button>
        <button type="button" class="demo-account amber" onclick="fillDemo('manager@dpis.com')">
          <span class="da-email">manager@dpis.com</span>
          <span class="da-role">Manager</span>
        </button>
      </div>
      <div class="demo-pw">Password: <code>password123</code></div>
    </div>

  </div>
</div>

<script>
function fillDemo(email) {
  document.querySelector('input[name="email"]').value = email;
  document.querySelector('input[name="password"]').value = 'password123';
  document.querySelector('input[name="email"]').focus();
}
function togglePw() {
  const inp = document.getElementById('pwInput');
  const icon = document.getElementById('pwEyeIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    inp.type = 'password';
    icon.className = 'bi bi-eye';
  }
}
</script>
</body>
</html>
