<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — DPIS | Dthree Production Integration System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ─── Design tokens ─── */
:root {
  --primary:        #533afd;
  --primary-deep:   #4434d4;
  --primary-press:  #2e2b8c;
  --primary-soft:   #665efd;
  --primary-subdued:#b9b9f9;
  --primary-bg:     #f0eeff;
  --ink:            #0d253d;
  --ink-mute:       #64748d;
  --canvas:         #ffffff;
  --canvas-soft:    #f6f9fc;
  --hairline:       #e3e8ee;
  --hairline-input: #a8c3de;
}

body {
  font-family: 'Inter', system-ui, sans-serif;
  font-weight: 300;
  font-feature-settings: "ss01";
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  -webkit-font-smoothing: antialiased;
  background: #0d1b2e;
  overflow: hidden;
  position: relative;
}

/* ══════════════════════════════
   GRADIENT MESH BACKDROP
   (design spec: cream→orange→lavender→indigo→ruby)
══════════════════════════════ */
.mesh {
  position: fixed; inset: 0; z-index: 0;
  background: #0d1b2e;
  overflow: hidden;
}
.mesh-blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(110px);
  pointer-events: none;
  animation: drift 16s ease-in-out infinite alternate;
}
.mesh-blob-1 {
  width: 640px; height: 480px;
  background: rgba(83,58,253,.28);
  top: -160px; left: -120px;
  animation-delay: 0s;
}
.mesh-blob-2 {
  width: 440px; height: 440px;
  background: rgba(234,34,97,.18);
  bottom: -100px; right: -80px;
  animation-delay: -6s;
}
.mesh-blob-3 {
  width: 320px; height: 320px;
  background: rgba(102,94,253,.22);
  top: 40%; right: 10%;
  animation-delay: -11s;
}
.mesh-blob-4 {
  width: 260px; height: 260px;
  background: rgba(185,185,249,.12);
  bottom: 20%; left: 6%;
  animation-delay: -4s;
}
@keyframes drift {
  from { transform: translate(0,0) scale(1); }
  to   { transform: translate(20px,30px) scale(1.06); }
}

/* Dot grid overlay */
.mesh::after {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.035) 1px, transparent 1px);
  background-size: 28px 28px;
  animation: gridShift 28s linear infinite;
  pointer-events: none;
}
@keyframes gridShift {
  from { background-position: 0 0; }
  to   { background-position: 28px 28px; }
}

/* ══════════════════════════════
   MAIN CARD — two-column
══════════════════════════════ */
.card {
  position: relative; z-index: 1;
  width: 100%; max-width: 840px;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  border: 1px solid rgba(83,58,253,.2);
  box-shadow:
    0 0 0 1px rgba(83,58,253,.1),
    0 40px 100px rgba(0,0,0,.55),
    0 8px 28px rgba(0,0,0,.3);
  animation: slideUp .42s cubic-bezier(.16,1,.3,1) both;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(24px) scale(.97); }
  to   { opacity: 1; transform: translateY(0)    scale(1); }
}

/* ── Left: branding panel ── */
.card-left {
  flex: 0 0 42%;
  background: #0c1628;
  background-image:
    radial-gradient(ellipse 80% 60% at 10% 0%, rgba(83,58,253,.22) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 90% 100%, rgba(234,34,97,.12) 0%, transparent 60%);
  padding: 2.5rem 2rem;
  display: flex; flex-direction: column;
  position: relative; overflow: hidden;
}
/* Indigo top accent bar (design spec: gradient fill) */
.card-left::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent 0%, var(--primary) 35%, var(--primary-soft) 65%, transparent 100%);
}
/* Inner grid */
.card-left::after {
  content: '';
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(83,58,253,.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(83,58,253,.04) 1px, transparent 1px);
  background-size: 32px 32px;
  pointer-events: none;
}
.left-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

/* Pill badge — design spec: pill-tag-soft */
.system-badge {
  display: inline-flex; align-items: center; gap: .4rem;
  background: rgba(83,58,253,.15);
  border: 1px solid rgba(83,58,253,.3);
  border-radius: 9999px; padding: .28rem .75rem;
  font-size: .66rem; font-weight: 500;
  color: var(--primary-subdued); letter-spacing: 1px; text-transform: uppercase;
  margin-bottom: 1.6rem; align-self: flex-start;
}
.system-badge .dot {
  width: 5px; height: 5px;
  background: var(--primary-soft); border-radius: 50%;
  animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.15} }

