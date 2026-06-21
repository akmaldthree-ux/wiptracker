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
  -webkit-font-smoothing: antialiased;
  background: #0d1117;
  overflow: hidden;
}

/* ══════════════════════════════════════
   LEFT PANEL — Branding
══════════════════════════════════════ */
.left-panel {
  flex: 0 0 52%;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 3rem 3.5rem;
  overflow: hidden;
  background: #222831;
}

/* Animated mesh background */
.left-panel::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(0,173,181,.06) 1px, transparent 1px),
    linear-gradient(90deg, rgba(0,173,181,.06) 1px, transparent 1px);
  background-size: 40px 40px;
  animation: meshShift 20s linear infinite;
}
@keyframes meshShift {
  from { background-position: 0 0; }
  to   { background-position: 40px 40px; }
}

/* Gradient orbs */
.orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  pointer-events: none;
  animation: orbFloat 12s ease-in-out infinite alternate;
}
.orb-1 { width: 420px; height: 420px; background: rgba(0,173,181,.28); top: -140px; left: -100px; animation-delay: 0s; }
.orb-2 { width: 300px; height: 300px; background: rgba(0,100,120,.35); bottom: -80px; right: -60px; animation-delay: -6s; }
.orb-3 { width: 180px; height: 180px; background: rgba(0,173,181,.2); top: 55%; left: 55%; animation-delay: -3s; }
@keyframes orbFloat {
  from { transform: translate(0, 0) scale(1); }
  to   { transform: translate(20px, 30px) scale(1.08); }
}

/* Floating particles */
.particle {
  position: absolute;
  width: 3px; height: 3px;
  background: rgba(0,173,181,.6);
  border-radius: 50%;
  animation: particleDrift linear infinite;
}
@keyframes particleDrift {
  0%   { transform: translateY(100vh) translateX(0); opacity: 0; }
  10%  { opacity: 1; }
  90%  { opacity: .6; }
  100% { transform: translateY(-100px) translateX(30px); opacity: 0; }
}

.left-content { position: relative; z-index: 1; }

/* Logo + brand */
.brand-block { margin-bottom: 3rem; }
.brand-logo {
  width: 100px; height: auto;
  margin-bottom: 1.25rem;
  filter: drop-shadow(0 6px 20px rgba(196,148,26,.3));
  animation: logoPulse 4s ease-in-out infinite;
}
@keyframes logoPulse {
  0%, 100% { filter: drop-shadow(0 6px 20px rgba(196,148,26,.25)); }
  50%       { filter: drop-shadow(0 6px 28px rgba(0,173,181,.5)); }
}

.brand-name {
  font-size: 2rem;
  font-weight: 900;
  color: #fff;
  letter-spacing: -1px;
  line-height: 1.1;
}
.brand-name span {
  background: linear-gradient(135deg, #00ADB5, #00d4de, #00ADB5);
  background-size: 200% auto;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: shimmer 3s linear infinite;
}
@keyframes shimmer {
  from { background-position: 0% center; }
  to   { background-position: 200% center; }
}
.brand-tagline {
  font-size: .82rem;
  color: rgba(255,255,255,.45);
  margin-top: .4rem;
  letter-spacing: .5px;
}

/* System label badge */
.system-badge {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: rgba(0,173,181,.15);
  border: 1px solid rgba(0,173,181,.3);
  border-radius: 30px;
  padding: .35rem .85rem;
  font-size: .7rem;
  font-weight: 700;
  color: #00ADB5;
  letter-spacing: 1.2px;
  text-transform: uppercase;
  margin-bottom: 1.5rem;
}
.system-badge .dot {
  width: 6px; height: 6px;
  background: #00ADB5;
  border-radius: 50%;
  animation: blink 1.8s ease-in-out infinite;
}
@keyframes blink {
  0%, 100% { opacity: 1; }
  50%       { opacity: .2; }
}

/* Feature highlights */
.features { display: flex; flex-direction: column; gap: .85rem; }
.feature-item {
  display: flex;
  align-items: flex-start;
  gap: .85rem;
  padding: .9rem 1.1rem;
  border-radius: 12px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.06);
  transition: all .25s;
  animation: fadeSlideIn .5s ease both;
}
.feature-item:nth-child(1) { animation-delay: .1s; }
.feature-item:nth-child(2) { animation-delay: .2s; }
.feature-item:nth-child(3) { animation-delay: .3s; }
.feature-item:nth-child(4) { animation-delay: .4s; }
@keyframes fadeSlideIn {
  from { opacity: 0; transform: translateX(-16px); }
  to   { opacity: 1; transform: translateX(0); }
}
.feature-item:hover {
  background: rgba(0,173,181,.08);
  border-color: rgba(0,173,181,.2);
  transform: translateX(4px);
}
.feature-icon {
  width: 36px; height: 36px; flex-shrink: 0;
  border-radius: 9px;
  background: linear-gradient(135deg, rgba(0,173,181,.3), rgba(0,173,181,.1));
  border: 1px solid rgba(0,173,181,.25);
  display: flex; align-items: center; justify-content: center;
  font-size: .95rem;
  color: #00ADB5;
}
.feature-text .ft { font-size: .82rem; font-weight: 700; color: rgba(255,255,255,.9); }
.feature-text .fd { font-size: .72rem; color: rgba(255,255,255,.4); margin-top: 1px; }

