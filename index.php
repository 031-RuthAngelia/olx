<?php
session_start();
require_once __DIR__ . '/config.php';

$q = trim($_GET['q'] ?? '');
$categoryId = $_GET['category_id'] ?? '';
$location = trim($_GET['location'] ?? '');

try {
  $catStmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
  $categories = $catStmt->fetchAll();
} catch (Throwable $e) {
  $categories = [];
}

$ads = [];
try {
  $where = [];
  $params = [];
  if ($q !== '') {
    $where[] = '(a.title LIKE ? OR a.description LIKE ?)';
    $params[] = "%$q%";
    $params[] = "%$q%";
  }
  if ($categoryId !== '' && ctype_digit((string)$categoryId)) {
    $where[] = 'a.category_id = ?';
    $params[] = (int)$categoryId;
  }
  if ($location !== '') {
    $where[] = 'a.location LIKE ?';
    $params[] = "%$location%";
  }
  $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

  $sql = "
    SELECT a.id, a.title, a.price, a.location,
           (SELECT image_path FROM ad_images ai WHERE ai.ad_id = a.id ORDER BY ai.id ASC LIMIT 1) AS image_path
    FROM ads a
    $whereSql
    ORDER BY a.id DESC
    LIMIT 12
  ";
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
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
  <title>FLX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{background-color:#f5f7f9}
    .navbar-brand{font-weight:700}
    .hero{background:linear-gradient(135deg,#0d6efd 0%, #6610f2 100%); color:#fff}
    .category-card{transition:transform .2s ease, box-shadow .2s ease}
    .category-card:hover{transform:translateY(-4px); box-shadow:0 8px 24px rgba(0,0,0,.08)}
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
          <li class="nav-item"><a class="nav-link active" href="#">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="#kategori">Kategori</a></li>
          <li class="nav-item"><a class="nav-link" href="#terbaru">Iklan Terbaru</a></li>
        </ul>
        <div class="d-flex gap-2">
          <?php if (!empty($_SESSION['user_id'])): ?>
            <span class="align-self-center text-muted small d-none d-md-inline">Halo, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="iklansaya.php" class="btn btn-outline-secondary">Iklan Saya</a>
            <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
            <a href="postad.php" class="btn btn-primary">Pasang Iklan</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary">Masuk</a>
            <a href="register.php" class="btn btn-outline-primary">Daftar</a>
            <a href="postad.php" class="btn btn-primary">Pasang Iklan</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
  <section class="hero py-5">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-6">
          <h1 class="h2 mb-3">Jual Beli Barang Bekas & Baru</h1>
          <p class="mb-4">Temukan penawaran terbaik di sekitar Anda</p>
          <form class="row g-2" method="get" action="index.php">
            <div class="col-12 col-md-6 col-lg-5">
              <input type="text" class="form-control" placeholder="Cari barang..." name="q" value="<?php echo htmlspecialchars($q, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-6 col-md-3 col-lg-3">
              <select class="form-select" name="category_id">
                <option value="">Semua Kategori</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($categoryId !== '' && (int)$categoryId === (int)$cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <input type="text" class="form-control" placeholder="Lokasi" name="location" value="<?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-12 col-lg-2 d-grid">
              <button class="btn btn-light text-primary fw-semibold" type="submit"><i class="bi bi-search me-1"></i>Cari</button>
            </div>
          </form>
        </div>
        <div class="col-lg-6 text-center text-lg-end">
          <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?q=80&w=1400&auto=format&fit=crop" alt="Hero" class="img-fluid rounded-3 shadow-sm">
        </div>
      </div>
    </div>
  </section>
  <section id="kategori" class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 m-0">Kategori Populer</h2>
        <a href="#" class="text-decoration-none">Lihat semua</a>
      </div>
      <div class="row g-3">
        <?php if (!empty($categories)): ?>
          <?php $shown = 0; foreach ($categories as $cat): if ($shown++ >= 6) break; ?>
            <div class="col-6 col-md-3 col-lg-2">
              <a href="index.php?category_id=<?php echo (int)$cat['id']; ?>" class="text-decoration-none">
                <div class="card category-card text-center h-100">
                  <div class="card-body">
                    <div class="display-6"><i class="bi bi-tag"></i></div>
                    <div class="fw-semibold mt-2"><?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                  </div>
                </div>
              </a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><div class="alert alert-light border">Belum ada kategori.</div></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <section id="terbaru" class="py-5 pt-0">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 m-0">Iklan Terbaru</h2>
        <a href="#" class="text-decoration-none">Lihat semua</a>
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
                  <div class="text-muted small"><?php echo htmlspecialchars($ad['location'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                  <a class="stretched-link" href="detail.php?id=<?php echo (int)$ad['id']; ?>" aria-label="Lihat detail"></a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><div class="alert alert-light border">Tidak ada iklan yang cocok.</div></div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <footer class="py-4 bg-white border-top mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
      <div class="text-muted small">© <?php echo date('Y'); ?> OLX</div>
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