.brand-logo {
  width: 88px; height: auto; margin-bottom: 1.2rem;
  filter: drop-shadow(0 4px 18px rgba(83,58,253,.35));
}
.brand-name {
  font-size: 1.65rem; font-weight: 300; color: #fff;
  letter-spacing: -1.2px; line-height: 1.1; margin-bottom: .3rem;
  font-feature-settings: "ss01";
}
.brand-name span {
  background: linear-gradient(120deg, var(--primary-subdued), var(--primary-soft), var(--primary-subdued));
  background-size: 200% auto;
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: shimmer 4s linear infinite;
}
@keyframes shimmer { from{background-position:0% center} to{background-position:200% center} }
.brand-tagline { font-size: .73rem; color: rgba(255,255,255,.3); letter-spacing: .3px; margin-bottom: 1.75rem; }

/* Feature list */
.features { display: flex; flex-direction: column; gap: .55rem; flex: 1; }
.feature-item {
  display: flex; align-items: center; gap: .7rem;
  padding: .65rem .8rem; border-radius: 10px;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.05);
  transition: all .18s;
  animation: fadeSlideIn .5s ease both;
}
.feature-item:nth-child(1){animation-delay:.06s}
.feature-item:nth-child(2){animation-delay:.13s}
.feature-item:nth-child(3){animation-delay:.2s}
.feature-item:nth-child(4){animation-delay:.27s}
@keyframes fadeSlideIn {
  from { opacity:0; transform:translateX(-10px); }
  to   { opacity:1; transform:translateX(0); }
}
.feature-item:hover {
  background: rgba(83,58,253,.1);
  border-color: rgba(83,58,253,.22);
  transform: translateX(3px);
}
.feature-icon {
  width: 30px; height: 30px; flex-shrink: 0; border-radius: 8px;
  background: rgba(83,58,253,.18);
  border: 1px solid rgba(83,58,253,.25);
  display: flex; align-items: center; justify-content: center;
  font-size: .82rem; color: var(--primary-subdued);
}
.feature-text .ft { font-size: .77rem; font-weight: 500; color: rgba(255,255,255,.82); }
.feature-text .fd { font-size: .67rem; color: rgba(255,255,255,.3); margin-top: 1px; }

.left-footer {
  margin-top: 1.5rem;
  display: flex; justify-content: space-between; align-items: center;
}
.version-tag { font-size: .63rem; color: rgba(255,255,255,.18); }
.status-tag  { display: flex; align-items: center; gap: .3rem; font-size: .63rem; color: rgba(102,94,253,.6); }
.status-tag .sdot { width: 4px; height: 4px; background: var(--primary-soft); border-radius: 50%; animation: blink 2s ease-in-out infinite; }

/* ── Right: form panel ── */
.card-right {
  flex: 1;
  background: var(--canvas);
  padding: 2.5rem 2rem;
  display: flex; flex-direction: column; justify-content: center;
  position: relative;
}
/* Indigo top accent bar */
.card-right::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, var(--primary) 0%, var(--primary-soft) 50%, var(--primary) 100%);
  background-size: 200% auto;
  animation: shimmer 4s linear infinite;
}

.form-heading { margin-bottom: 1.5rem; }
.form-heading h2 {
  font-size: 1.3rem; font-weight: 300; color: var(--ink);
  letter-spacing: -.6px;
  font-feature-settings: "ss01";
}
.form-heading p { font-size: .8rem; color: var(--ink-mute); margin-top: .3rem; font-weight: 300; }

