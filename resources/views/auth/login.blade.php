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

body {
  font-family: 'Inter', system-ui, sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  -webkit-font-smoothing: antialiased;
  background: #0f172a;
  overflow: hidden;
  position: relative;
}

/* ── BACKGROUND ── */
.bg-layer {
  position: fixed;
  inset: 0;
  z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 20% 0%, rgba(37,99,235,.35) 0%, transparent 60%),
    radial-gradient(ellipse 60% 50% at 80% 100%, rgba(99,102,241,.2) 0%, transparent 55%),
    #0f172a;
}
/* Subtle dot grid */
.bg-layer::after {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.06) 1px, transparent 1px);
  background-size: 28px 28px;
}

/* ── FLOATING BLOBS ── */
.blob {
  position: fixed;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
  opacity: .35;
  animation: drift 12s ease-in-out infinite alternate;
}
.blob-1 { width: 500px; height: 500px; background: #3b82f6; top: -150px; left: -100px; animation-delay: 0s; }
.blob-2 { width: 400px; height: 400px; background: #6366f1; bottom: -100px; right: -80px; animation-delay: -4s; }
.blob-3 { width: 250px; height: 250px; background: #0ea5e9; top: 40%; right: 15%; animation-delay: -8s; }
@keyframes drift {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(20px, 30px) scale(1.05); }
}

/* ── CARD ── */
.card {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 420px;
  background: rgba(255,255,255,.97);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  box-shadow:
    0 0 0 1px rgba(255,255,255,.1),
    0 30px 80px rgba(0,0,0,.4),
    0 8px 24px rgba(0,0,0,.2);
  overflow: hidden;
  animation: slideUp .4s cubic-bezier(.16,1,.3,1) both;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(24px) scale(.97); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}

/* Card top accent */
.card::before {
  content: '';
  display: block;
  height: 4px;
  background: linear-gradient(90deg, #2563eb, #6366f1, #0ea5e9);
}

/* ── CARD HEADER ── */
.card-header {
  padding: 2rem 2.25rem 0;
  text-align: center;
}

.brand-mark {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 56px; height: 56px;
  background: linear-gradient(135deg, #2563eb, #6366f1);
  border-radius: 16px;
  font-size: 1.5rem;
  color: #fff;
  margin-bottom: 1.1rem;
  box-shadow: 0 8px 24px rgba(37,99,235,.35);
}

.card-header h1 {
  font-size: 1.35rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -.3px;
  margin-bottom: .2rem;
}
.card-header p {
  font-size: .8rem;
  color: #94a3b8;
  margin-bottom: 0;
}

/* ── CARD BODY ── */
.card-body { padding: 1.75rem 2.25rem 2.25rem; }

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
.alert-box.success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* Field */
.field { margin-bottom: 1rem; }
.field label {
  display: block;
  font-size: .75rem;
  font-weight: 700;
  color: #475569;
  letter-spacing: .4px;
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
  border-color: #2563eb;
  box-shadow: 0 0 0 3px rgba(37,99,235,.12);
}
.input-wrap input:focus ~ .icon,
.input-wrap input:focus + .icon { color: #2563eb; }
.input-wrap input::placeholder { color: #cbd5e1; }

.pw-toggle {
  position: absolute; right: .8rem; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; font-size: .9rem; line-height: 1;
  transition: color .15s; padding: 2px;
}
.pw-toggle:hover { color: #2563eb; }

/* Remember row */
.remember-row {
  display: flex;
  align-items: center;
  gap: .5rem;
  margin: 1.25rem 0 1.4rem;
}
.remember-row input[type="checkbox"] {
  width: 15px; height: 15px;
  accent-color: #2563eb; cursor: pointer; flex-shrink: 0;
}
.remember-row label { font-size: .82rem; color: #64748b; cursor: pointer; user-select: none; }

/* Submit */
.btn-login {
  width: 100%;
  padding: .8rem;
  background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
  border: none;
  border-radius: 10px;
  color: #fff;
  font-size: .9rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  letter-spacing: .2px;
  box-shadow: 0 4px 16px rgba(37,99,235,.35);
  transition: all .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
}
.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(37,99,235,.45);
  background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%);
}
.btn-login:active { transform: translateY(0); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .6rem;
  margin: 1.5rem 0 1rem;
  font-size: .7rem; font-weight: 700; letter-spacing: .5px;
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
.demo-btn.blue   { border-color: #bfdbfe; color: #1d4ed8; background: #eff6ff; }
.demo-btn.green  { border-color: #bbf7d0; color: #15803d; background: #f0fdf4; }
.demo-btn.cyan   { border-color: #a5f3fc; color: #0e7490; background: #ecfeff; }
.demo-btn.amber  { border-color: #fde68a; color: #92400e; background: #fffbeb; }
.demo-btn:hover.blue  { background: #dbeafe; }
.demo-btn:hover.green { background: #dcfce7; }
.demo-btn:hover.cyan  { background: #cffafe; }
.demo-btn:hover.amber { background: #fef9c3; }
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

/* Mobile */
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
  <!-- Color accent strip is via ::before -->

  <div class="card-header">
    <div class="brand-mark"><i class="bi bi-factory"></i></div>
    <h1>DPIS</h1>
    <p>DTHREE Production Integration System</p>
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
          <i class="bi bi-envelope icon"></i>
          <input type="email" name="email" value="{{ old('email','admin@dpis.com') }}"
                 placeholder="email@perusahaan.com" required autocomplete="email">
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <i class="bi bi-lock icon"></i>
          <input type="password" name="password" id="pwInput"
                 placeholder="Masukkan password" required autocomplete="current-password">
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
      <button class="demo-btn blue"  onclick="fillDemo('admin@dpis.com')">
        <span class="demo-email">admin@dpis.com</span>
        <span class="demo-role">Admin</span>
      </button>
      <button class="demo-btn green" onclick="fillDemo('supervisor@dpis.com')">
        <span class="demo-email">supervisor@dpis.com</span>
        <span class="demo-role">Supervisor</span>
      </button>
      <button class="demo-btn cyan"  onclick="fillDemo('cutting@dpis.com')">
        <span class="demo-email">cutting@dpis.com</span>
        <span class="demo-role">PIC Cutting</span>
      </button>
      <button class="demo-btn amber" onclick="fillDemo('manager@dpis.com')">
        <span class="demo-email">manager@dpis.com</span>
        <span class="demo-role">Manager</span>
      </button>
    </div>
    <div class="demo-hint">Password: <code>password123</code></div>

  </div>
</div>

<script>
function fillDemo(email) {
  document.querySelector('input[name="email"]').value = email;
  document.querySelector('#pwInput').value = 'password123';
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
