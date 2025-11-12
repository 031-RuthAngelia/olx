<?php
require_once __DIR__ . '/config.php';
session_start();
ob_start();

$errors = [];
$identity = '';

if (isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identity = trim($_POST['identity'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);

  if ($identity === '') {
    $errors[] = 'Email atau Username harus diisi.';
  }
  if ($password === '') {
    $errors[] = 'Kata sandi harus diisi.';
  }

  if (!$errors) {
    try {
      $stmt = $pdo->prepare('SELECT id, name, email, password FROM users WHERE email = ? OR name = ? LIMIT 1');
      $stmt->execute([$identity, $identity]);
      $user = $stmt->fetch();

      if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        if ($remember) {
          setcookie('remember_identity', $identity, time() + 60 * 60 * 24 * 30, '/', '', false, true);
        } else {
          if (isset($_COOKIE['remember_identity'])) {
            setcookie('remember_identity', '', time() - 3600, '/');
          }
        }

        header('Location: index.php');
        echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=index.php"></head><body><script>window.location.replace("index.php");</script></body></html>';
        exit;
      } elseif ($user && hash_equals((string)$user['password'], (string)$password)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $pdo->prepare('UPDATE users SET password = ? WHERE id = ?');
        $upd->execute([$newHash, (int)$user['id']]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        if ($remember) {
          setcookie('remember_identity', $identity, time() + 60 * 60 * 24 * 30, '/', '', false, true);
        } else {
          if (isset($_COOKIE['remember_identity'])) {
            setcookie('remember_identity', '', time() - 3600, '/');
          }
        }

        header('Location: index.php');
        echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=index.php"></head><body><script>window.location.replace("index.php");</script></body></html>';
        exit;
      } else {
        $errors[] = 'Kredensial tidak valid.';
      }
    } catch (Throwable $e) {
      $errors[] = 'Terjadi kesalahan pada server. Silakan coba lagi.';
    }
  }
}

if ($identity === '' && !empty($_COOKIE['remember_identity'])) {
  $identity = $_COOKIE['remember_identity'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk - FLX</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background-color:#f5f7f9}
  .navbar-brand{font-weight:700}
  .auth-card{max-width: 440px}
  .price{color:#0d6efd; font-weight:700}
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
        <a href="login.php" class="btn btn-outline-primary active" aria-current="page">Masuk</a>
        <a href="register.php" class="btn btn-outline-primary">Daftar</a>
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
          <div class="display-6 mb-2"><i class="bi bi-person-circle text-primary"></i></div>
          <h1 class="h4 m-0">Masuk ke Akun</h1>
          <div class="text-muted small mt-1">Lanjutkan untuk memasang dan mengelola iklan</div>
        </div>
        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger" role="alert">
            <?php foreach ($errors as $err): ?>
              <div><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form method="post" action="login.php">
          <div class="mb-3">
            <label for="inputEmail" class="form-label">Email atau Username</label>
            <input type="text" class="form-control" id="inputEmail" name="identity" placeholder="nama@contoh.com" value="<?php echo htmlspecialchars($identity, ENT_QUOTES, 'UTF-8'); ?>">
          </div>
          <div class="mb-2">
            <label for="inputPassword" class="form-label">Kata Sandi</label>
            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="••••••••">
          </div>
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="remember" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="rememberMe">Ingat saya</label>
            </div>
            <a href="#" class="small text-decoration-none">Lupa kata sandi?</a>
          </div>
          <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary fw-semibold">Masuk</button>
          </div>
          <div class="text-center text-muted small">
            Belum punya akun? <a href="register.php" class="text-decoration-none">Daftar</a>
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

<?php ob_end_flush(); ?>
