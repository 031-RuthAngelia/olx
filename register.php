<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar - FLX</title>
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
        <a href="register.php" class="btn btn-outline-primary active" aria-current="page">Daftar</a>
        <a href="#" class="btn btn-primary">Pasang Iklan</a>
      </div>
    </div>
  </div>
</nav>

<main class="py-5">
  <div class="container d-flex justify-content-center">
    <div class="card shadow-sm auth-card w-100">
      <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
          <div class="display-6 mb-2"><i class="bi bi-person-plus text-primary"></i></div>
          <h1 class="h4 m-0">Buat Akun Baru</h1>
          <div class="text-muted small mt-1">Daftar untuk mulai memasang dan mengelola iklan</div>
        </div>
        <form>
          <div class="row g-3">
            <div class="col-12">
              <label for="inputName" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="inputName" placeholder="Nama lengkap">
            </div>
            <div class="col-12">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" placeholder="nama@contoh.com">
            </div>
            <div class="col-12 col-md-6">
              <label for="inputPassword" class="form-label">Kata Sandi</label>
              <input type="password" class="form-control" id="inputPassword" placeholder="••••••••">
            </div>
            <div class="col-12 col-md-6">
              <label for="inputPassword2" class="form-label">Ulangi Kata Sandi</label>
              <input type="password" class="form-control" id="inputPassword2" placeholder="••••••••">
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="agreeTerms">
                <label class="form-check-label" for="agreeTerms">
                  Saya setuju dengan <a href="#" class="text-decoration-none">Syarat & Ketentuan</a> dan <a href="#" class="text-decoration-none">Kebijakan Privasi</a>
                </label>
              </div>
            </div>
            <div class="col-12 d-grid mt-2">
              <button type="submit" class="btn btn-primary fw-semibold">Daftar</button>
            </div>
            <div class="col-12 text-center text-muted small">
              Sudah punya akun? <a href="login.php" class="text-decoration-none">Masuk</a>
            </div>
          </div>
        </form>
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