/* Bottom badge */
.left-footer {
  position: absolute;
  bottom: 1.75rem; left: 3.5rem; right: 3.5rem;
  display: flex; align-items: center; justify-content: space-between;
  z-index: 1;
}
.version-tag {
  font-size: .68rem;
  color: rgba(255,255,255,.25);
  letter-spacing: .3px;
}
.status-tag {
  display: flex; align-items: center; gap: .35rem;
  font-size: .68rem; color: rgba(0,173,181,.7);
}
.status-tag .sdot {
  width: 5px; height: 5px;
  background: #00ADB5; border-radius: 50%;
  animation: blink 2s ease-in-out infinite;
}

/* ══════════════════════════════════════
   RIGHT PANEL — Form
══════════════════════════════════════ */
.right-panel {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 2.5rem;
  background: #f1f5f9;
  position: relative;
  overflow-y: auto;
}
.right-panel::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background:
    radial-gradient(ellipse 70% 50% at 80% 10%, rgba(0,173,181,.07) 0%, transparent 60%),
    radial-gradient(ellipse 50% 40% at 20% 90%, rgba(0,100,120,.06) 0%, transparent 60%);
  pointer-events: none;
}

.form-card {
  position: relative;
  width: 100%;
  max-width: 400px;
  animation: slideUp .45s cubic-bezier(.16,1,.3,1) both;
}
@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

.form-heading {
  margin-bottom: 1.75rem;
}
.form-heading h2 {
  font-size: 1.55rem;
  font-weight: 800;
  color: #0f172a;
  letter-spacing: -.5px;
  line-height: 1.2;
}
.form-heading p {
  font-size: .82rem;
  color: #64748b;
  margin-top: .35rem;
}

/* Alert */
.alert-box {
  display: flex; align-items: center; gap: .5rem;
  padding: .7rem .9rem; border-radius: 10px;
  font-size: .82rem; margin-bottom: 1.25rem;
}
.alert-box.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.alert-box.success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