/* Alert */
.alert-box {
  display: flex; align-items: center; gap: .5rem;
  padding: .62rem .9rem;
  border-radius: 8px;
  font-size: .82rem; margin-bottom: 1.25rem; font-weight: 300;
  border-left: 3px solid;
}
.alert-box.error   { background: #fef2f2; color: #991b1b; border-color: #ef4444; }
.alert-box.success { background: #f0fdf4; color: #166534; border-color: #22c55e; }

/* Fields */
.field { margin-bottom: .95rem; }
.field label {
  display: block; font-size: .7rem; font-weight: 500;
  color: var(--ink-mute); letter-spacing: .4px; text-transform: uppercase;
  margin-bottom: .35rem;
}
.input-wrap { position: relative; }
.input-wrap .icon {
  position: absolute; left: .8rem; top: 50%;
  transform: translateY(-50%);
  color: var(--hairline-input); font-size: .85rem; pointer-events: none; transition: color .13s;
}
.input-wrap input {
  width: 100%;
  padding: .7rem .8rem .7rem 2.35rem;
  border: 1px solid var(--hairline-input);
  border-radius: 6px;
  font-size: .875rem; font-family: inherit; font-weight: 300;
  color: var(--ink); background: var(--canvas-soft);
  outline: none; transition: all .15s;
}
.input-wrap input:focus {
  background: var(--canvas);
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(83,58,253,.1);
}
.input-wrap input:focus + .icon,
.input-wrap .icon.focused { color: var(--primary); }
.input-wrap input::placeholder { color: #c0cfe0; }

.pw-toggle {
  position: absolute; right: .75rem; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: var(--hairline-input); font-size: .85rem;
  transition: color .13s; padding: 2px; line-height: 1;
}
.pw-toggle:hover { color: var(--primary); }

/* Remember */
.remember-row {
  display: flex; align-items: center; gap: .5rem;
  margin: 1rem 0 1.2rem;
}
.remember-row input[type="checkbox"] {
  width: 14px; height: 14px;
  accent-color: var(--primary); cursor: pointer;
}
.remember-row label { font-size: .8rem; color: var(--ink-mute); cursor: pointer; user-select: none; font-weight: 300; }

/* Submit — primary pill button per design spec */
.btn-login {
  width: 100%; padding: .72rem;
  background: var(--primary);
  border: none; border-radius: 9999px;
  color: #fff; font-size: .875rem; font-weight: 400;
  font-family: inherit; cursor: pointer;
  box-shadow: 0 4px 16px rgba(83,58,253,.38);
  transition: all .15s;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  position: relative; overflow: hidden;
  font-feature-settings: "ss01";
}
.btn-login:hover {
  background: var(--primary-deep);
  box-shadow: 0 6px 22px rgba(83,58,253,.46);
  transform: translateY(-1px);
}
.btn-login:active { background: var(--primary-press); transform: scale(.99); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .5rem;
  margin: 1.3rem 0 .8rem;
  font-size: .64rem; font-weight: 500;
  letter-spacing: .5px; text-transform: uppercase; color: var(--hairline-input);
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--hairline); }

/* Demo accounts */
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .3rem; }
.demo-btn {
  padding: .45rem .65rem; border-radius: 9999px; border: 1px solid;
  background: transparent; cursor: pointer; text-align: left;
  font-family: inherit; transition: all .13s; font-weight: 300;
}
.demo-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.07); }
.demo-btn.indigo { border-color: var(--primary-subdued); color: var(--primary-deep); background: var(--primary-bg); }
.demo-btn.indigo:hover { background: #e6e0ff; }
.demo-btn.navy   { border-color: #a8c3de; color: #1c3d5a; background: #f0f6fb; }
.demo-btn.slate  { border-color: #cbd5e1; color: #475569; background: #f8fafc; }
.demo-btn.rose   { border-color: #fca5a5; color: #991b1b; background: #fef2f2; }
.demo-email { display: block; font-size: .68rem; font-weight: 500; }
.demo-role  { display: block; font-size: .61rem; opacity: .6; margin-top: 1px; }
.demo-hint  { text-align: center; margin-top: .6rem; font-size: .7rem; color: var(--ink-mute); }
.demo-hint code {
  background: var(--canvas-soft); padding: .1em .4em; border-radius: 4px;
  color: var(--ink-mute); font-family: ui-monospace, monospace; font-size: .68rem;
}

/* ── Responsive ── */
@media (max-width: 680px) {
  body { padding: 1rem; align-items: flex-start; overflow: auto; }
  .card { flex-direction: column; max-width: 420px; border-radius: 16px; }
  .card-left { flex: none; padding: 1.75rem 1.5rem 1.5rem; }
  .features { display: none; }
  .left-footer { display: none; }
  .brand-tagline { margin-bottom: 0; }
  .card-right { padding: 1.75rem 1.5rem; }
}

.spin-ring {
  display: inline-block; width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,.3);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .6s linear infinite;
  vertical-align: middle;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>

<div class="mesh">
  <div class="mesh-blob mesh-blob-1"></div>
  <div class="mesh-blob mesh-blob-2"></div>
  <div class="mesh-blob mesh-blob-3"></div>
  <div class="mesh-blob mesh-blob-4"></div>
</div>

<!-- Main Card -->
<div class="card">

  <!-- LEFT: branding -->
  <div class="card-left">
    <div class="left-inner">

      <div class="system-badge">
        <span class="dot"></span>
        Production System
      </div>

      <img src="{{ asset('images/dthree-logo.png') }}" alt="DTHREE" class="brand-logo">
      <div class="brand-name">DPIS<br><span>Dthree</span></div>
      <div class="brand-tagline">Production Integration System</div>

      <div class="features">
        <div class="feature-item">
          <div class="feature-icon"><i class="bi bi-layers"></i></div>
          <div class="feature-text">
            <div class="ft">Real-time WIP Tracking</div>
            <div class="fd">Pantau produksi per stasiun secara live</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="bi bi-arrow-left-right"></i></div>
          <div class="feature-text">
            <div class="ft">Digital Handover</div>
            <div class="fd">Serah terima antar stasiun dengan bukti foto</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
          <div class="feature-text">
            <div class="ft">Quality Control</div>
            <div class="fd">Inspeksi QC & reject terintegrasi</div>
          </div>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
          <div class="feature-text">
            <div class="ft">Analytics & Laporan</div>
            <div class="fd">Dashboard eksekutif & laporan lengkap</div>
          </div>
        </div>
      </div>

      <div class="left-footer">
        <span class="version-tag">v2.1.0 · 2026</span>
        <span class="status-tag"><span class="sdot"></span>System Online</span>
      </div>

    </div>
  </div>

  <!-- RIGHT: form -->
  <div class="card-right">

    <div class="form-heading">
      <h2>Selamat Datang</h2>
      <p>Masuk ke akun Anda untuk melanjutkan</p>
    </div>

    @if(session('success'))
    <div class="alert-box success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert-box error"><i class="bi bi-exclamation-triangle-fill"></i>{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
      @csrf

      <div class="field">
        <label>Email</label>
        <div class="input-wrap">
          <input type="email" name="email" value="{{ old('email','admin@dthree.id') }}"
                 placeholder="email@dthree.id" required autocomplete="email"
                 onfocus="this.nextElementSibling.classList.add('focused')"
                 onblur="this.nextElementSibling.classList.remove('focused')">
          <i class="bi bi-envelope icon"></i>
        </div>
      </div>

      <div class="field">
        <label>Password</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pwInput"
                 placeholder="Masukkan password" required autocomplete="current-password"
                 onfocus="this.nextElementSibling.classList.add('focused')"
                 onblur="this.nextElementSibling.classList.remove('focused')">
          <i class="bi bi-lock icon"></i>
          <button type="button" class="pw-toggle" onclick="togglePw()" tabindex="-1">
            <i class="bi bi-eye" id="pwIcon"></i>
          </button>
        </div>
      </div>

      <div class="remember-row">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Ingat saya di perangkat ini</label>
      </div>

      <button type="submit" class="btn-login" id="loginBtn">
        <i class="bi bi-box-arrow-in-right"></i>
        Masuk ke Sistem
      </button>
    </form>

    <div class="divider">Akun Demo</div>

    <div class="demo-grid">
      <button class="demo-btn indigo" onclick="fillDemo('admin@dthree.id')">
        <span class="demo-email">admin@dthree.id</span>
        <span class="demo-role">Admin</span>
      </button>
      <button class="demo-btn navy"   onclick="fillDemo('supervisor@dthree.id')">
        <span class="demo-email">supervisor@dthree.id</span>
        <span class="demo-role">Supervisor</span>
      </button>
      <button class="demo-btn slate"  onclick="fillDemo('manager@dthree.id')">
        <span class="demo-email">manager@dthree.id</span>
        <span class="demo-role">Manager</span>
      </button>
      <button class="demo-btn rose"   onclick="fillDemo('cutting@dthree.id')">
        <span class="demo-email">cutting@dthree.id</span>
        <span class="demo-role">Staff Cutting</span>
      </button>
    </div>
    <div class="demo-hint">Password: <code>password</code></div>

  </div>
</div>

<script>
function fillDemo(email) {
  const emailEl = document.querySelector('input[name="email"]');
  const pwEl    = document.getElementById('pwInput');
  emailEl.value = email;
  pwEl.value    = 'password';
  [emailEl, pwEl].forEach(el => {
    el.style.transition = 'background .3s, border-color .3s';
    el.style.background = 'rgba(83,58,253,.06)';
    el.style.borderColor = 'rgba(83,58,253,.4)';
    setTimeout(() => { el.style.background = ''; el.style.borderColor = ''; }, 600);
  });
}

function togglePw() {
  const inp  = document.getElementById('pwInput');
  const icon = document.getElementById('pwIcon');
  inp.type = inp.type === 'password' ? 'text' : 'password';
  icon.className = inp.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

document.getElementById('loginForm').addEventListener('submit', function() {
  const btn = document.getElementById('loginBtn');
  btn.innerHTML = '<span class="spin-ring"></span> Memverifikasi...';
  btn.disabled = true;
});
</script>

</body>
</html>
