<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — DPIS | Dthree Production Integration System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: 'Inter', system-ui, sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  -webkit-font-smoothing: antialiased;
  background: #222831;
  overflow: hidden;
  position: relative;
}

/* ── BACKGROUND ── */
.bg-layer {
  position: fixed;
  inset: 0;
  z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 15% 0%, rgba(0,173,181,.22) 0%, transparent 55%),
    radial-gradient(ellipse 60% 50% at 85% 100%, rgba(57,62,70,.8) 0%, transparent 55%),
    #222831;
}
.bg-layer::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px);
  background-size: 28px 28px;
}

/* ── FLOATING BLOBS ── */
.blob {
  position: fixed;
  border-radius: 50%;
  filter: blur(90px);
  pointer-events: none;
  z-index: 0;
  opacity: .3;
  animation: drift 14s ease-in-out infinite alternate;
}
.blob-1 { width: 520px; height: 520px; background: #00ADB5; top: -180px; left: -120px; animation-delay: 0s; }
.blob-2 { width: 380px; height: 380px; background: #393E46; bottom: -120px; right: -80px; animation-delay: -5s; }
.blob-3 { width: 240px; height: 240px; background: #00ADB5; top: 45%; right: 12%; animation-delay: -9s; }
@keyframes drift {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(18px, 28px) scale(1.06); }
}

/* ── CARD ── */
.card {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 420px;
  background: rgba(255,255,255,.97);
  backdrop-filter: blur(24px);
  border-radius: 20px;
  box-shadow:
    0 0 0 1px rgba(0,173,181,.1),
    0 32px 80px rgba(0,0,0,.45),
    0 8px 24px rgba(0,0,0,.2);
  overflow: hidden;
  animation: slideUp .4s cubic-bezier(.16,1,.3,1) both;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(24px) scale(.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

.card::before {
  content: '';
  display: block;
  height: 4px;
  background: linear-gradient(90deg, #222831, #00ADB5, #00d4de, #00ADB5, #222831);
}

/* ── CARD HEADER ── */
.card-header {
  padding: 1.8rem 2.25rem 0;
  text-align: center;
}

.brand-logo {
  display: block;
  width: 110px;
  height: auto;
  margin: 0 auto 1rem;
  filter: drop-shadow(0 4px 12px rgba(196,148,26,.2));
}

.header-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(0,173,181,.3), transparent);
  margin: .9rem 0 0;
}

.system-label {
  font-size: .72rem;
  font-weight: 700;
  letter-spacing: 2px;
  text-transform: uppercase;
  color: #94a3b8;
  margin-top: .75rem;
}

/* ── CARD BODY ── */
.card-body { padding: 1.5rem 2.25rem 2.25rem; }

/* Alert */
.alert-box {
  display: flex;
  align-items: center;
  gap: .5rem;
  padding: .65rem .9rem;
  border-radius: 10px;
  font-size: .82rem;
  margin-bottom: 1.25rem;
}
.alert-box.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-box.success { background: #fefce8; color: #92400e; border: 1px solid #fde68a; }

/* Field */
.field { margin-bottom: 1rem; }
.field label {
  display: block;
  font-size: .72rem;
  font-weight: 700;
  color: #64748b;
  letter-spacing: .5px;
  text-transform: uppercase;
  margin-bottom: .4rem;
}
.input-wrap { position: relative; }
.input-wrap .icon {
  position: absolute; left: .85rem; top: 50%;
  transform: translateY(-50%);
  color: #94a3b8; font-size: .9rem; pointer-events: none;
  transition: color .15s;
}
.input-wrap input {
  width: 100%;
  padding: .72rem .85rem .72rem 2.4rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: .875rem;
  font-family: inherit;
  color: #0f172a;
  background: #f8fafc;
  outline: none;
  transition: all .15s;
}
.input-wrap input:focus {
  background: #fff;
  border-color: #00ADB5;
  box-shadow: 0 0 0 3px rgba(0,173,181,.12);
}
.input-wrap input:focus + .icon { color: #00ADB5; }
.input-wrap input::placeholder { color: #cbd5e1; }

.pw-toggle {
  position: absolute; right: .8rem; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; font-size: .9rem; line-height: 1;
  transition: color .15s; padding: 2px;
}
.pw-toggle:hover { color: #00ADB5; }

/* Remember row */
.remember-row {
  display: flex;
  align-items: center;
  gap: .5rem;
  margin: 1.25rem 0 1.4rem;
}
.remember-row input[type="checkbox"] {
  width: 15px; height: 15px;
  accent-color: #00ADB5; cursor: pointer; flex-shrink: 0;
}
.remember-row label { font-size: .82rem; color: #64748b; cursor: pointer; user-select: none; }

/* Submit */
.btn-login {
  width: 100%;
  padding: .82rem;
  background: linear-gradient(135deg, #00ADB5 0%, #008a91 100%);
  border: none;
  border-radius: 10px;
  color: #fff;
  font-size: .9rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  letter-spacing: .3px;
  box-shadow: 0 4px 16px rgba(0,173,181,.35);
  transition: all .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
}
.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0,173,181,.45);
  background: linear-gradient(135deg, #00c2cb 0%, #00ADB5 100%);
}
.btn-login:active { transform: translateY(0); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .6rem;
  margin: 1.5rem 0 1rem;
  font-size: .68rem; font-weight: 700; letter-spacing: .6px;
  text-transform: uppercase; color: #cbd5e1;
}
.divider::before, .divider::after {
  content: ''; flex: 1; height: 1px; background: #e2e8f0;
}

/* Demo accounts */
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; }
.demo-btn {
  padding: .5rem .7rem;
  border-radius: 8px;
  border: 1.5px solid;
  background: transparent;
  cursor: pointer;
  text-align: left;
  font-family: inherit;
  transition: all .15s;
}
.demo-btn:hover { transform: translateY(-1px); }
.demo-btn.gold   { border-color: #fde68a; color: #92400e; background: #fffbeb; }
.demo-btn.amber  { border-color: #fed7aa; color: #9a3412; background: #fff7ed; }
.demo-btn.yellow { border-color: #fef08a; color: #713f12; background: #fefce8; }
.demo-btn.warm   { border-color: #fecaca; color: #991b1b; background: #fef2f2; }
.demo-btn:hover.gold   { background: #fef9c3; }
.demo-btn:hover.amber  { background: #ffedd5; }
.demo-btn:hover.yellow { background: #fef9c3; }
.demo-btn:hover.warm   { background: #fee2e2; }
.demo-email { display: block; font-size: .72rem; font-weight: 700; }
.demo-role  { display: block; font-size: .65rem; opacity: .7; margin-top: 1px; }
.demo-hint {
  text-align: center; margin-top: .7rem;
  font-size: .72rem; color: #94a3b8;
}
.demo-hint code {
  background: #f1f5f9; padding: .1em .4em; border-radius: 4px;
  color: #475569; font-family: ui-monospace, monospace;
}

@media (max-width: 480px) {
  .card-header { padding: 1.5rem 1.5rem 0; }
  .card-body { padding: 1.5rem; }
}
</style>
</head>
<body>

<div class="bg-layer"></div>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>

<div class="card">

  <div class="card-header">
    <img src="{{ asset('images/dthree-logo.png') }}" alt="DTHREE Logo" class="brand-logo">
    <div class="header-divider"></div>
    <div class="system-label">Dthree Production Integration System</div>
  </div>

  <div class="card-body">

    @if(session('success'))
    <div class="alert-box success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-box error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div class="field">
        <label>Email</label>
        <div class="input-wrap">
          <input type="email" name="email" value="{{ old('email','admin@dthree.id') }}"
                 placeholder="email@dthree.id" required autocomplete="email">
          <i class="bi bi-envelope icon"></i>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pwInput"
                 placeholder="Masukkan password" required autocomplete="current-password">
          <i class="bi bi-lock icon"></i>
          <button type="button" class="pw-toggle" onclick="togglePw()">
            <i class="bi bi-eye" id="pwIcon"></i>
          </button>
        </div>
      </div>

      <div class="remember-row">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Ingat saya di perangkat ini</label>
      </div>

      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right"></i>
        Masuk ke Sistem
      </button>
    </form>

    <div class="divider">Akun Demo</div>

    <div class="demo-grid">
      <button class="demo-btn gold"   onclick="fillDemo('admin@dthree.id')">
        <span class="demo-email">admin@dthree.id</span>
        <span class="demo-role">Admin</span>
      </button>
      <button class="demo-btn amber"  onclick="fillDemo('supervisor@dthree.id')">
        <span class="demo-email">supervisor@dthree.id</span>
        <span class="demo-role">Supervisor</span>
      </button>
      <button class="demo-btn yellow" onclick="fillDemo('manager@dthree.id')">
        <span class="demo-email">manager@dthree.id</span>
        <span class="demo-role">Manager</span>
      </button>
      <button class="demo-btn warm"   onclick="fillDemo('cutting@dthree.id')">
        <span class="demo-email">cutting@dthree.id</span>
        <span class="demo-role">Staff Cutting</span>
      </button>
    </div>
    <div class="demo-hint">Password: <code>password</code></div>

  </div>
</div>

<script>
function fillDemo(email) {
  document.querySelector('input[name="email"]').value = email;
  document.querySelector('#pwInput').value = 'password';
}
function togglePw() {
  const inp = document.getElementById('pwInput');
  const icon = document.getElementById('pwIcon');
  if (inp.type === 'password') { inp.type = 'text'; icon.className = 'bi bi-eye-slash'; }
  else { inp.type = 'password'; icon.className = 'bi bi-eye'; }
}
</script>
</body>
</html>
