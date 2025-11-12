<?php
require_once __DIR__ . '/config.php';

$errors = [];
$name = '';
$email = '';
$agree = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password2 = $_POST['password2'] ?? '';
  $agree = isset($_POST['agree']);

  if ($name === '' || mb_strlen($name) < 3 || mb_strlen($name) > 100) {
    $errors[] = 'Nama harus diisi (3-100 karakter).';
  }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
    $errors[] = 'Email tidak valid.';
  }
  if (mb_strlen($password) < 8) {
    $errors[] = 'Kata sandi minimal 8 karakter.';
  }
  if ($password !== $password2) {
    $errors[] = 'Konfirmasi kata sandi tidak cocok.';
  }
  if (!$agree) {
    $errors[] = 'Anda harus menyetujui Syarat & Ketentuan.';
  }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
        $errors[] = 'Email sudah terdaftar.';
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $ins->execute([$name, $email, $hash]);
        header('Location: success.php');
        exit;
      }
    } catch (Throwable $e) {
      $errors[] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    }
  }
}
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
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger" role="alert">
            <div class="fw-semibold mb-1">Pendaftaran gagal:</div>
            <ul class="m-0 ps-3">
              <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        <form method="post" novalidate>
          <div class="row g-3">
            <div class="col-12">
              <label for="inputName" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="inputName" name="name" placeholder="Nama lengkap" value="<?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
            </div>
            <div class="col-12">
              <label for="inputEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="inputEmail" name="email" placeholder="nama@contoh.com" value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required>
            </div>
            <div class="col-12 col-md-6">
              <label for="inputPassword" class="form-label">Kata Sandi</label>
              <input type="password" class="form-control" id="inputPassword" name="password" placeholder="••••••••" minlength="8" required>
            </div>
            <div class="col-12 col-md-6">
              <label for="inputPassword2" class="form-label">Ulangi Kata Sandi</label>
              <input type="password" class="form-control" id="inputPassword2" name="password2" placeholder="••••••••" minlength="8" required>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="agreeTerms" name="agree" <?= $agree ? 'checked' : '' ?>>
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
