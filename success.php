<?php
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Berhasil - FLX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{background-color:#f5f7f9}
    .navbar-brand{font-weight:700}
    .auth-card{max-width: 520px}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">FLX</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample" aria-controls="navbarsExample" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarsExample">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#kategori">Kategori</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#terbaru">Iklan Terbaru</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a href="login.php" class="btn btn-outline-primary">Masuk</a>
        <a href="register.php" class="btn btn-outline-primary">Daftar</a>
        <a href="#" class="btn btn-primary">Pasang Iklan</a>
      </div>
    </div>
  </div>
</nav>

<main class="py-5">
  <div class="container d-flex justify-content-center">
    <div class="card shadow-sm auth-card w-100">
      <div class="card-body p-4 p-md-5 text-center">
        <div class="display-6 mb-3 text-success"><i class="bi bi-check-circle"></i></div>
        <h1 class="h4">Pendaftaran berhasil</h1>
        <p class="text-muted mb-4">Akun Anda telah dibuat. Silakan masuk untuk mulai menggunakan FLX.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
          <a href="login.php" class="btn btn-primary fw-semibold">Masuk</a>
          <a href="index.php" class="btn btn-outline-secondary">Kembali ke Beranda</a>
        </div>
      </div>
    </div>
  </div>
</main>

<footer class="py-4 bg-white border-top mt-4">
  <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
    <div class="text-muted small">© <?php echo date('Y'); ?> FLX</div>
    <div class="d-flex gap-3 small">
      <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
      <a href="#" class="text-decoration-none">Syarat & Ketentuan</a>
      <a href="#" class="text-decoration-none">Bantuan</a>
    </div>
  </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
