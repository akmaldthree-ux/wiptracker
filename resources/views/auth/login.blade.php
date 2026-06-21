<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — DPIS | Dthree Production Integration System</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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

/* ══════════════════════════════
   ANIMATED BACKGROUND
══════════════════════════════ */
.bg-layer {
  position: fixed; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 80% 60% at 15% 0%,  rgba(0,173,181,.22) 0%, transparent 55%),
    radial-gradient(ellipse 60% 50% at 85% 100%, rgba(57,62,70,.8)   0%, transparent 55%),
    #222831;
}
/* Animated dot grid */
.bg-layer::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.04) 1px, transparent 1px);
  background-size: 28px 28px;
  animation: gridShift 25s linear infinite;
}
@keyframes gridShift {
  from { background-position: 0 0; }
  to   { background-position: 28px 28px; }
}
/* Scanline shimmer */
.bg-layer::after {
  content: '';
  position: absolute; inset: 0;
  background: repeating-linear-gradient(
    0deg,
    transparent,
    transparent 2px,
    rgba(0,173,181,.015) 2px,
    rgba(0,173,181,.015) 4px
  );
  pointer-events: none;
}

/* Floating blobs */
.blob {
  position: fixed; border-radius: 50%;
  filter: blur(90px); pointer-events: none; z-index: 0;
  animation: drift 14s ease-in-out infinite alternate;
}
.blob-1 { width: 520px; height: 520px; background: rgba(0,173,181,.35); top: -180px; left: -120px; animation-delay: 0s; }
.blob-2 { width: 380px; height: 380px; background: rgba(57,62,70,.9);   bottom: -120px; right: -80px; animation-delay: -5s; }
.blob-3 { width: 260px; height: 260px; background: rgba(0,173,181,.2);  top: 45%; right: 12%; animation-delay: -9s; }
.blob-4 { width: 180px; height: 180px; background: rgba(0,100,120,.4);  bottom: 15%; left: 8%;  animation-delay: -3s; }
@keyframes drift {
  from { transform: translate(0,0) scale(1); }
  to   { transform: translate(18px,28px) scale(1.07); }
}

/* Particles */
.particle {
  position: fixed; border-radius: 50%; pointer-events: none; z-index: 0;
  background: rgba(0,173,181,.7);
  animation: rise linear infinite;
}
@keyframes rise {
  0%   { transform: translateY(105vh) translateX(0);   opacity: 0; }
  8%   { opacity: 1; }
  92%  { opacity: .5; }
  100% { transform: translateY(-80px)  translateX(25px); opacity: 0; }
}

/* ══════════════════════════════
   MAIN CARD (two-column inside)
══════════════════════════════ */
.card {
  position: relative; z-index: 1;
  width: 100%; max-width: 820px;
  border-radius: 24px;
  overflow: hidden;
  display: flex;
  box-shadow:
    0 0 0 1px rgba(0,173,181,.12),
    0 40px 100px rgba(0,0,0,.55),
    0 8px 28px rgba(0,0,0,.25);
  animation: slideUp .45s cubic-bezier(.16,1,.3,1) both;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(28px) scale(.97); }
  to   { opacity: 1; transform: translateY(0)    scale(1); }
}

/* ── Left: branding panel ── */
.card-left {
  flex: 0 0 44%;
  background: #1a2028;
  background-image: linear-gradient(160deg, rgba(0,173,181,.12) 0%, rgba(34,40,49,.95) 50%, rgba(20,26,32,1) 100%);
  padding: 2.25rem 2rem;
  display: flex; flex-direction: column;
  position: relative; overflow: hidden;
}
/* Inner mesh on left panel */
.card-left::before {
  content: '';
  position: absolute; inset: 0;
  background-image:
    linear-gradient(rgba(0,173,181,.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,173,181,.05) 1px, transparent 1px);
  background-size: 32px 32px;
  animation: gridShift 20s linear infinite;
  pointer-events: none;
}
/* Glow corner */
.card-left::after {
  content: '';
  position: absolute; top: -60px; left: -60px;
  width: 240px; height: 240px;
  background: radial-gradient(circle, rgba(0,173,181,.25) 0%, transparent 70%);
  pointer-events: none;
  animation: glowPulse 4s ease-in-out infinite;
}
@keyframes glowPulse {
  0%,100% { opacity: .7; transform: scale(1); }
  50%      { opacity: 1;  transform: scale(1.08); }
}

/* Top accent bar */
.card-left .accent-bar {
  position: absolute; top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, transparent, #00ADB5 40%, #00d4de 60%, transparent);
}

.left-inner { position: relative; z-index: 1; display: flex; flex-direction: column; height: 100%; }

/* System badge */
.system-badge {
  display: inline-flex; align-items: center; gap: .45rem;
  background: rgba(0,173,181,.12);
  border: 1px solid rgba(0,173,181,.28);
  border-radius: 30px; padding: .3rem .75rem;
  font-size: .67rem; font-weight: 700;
  color: #00ADB5; letter-spacing: 1.2px; text-transform: uppercase;
  margin-bottom: 1.5rem; align-self: flex-start;
}
.system-badge .dot {
  width: 5px; height: 5px;
  background: #00ADB5; border-radius: 50%;
  animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.15} }