/* Field */
.field { margin-bottom: 1.1rem; }
.field label {
  display: block;
  font-size: .72rem; font-weight: 700;
  color: #475569; letter-spacing: .5px;
  text-transform: uppercase; margin-bottom: .4rem;
}
.input-wrap { position: relative; }
.input-wrap .icon {
  position: absolute; left: .9rem; top: 50%;
  transform: translateY(-50%);
  color: #94a3b8; font-size: .9rem;
  pointer-events: none; transition: color .15s;
}
.input-wrap input {
  width: 100%;
  padding: .78rem .9rem .78rem 2.45rem;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  font-size: .875rem; font-family: inherit;
  color: #0f172a; background: #fff;
  outline: none; transition: all .2s;
  box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.input-wrap input:focus {
  border-color: #00ADB5;
  box-shadow: 0 0 0 3px rgba(0,173,181,.12), 0 1px 3px rgba(0,0,0,.04);
}
.input-wrap input:focus ~ .icon { color: #00ADB5; }
.input-wrap input::placeholder { color: #c0cfe0; }

.pw-toggle {
  position: absolute; right: .8rem; top: 50%;
  transform: translateY(-50%);
  background: none; border: none; cursor: pointer;
  color: #94a3b8; font-size: .9rem; line-height: 1;
  transition: color .15s; padding: 2px;
}
.pw-toggle:hover { color: #00ADB5; }

/* Remember */
.remember-row {
  display: flex; align-items: center; gap: .5rem;
  margin: 1.25rem 0 1.4rem;
}
.remember-row input[type="checkbox"] {
  width: 15px; height: 15px;
  accent-color: #00ADB5; cursor: pointer;
}
.remember-row label { font-size: .82rem; color: #64748b; cursor: pointer; user-select: none; }

/* Submit */
.btn-login {
  width: 100%; padding: .85rem;
  background: linear-gradient(135deg, #00ADB5 0%, #008a91 100%);
  border: none; border-radius: 10px;
  color: #fff; font-size: .9rem; font-weight: 700;
  font-family: inherit; cursor: pointer; letter-spacing: .3px;
  box-shadow: 0 4px 18px rgba(0,173,181,.38);
  transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: .5rem;
  position: relative; overflow: hidden;
}
.btn-login::after {
  content: '';
  position: absolute; inset: 0;
  background: linear-gradient(135deg, rgba(255,255,255,.15) 0%, transparent 60%);
  pointer-events: none;
}
.btn-login:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(0,173,181,.48);
}
.btn-login:active { transform: translateY(0); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: .6rem;
  margin: 1.5rem 0 .9rem;
  font-size: .67rem; font-weight: 700;
  letter-spacing: .6px; text-transform: uppercase; color: #cbd5e1;
}
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }

/* Demo accounts */
.demo-label {
  font-size: .67rem; font-weight: 700;
  letter-spacing: .8px; text-transform: uppercase;
  color: #94a3b8; text-align: center;
  margin-bottom: .7rem;
}
.demo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .4rem; }
.demo-btn {
  padding: .55rem .75rem;
  border-radius: 9px; border: 1.5px solid;
  background: transparent; cursor: pointer;
  text-align: left; font-family: inherit;
  transition: all .18s;
}
.demo-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

/* Color variants */
.demo-btn.c-teal   { border-color: rgba(0,173,181,.35); color: #005f68; background: rgba(0,173,181,.07); }
.demo-btn.c-blue   { border-color: rgba(59,130,246,.3); color: #1e40af; background: rgba(219,234,254,.4); }
.demo-btn.c-purple { border-color: rgba(139,92,246,.3); color: #4c1d95; background: rgba(237,233,254,.5); }
.demo-btn.c-green  { border-color: rgba(34,197,94,.3);  color: #14532d; background: rgba(220,252,231,.5); }
.demo-btn.c-orange { border-color: rgba(249,115,22,.3); color: #7c2d12; background: rgba(255,237,213,.5); }
.demo-btn.c-rose   { border-color: rgba(244,63,94,.3);  color: #9f1239; background: rgba(255,228,230,.5); }
.demo-btn:hover.c-teal   { background: rgba(0,173,181,.15); }
.demo-btn:hover.c-blue   { background: rgba(59,130,246,.12); }
.demo-btn:hover.c-purple { background: rgba(139,92,246,.12); }
.demo-btn:hover.c-green  { background: rgba(34,197,94,.12); }
.demo-btn:hover.c-orange { background: rgba(249,115,22,.12); }
.demo-btn:hover.c-rose   { background: rgba(244,63,94,.12); }

.demo-email { display: block; font-size: .7rem; font-weight: 700; }
.demo-role  { display: block; font-size: .62rem; opacity: .65; margin-top: 1px; }

.demo-footer {
  display: flex; align-items: center; justify-content: center; gap: .4rem;
  margin-top: .75rem;
  font-size: .72rem; color: #94a3b8;
}
.demo-footer code {
  background: #e2e8f0; padding: .1em .45em;
  border-radius: 4px; color: #334155;
  font-family: ui-monospace, monospace; font-size: .72rem;
}

/* ── Responsive ── */
@media (max-width: 900px) {
  body { flex-direction: column; overflow: auto; }
  .left-panel {
    flex: 0 0 auto;
    padding: 2rem 1.5rem 1.5rem;
    min-height: auto;
  }
  .features { display: none; }
  .brand-block { margin-bottom: 1.25rem; }
  .left-footer { display: none; }
  .right-panel { padding: 1.5rem; }
  .orb-1 { width: 220px; height: 220px; }
  .orb-2, .orb-3 { display: none; }
}
@media (max-width: 480px) {
  .left-panel { padding: 1.5rem 1.25rem 1rem; }
  .brand-name { font-size: 1.5rem; }
  .right-panel { padding: 1.25rem; }
}
</style>
</head>
<body>

<!-- LEFT PANEL -->
<div class="left-panel">
  <!-- Orbs -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <!-- Particles (JS-generated below) -->
  <div id="particles"></div>

  <div class="left-content">
    <!-- System badge -->
    <div class="system-badge">
      <span class="dot"></span>
      Production System
    </div>

    <!-- Brand -->
    <div class="brand-block">
      <img src="{{ asset('images/dthree-logo.png') }}" alt="DTHREE" class="brand-logo">
      <div class="brand-name">DPIS<br><span>Dthree</span></div>
      <div class="brand-tagline">Production Integration System</div>
    </div>

    <!-- Feature highlights -->
    <div class="features">
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-layers"></i></div>
        <div class="feature-text">
          <div class="ft">Real-time WIP Tracking</div>
          <div class="fd">Pantau progress produksi per stasiun secara live</div>
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
          <div class="fd">Inspeksi QC & manajemen reject terintegrasi</div>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
        <div class="feature-text">
          <div class="ft">Analytics & Laporan</div>
          <div class="fd">Dashboard eksekutif & laporan produksi lengkap</div>
        </div>
      </div>
    </div>
  </div>

  <div class="left-footer">
    <span class="version-tag">v2.0.0 · 2026</span>
    <span class="status-tag"><span class="sdot"></span>System Online</span>
  </div>
</div>

<!-- RIGHT PANEL -->
<div class="right-panel">
  <div class="form-card">

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

    <div class="demo-label">Klik untuk mengisi otomatis</div>
    <div class="demo-grid">
      <button class="demo-btn c-teal"   onclick="fillDemo('admin@dthree.id')">
        <span class="demo-email">admin@dthree.id</span>
        <span class="demo-role"><i class="bi bi-shield-fill-check" style="font-size:.6rem"></i> Admin</span>
      </button>
      <button class="demo-btn c-blue"   onclick="fillDemo('supervisor@dthree.id')">
        <span class="demo-email">supervisor@dthree.id</span>
        <span class="demo-role"><i class="bi bi-person-badge" style="font-size:.6rem"></i> Supervisor</span>
      </button>
      <button class="demo-btn c-purple" onclick="fillDemo('manager@dthree.id')">
        <span class="demo-email">manager@dthree.id</span>
        <span class="demo-role"><i class="bi bi-briefcase" style="font-size:.6rem"></i> Manager</span>
      </button>
      <button class="demo-btn c-green"  onclick="fillDemo('cutting@dthree.id')">
        <span class="demo-email">cutting@dthree.id</span>
        <span class="demo-role"><i class="bi bi-scissors" style="font-size:.6rem"></i> Staff Cutting</span>
      </button>
      <button class="demo-btn c-orange" onclick="fillDemo('sewing@dthree.id')">
        <span class="demo-email">sewing@dthree.id</span>
        <span class="demo-role"><i class="bi bi-arrow-left-right" style="font-size:.6rem"></i> Staff Sewing</span>
      </button>
      <button class="demo-btn c-rose"   onclick="fillDemo('qc@dthree.id')">
        <span class="demo-email">qc@dthree.id</span>
        <span class="demo-role"><i class="bi bi-shield-check" style="font-size:.6rem"></i> Staff QC</span>
      </button>
    </div>
    <div class="demo-footer">
      <i class="bi bi-key"></i>
      Password semua akun: <code>password</code>
    </div>

  </div>
</div>

<script>
/* ── Fill demo credentials ── */
function fillDemo(email) {
  const emailEl = document.querySelector('input[name="email"]');
  const pwEl    = document.getElementById('pwInput');
  emailEl.value = email;
  pwEl.value    = 'password';
  /* Flash animation */
  [emailEl, pwEl].forEach(el => {
    el.style.transition = 'background .25s';
    el.style.background = 'rgba(0,173,181,.1)';
    setTimeout(() => el.style.background = '', 450);
  });
}

/* ── Password toggle ── */
function togglePw() {
  const inp  = document.getElementById('pwInput');
  const icon = document.getElementById('pwIcon');
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    inp.type = 'password';
    icon.className = 'bi bi-eye';
  }
}

/* ── Login button loading state ── */
document.querySelector('form').addEventListener('submit', function() {
  const btn = document.getElementById('loginBtn');
  btn.innerHTML = '<span style="width:16px;height:16px;border:2px solid rgba(255,255,255,.4);border-top-color:#fff;border-radius:50%;display:inline-block;animation:spin .7s linear infinite"></span> Memverifikasi...';
  btn.disabled = true;
  const style = document.createElement('style');
  style.textContent = '@keyframes spin{to{transform:rotate(360deg)}}';
  document.head.appendChild(style);
});

/* ── Particles ── */
const container = document.getElementById('particles');
for (let i = 0; i < 12; i++) {
  const p = document.createElement('div');
  p.className = 'particle';
  p.style.left   = Math.random() * 100 + '%';
  p.style.width  = (Math.random() * 2 + 1.5) + 'px';
  p.style.height = p.style.width;
  p.style.animationDuration  = (Math.random() * 18 + 12) + 's';
  p.style.animationDelay     = -(Math.random() * 20) + 's';
  p.style.opacity = Math.random() * 0.5 + 0.2;
  container.appendChild(p);
}
</script>
</body>
</html>
