<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — DPIS DTHREE</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
:root{--primary:#1a3c6e;--accent:#e85d04}
body{background:linear-gradient(135deg,#1a3c6e 0%,#0d2444 60%,#0a1628 100%);min-height:100vh;display:flex;align-items:center;font-family:'Segoe UI',sans-serif}
.login-card{background:#fff;border-radius:16px;box-shadow:0 25px 60px rgba(0,0,0,.4);overflow:hidden;width:100%;max-width:440px}
.login-header{background:linear-gradient(135deg,var(--primary),#2557a7);padding:2.5rem 2rem;text-align:center;color:#fff}
.login-header .logo-icon{width:72px;height:72px;background:rgba(255,255,255,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem}
.login-body{padding:2rem}
.form-control{border-radius:8px;padding:.75rem 1rem;border:1.5px solid #dee2e6}
.form-control:focus{border-color:var(--primary);box-shadow:0 0 0 .25rem rgba(26,60,110,.15)}
.btn-login{background:linear-gradient(135deg,var(--primary),#2557a7);border:none;border-radius:8px;padding:.8rem;font-weight:600;letter-spacing:.5px;font-size:1rem}
.btn-login:hover{background:linear-gradient(135deg,#0d2444,var(--primary));transform:translateY(-1px)}
.input-group-text{background:#f8f9fa;border:1.5px solid #dee2e6;border-radius:8px 0 0 8px}
.demo-box{background:#f8f9fa;border-radius:8px;padding:1rem;font-size:.8rem}
</style>
</head>
<body>
<div class="container d-flex justify-content-center align-items-center" style="min-height:100vh">
  <div class="login-card">
    <div class="login-header">
      <div class="logo-icon"><i class="bi bi-factory text-white"></i></div>
      <h4 class="fw-bold mb-1">DPIS</h4>
      <p class="mb-0 opacity-75 small">DTHREE Production Integration System</p>
      <p class="mb-0 opacity-60" style="font-size:.75rem">PT DTHREE SUKSES MULIA</p>
    </div>
    <div class="login-body">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2"><i class="bi bi-check-circle me-1"></i>{{ session('success') }}<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger py-2"><i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}</div>
      @endif
      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold text-secondary small">ALAMAT EMAIL</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-envelope text-muted"></i></span>
            <input type="email" name="email" class="form-control" value="{{ old('email','admin@dpis.com') }}" placeholder="email@perusahaan.com" required>
          </div>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold text-secondary small">PASSWORD</label>
          <div class="input-group">
            <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label small" for="remember">Ingat saya</label></div>
        </div>
        <button type="submit" class="btn btn-login btn-primary w-100 text-white"><i class="bi bi-box-arrow-in-right me-2"></i>MASUK KE SISTEM</button>
      </form>
      <div class="demo-box mt-4">
        <p class="text-muted mb-2 fw-semibold">Akun Demo:</p>
        <div class="row g-1">
          <div class="col-6"><span class="badge bg-primary w-100 text-start p-2">admin@dpis.com <br><small class="opacity-75">Admin</small></span></div>
          <div class="col-6"><span class="badge bg-success w-100 text-start p-2">supervisor@dpis.com <br><small class="opacity-75">Supervisor</small></span></div>
          <div class="col-6"><span class="badge bg-info w-100 text-start p-2">cutting@dpis.com <br><small class="opacity-75">PIC Cutting</small></span></div>
          <div class="col-6"><span class="badge bg-warning text-dark w-100 text-start p-2">manager@dpis.com <br><small class="opacity-75">Manager</small></span></div>
        </div>
        <p class="text-muted mt-2 mb-0" style="font-size:.75rem">Password semua akun: <code>password123</code></p>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