/* Brand */
.brand-logo {
  width: 90px; height: auto; margin-bottom: 1.2rem;
  filter: drop-shadow(0 4px 14px rgba(196,148,26,.3));
  animation: logoPulse 5s ease-in-out infinite;
}
@keyframes logoPulse {
  0%,100% { filter: drop-shadow(0 4px 14px rgba(196,148,26,.25)); }
  50%      { filter: drop-shadow(0 4px 20px rgba(0,173,181,.5)); }
}
.brand-name {
  font-size: 1.7rem; font-weight: 900; color: #fff;
  letter-spacing: -1px; line-height: 1.1; margin-bottom: .3rem;
}
.brand-name span {
  background: linear-gradient(135deg, #00ADB5, #00e5ef, #00ADB5);
  background-size: 200% auto;
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: shimmer 3s linear infinite;
}
@keyframes shimmer { from{background-position:0% center} to{background-position:200% center} }
.brand-tagline { font-size: .75rem; color: rgba(255,255,255,.38); letter-spacing: .4px; margin-bottom: 1.75rem; }

/* Features */
.features { display: flex; flex-direction: column; gap: .6rem; flex: 1; }
.feature-item {
  display: flex; align-items: center; gap: .75rem;
  padding: .7rem .85rem;
  border-radius: 10px;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.05);
  transition: all .2s;
  animation: fadeSlideIn .5s ease both;
}
.feature-item:nth-child(1){animation-delay:.05s}
.feature-item:nth-child(2){animation-delay:.12s}
.feature-item:nth-child(3){animation-delay:.19s}
.feature-item:nth-child(4){animation-delay:.26s}
@keyframes fadeSlideIn {
  from { opacity:0; transform:translateX(-12px); }
  to   { opacity:1; transform:translateX(0); }
}
.feature-item:hover {
  background: rgba(0,173,181,.08);
  border-color: rgba(0,173,181,.2);
  transform: translateX(3px);
}
.feature-icon {
  width: 32px; height: 32px; flex-shrink: 0;
  border-radius: 8px;
  background: linear-gradient(135deg, rgba(0,173,181,.28), rgba(0,173,181,.08));
  border: 1px solid rgba(0,173,181,.22);
  display: flex; align-items: center; justify-content: center;
  font-size: .85rem; color: #00ADB5;
}
.feature-text .ft { font-size: .78rem; font-weight: 700; color: rgba(255,255,255,.88); }
.feature-text .fd { font-size: .68rem; color: rgba(255,255,255,.35); margin-top: 1px; }

/* Left footer */
.left-footer {
  margin-top: 1.5rem;
  display: flex; justify-content: space-between; align-items: center;
}
.version-tag { font-size: .65rem; color: rgba(255,255,255,.2); }
.status-tag  { display: flex; align-items: center; gap: .3rem; font-size: .65rem; color: rgba(0,173,181,.65); }
.status-tag .sdot { width: 5px; height: 5px; background: #00ADB5; border-radius: 50%; animation: blink 2s ease-in-out infinite; }

/* ── Right: form panel ── */
.card-right {
  flex: 1;
  background: rgba(255,255,255,.975);
  backdrop-filter: blur(20px);
  padding: 2.25rem 2rem;
  display: flex; flex-direction: column; justify-content: center;
  position: relative;
}
/* Right top accent bar */
.card-right::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, #00ADB5, #00d4de, #00ADB5);
  background-size: 200% auto;
  animation: shimmer 3s linear infinite;
}

