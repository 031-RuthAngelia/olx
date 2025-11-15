<?php
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=login.php"></head><body><script>window.location.replace("login.php");</script></body></html>';
  exit;
}

$userId = (int)$_SESSION['user_id'];
$name = (string)($_SESSION['user_name'] ?? 'Pengguna');

$ads = [];
try {
  $stmt = $pdo->prepare(
    "SELECT a.id, a.title, a.price, a.location, a.created_at, c.name AS category_name,
            (SELECT image_path FROM ad_images ai WHERE ai.ad_id = a.id ORDER BY ai.id ASC LIMIT 1) AS image_path
     FROM ads a
     JOIN categories c ON c.id = a.category_id
     WHERE a.user_id = ?
     ORDER BY a.id DESC"
  );
  $stmt->execute([$userId]);
  $ads = $stmt->fetchAll();
} catch (Throwable $e) {
  $ads = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iklan Saya - FLX</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background-color:#f5f7f9}
  .navbar-brand{font-weight:700}
  .ad-card{transition:box-shadow .2s ease}
  .ad-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.08)}
  .price{color:#0d6efd; font-weight:700}
  .ad-img{aspect-ratio:4/3; object-fit:cover}
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
        <span class="align-self-center text-muted small d-none d-md-inline">Halo, <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
        <a href="iklansaya.php" class="btn btn-outline-secondary active" aria-current="page">Iklan Saya</a>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
        <a href="postad.php" class="btn btn-primary">Pasang Iklan</a>
      </div>
    </div>
  </div>
</nav>

<main class="py-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h1 class="h4 m-0">Iklan Saya</h1>
        <div class="text-muted small">Daftar iklan milik <?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
      <div class="d-grid d-sm-inline">
        <a href="postad.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Pasang Iklan</a>
      </div>
    </div>

    <div class="row g-3">
      <?php if (!empty($ads)): ?>
        <?php foreach ($ads as $ad): ?>
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="card h-100 ad-card">
              <?php if (!empty($ad['image_path'])): ?>
                <img class="card-img-top ad-img" src="<?php echo htmlspecialchars($ad['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Ad Image">
              <?php else: ?>
                <img class="card-img-top ad-img" src="https://placehold.co/600x450?text=No+Image" alt="Ad Image">
              <?php endif; ?>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <h3 class="h6 mb-1"><?php echo htmlspecialchars($ad['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                  <div class="price">Rp <?php echo number_format((float)$ad['price'], 0, ',', '.'); ?></div>
                </div>
                <div class="text-muted small"><?php echo htmlspecialchars($ad['category_name'], ENT_QUOTES, 'UTF-8'); ?> • <?php echo htmlspecialchars($ad['location'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                <a class="stretched-link" href="detail.php?id=<?php echo (int)$ad['id']; ?>" aria-label="Lihat detail"></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="alert alert-light border d-flex justify-content-between align-items-center">
            <div>Anda belum memiliki iklan. Mulai pasang iklan pertama Anda.</div>
            <a href="postad.php" class="btn btn-primary btn-sm">Pasang Iklan</a>
          </div>
        </div>
      <?php endif; ?>
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
