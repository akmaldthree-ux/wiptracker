<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>404 — Halaman Tidak Ditemukan</title>
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: #080e1a;
    color: #c9d4e8;
    font-family: 'Inter', system-ui, sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
  }
  .container {
    text-align: center;
    max-width: 480px;
  }
  .error-code {
    font-size: 7rem;
    font-weight: 700;
    line-height: 1;
    color: #533afd;
    letter-spacing: -4px;
    text-shadow: 0 0 60px rgba(83,58,253,.4);
  }
  .error-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #ffffff;
    margin: 1rem 0 .5rem;
    letter-spacing: -.3px;
  }
  .error-desc {
    font-size: .95rem;
    color: #8899b4;
    line-height: 1.6;
    margin-bottom: 2rem;
  }
  .btn-back {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: #533afd;
    color: #fff;
    text-decoration: none;
    padding: .65rem 1.6rem;
    border-radius: 9999px;
    font-size: .9rem;
    font-weight: 500;
    transition: background .15s, box-shadow .15s;
    border: none;
    cursor: pointer;
  }
  .btn-back:hover {
    background: #4434d4;
    box-shadow: 0 4px 20px rgba(83,58,253,.45);
    color: #fff;
  }
  .divider {
    width: 48px;
    height: 3px;
    background: #533afd;
    border-radius: 9999px;
    margin: 1.25rem auto;
    opacity: .5;
  }
</style>
</head>
<body>
  <div class="container">
    <div class="error-code">404</div>
    <div class="divider"></div>
    <h1 class="error-title">Halaman Tidak Ditemukan</h1>
    <p class="error-desc">Halaman yang Anda cari tidak ada atau telah dipindahkan.<br>Periksa kembali URL atau kembali ke halaman sebelumnya.</p>
    <a href="javascript:history.back()" class="btn-back">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
      Kembali
    </a>
  </div>
</body>
</html>