.form-heading { margin-bottom: 1.5rem; }
.form-heading h2 { font-size: 1.35rem; font-weight: 800; color: #0f172a; letter-spacing: -.4px; }
.form-heading p  { font-size: .8rem; color: #64748b; margin-top: .3rem; }

/* Alert */
.alert-box {
  display: flex; align-items: center; gap: .5rem;
  padding: .65rem .9rem; border-radius: 10px;
  font-size: .82rem; margin-bottom: 1.25rem;
}
.alert-box.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-box.success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* Fields */
.field { margin-bottom: 1rem; }
.field label {
  display: block; font-size: .7rem; font-weight: 700;
  color: #64748b; letter-spacing: .5px; text-transform: uppercase; margin-bottom: .38rem;
}
.input-wrap { position: relative; }
.input-wrap .icon {
  position: absolute; left: .85rem; top: 50%;
  transform: translateY(-50%);
  color: #94a3b8; font-size: .88rem; pointer-events: none; transition: color .15s;
}
.input-wrap input {
  width: 100%;
  padding: .72rem .85rem .72rem 2.4rem;
  border: 1.5px solid #e2e8f0; border-radius: 10px;
  font-size: .875rem; font-family: inherit;
  color: #0f172a; background: #f8fafc;
  outline: none; transition: all .18s;
  box-shadow: 0 1px 2px rgba(0,0,0,.04);
}
.input-wrap input:focus {
  background: #fff; border-color: #00ADB5;
  box-shadow: 0 0 0 3px rgba(0,173,181,.12);
}
.input-wrap input:focus ~ .icon,
.input-wrap input:focus + .btn-pw ~ .icon { color: #00ADB5; }
.input-wrap input::placeholder { color: #c0cfe0; }

.pw-toggle {
  position: absolute; right: .8rem; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; font-size: .88rem;
  transition: color .15s; padding: 2px; line-height: 1;
}
.pw-toggle:hover { color: #00ADB5; }

/* Remember */
.remember-row {
  display: flex; align-items: center; gap: .5rem;
  margin: 1.1rem 0 1.3rem;
}
.remember-row input[type="checkbox"] { width: 14px; height: 14px; accent-color: #00ADB5; cursor: pointer; }
.remember-row label { font-size: .8rem; color: #64748b; cursor: pointer; user-select: none; }

/* Submit */
.btn-login {
  width: 100%; padding: .8rem;
  background: linear-gradient(135deg, #00ADB5 0%, #008a91 100%);
  border: none; border-radius: 10px;
  color: #fff; font-size: .88rem; font-weight: 700;
  font-family: inherit; cursor: pointer; letter-spacing: .3px;
  box-shadow: 0 4px 16px rgba(0,173,181,.38);
  transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  position: relative; overflow: hidden;
}
.btn-login::after {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 55%);
  pointer-events: none;
}
.btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,173,181,.48); }
.btn-login:active { transform: translateY(0); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .55rem;
  margin: 1.4rem 0 .85rem;
  font-size: .66rem; font-weight: 700;
  letter-spacing: .6px; text-transform: uppercase; color: #cbd5e1;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

/* Demo accounts */
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .35rem; }
.demo-btn {
  padding: .48rem .65rem; border-radius: 8px; border: 1.5px solid;
  background: transparent; cursor: pointer; text-align: left;
  font-family: inherit; transition: all .15s;
}
.demo-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.08); }
.demo-btn.gold   { border-color: #fde68a; color: #92400e; background: #fffbeb; }
.demo-btn.amber  { border-color: #fed7aa; color: #9a3412; background: #fff7ed; }
.demo-btn.yellow { border-color: #fef08a; color: #713f12; background: #fefce8; }
.demo-btn.warm   { border-color: #fecaca; color: #991b1b; background: #fef2f2; }
.demo-email { display: block; font-size: .69rem; font-weight: 700; }
.demo-role  { display: block; font-size: .62rem; opacity: .65; margin-top: 1px; }
.demo-hint  { text-align: center; margin-top: .65rem; font-size: .7rem; color: #94a3b8; }
.demo-hint code {
  background: #f1f5f9; padding: .1em .4em; border-radius: 4px;
  color: #475569; font-family: ui-monospace, monospace; font-size: .69rem;
}

/* ── Responsive ── */
@media (max-width: 680px) {
  body { padding: 1rem; align-items: flex-start; overflow: auto; }
  .card { flex-direction: column; max-width: 420px; }
  .card-left { flex: none; padding: 1.75rem 1.5rem 1.5rem; }
  .features { display: none; }
  .left-footer { display: none; }
  .brand-tagline { margin-bottom: 0; }
  .card-right { padding: 1.75rem 1.5rem; }
}
</style>
</head>
<body>

<!-- Background layers -->
<div class="bg-layer"></div>
<div class="blob blob-1"></div>
<div class="blob blob-2"></div>
<div class="blob blob-3"></div>
<div class="blob blob-4"></div>
<div id="particles"></div>

<!-- Main Card -->
<div class="card">

  <!-- LEFT: branding -->
  <div class="card-left">
    <div class="accent-bar"></div>
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
        <span class="version-tag">v2.0.0 · 2026</span>
        <span class="status-tag"><span class="sdot"></span>System Online</span>
      </div>

    </div>
  </div>

  <!-- RIGHT: form -->
  <div class="card-right">

    <div class="form-heading">
      <h2>Selamat Datang 👋</h2>
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
  const emailEl = document.querySelector('input[name="email"]');
  const pwEl    = document.getElementById('pwInput');
  emailEl.value = email;
  pwEl.value    = 'password';
  [emailEl, pwEl].forEach(el => {
    el.style.transition = 'background .3s, border-color .3s';
    el.style.background = 'rgba(0,173,181,.08)';
    el.style.borderColor = 'rgba(0,173,181,.5)';
    setTimeout(() => { el.style.background = ''; el.style.borderColor = ''; }, 500);
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

/* Floating particles */
const pCont = document.getElementById('particles');
for (let i = 0; i < 14; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  const size = Math.random() * 2.5 + 1.5;
  p.style.cssText = `
    left: ${Math.random() * 100}%;
    width: ${size}px; height: ${size}px;
    animation-duration: ${Math.random() * 20 + 14}s;
    animation-delay: -${Math.random() * 22}s;
    opacity: ${Math.random() * .4 + .15};
  `;
  pCont.appendChild(p);
}
</script>

<style>
.spin-ring {
  display: inline-block; width: 15px; height: 15px;
  border: 2px solid rgba(255,255,255,.35);
  border-top-color: #fff; border-radius: 50%;
  animation: spin .65s linear infinite;
  vertical-align: middle;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>

</body>
</html>
