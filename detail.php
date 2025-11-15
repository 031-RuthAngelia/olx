<?php
require_once __DIR__ . '/config.php';
session_start();

$id = $_GET['id'] ?? '';
if (!ctype_digit((string)$id)) { $id = '0'; }
$ad = null; $images = []; $similar = [];

try {
  $stmt = $pdo->prepare(
    "SELECT a.id, a.title, a.description, a.price, a.location, a.created_at,
            c.name AS category_name, c.id AS category_id,
            u.id AS seller_id, u.name AS seller_name, u.created_at AS seller_joined
     FROM ads a
     JOIN categories c ON c.id = a.category_id
     JOIN users u ON u.id = a.user_id
     WHERE a.id = ?"
  );
  $stmt->execute([(int)$id]);
  $ad = $stmt->fetch();

  if ($ad) {
    $imgStmt = $pdo->prepare('SELECT image_path FROM ad_images WHERE ad_id = ? ORDER BY id');
    $imgStmt->execute([(int)$id]);
    $images = $imgStmt->fetchAll();
    
    $sellerWhatsapp = null;
    try {
      $waStmt = $pdo->prepare('SELECT whatsapp FROM users WHERE id = ? LIMIT 1');
      $waStmt->execute([(int)$ad['seller_id']]);
      $waRow = $waStmt->fetch();
      if ($waRow && !empty($waRow['whatsapp'])) {
        $sellerWhatsapp = preg_replace('/\D+/', '', (string)$waRow['whatsapp']);
      }
    } catch (Throwable $e) {
      $sellerWhatsapp = null;
    }

    $simStmt = $pdo->prepare(
      "SELECT a.id, a.title, a.price, a.location,
              (SELECT image_path FROM ad_images ai WHERE ai.ad_id = a.id ORDER BY ai.id ASC LIMIT 1) AS image_path
       FROM ads a
       WHERE a.category_id = ? AND a.id <> ?
       ORDER BY a.id DESC
       LIMIT 8"
    );
    $simStmt->execute([(int)$ad['category_id'], (int)$ad['id']]);
    $similar = $simStmt->fetchAll();
  }
} catch (Throwable $e) {
  $ad = null;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Iklan - FLX</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background-color:#f5f7f9}
  .navbar-brand{font-weight:700}
  .price{color:#0d6efd; font-weight:700}
  .ad-img{aspect-ratio:4/3; object-fit:cover}
  .thumb{aspect-ratio:1/1; object-fit:cover; cursor:pointer}
  .card-hover{transition:box-shadow .2s ease}
  .card-hover:hover{box-shadow:0 8px 24px rgba(0,0,0,.08)}
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
        <?php if (!empty($_SESSION['user_id'])): ?>
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

<?php if (!$ad): ?>
  <section class="py-5">
    <div class="container">
      <div class="alert alert-warning">Iklan tidak ditemukan.</div>
      <a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>
    </div>
  </section>
<?php else: ?>
<section class="py-3 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small m-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Beranda</a></li>
        <li class="breadcrumb-item"><a href="index.php?category_id=<?php echo (int)$ad['category_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($ad['category_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($ad['title'], ENT_QUOTES, 'UTF-8'); ?></li>
      </ol>
    </nav>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card card-hover">
          <?php $main = $images[0]['image_path'] ?? null; ?>
          <div class="ratio ratio-4x3 bg-light">
            <?php if ($main): ?>
              <img src="<?php echo htmlspecialchars($main, ENT_QUOTES, 'UTF-8'); ?>" alt="Gambar Utama" class="w-100 h-100 rounded-top-2" style="object-fit:cover">
            <?php else: ?>
              <img src="https://placehold.co/1200x900?text=No+Image" alt="Gambar Utama" class="w-100 h-100 rounded-top-2" style="object-fit:cover">
            <?php endif; ?>
          </div>
          <div class="p-3">
            <div class="row g-2">
              <?php if (!empty($images)): foreach ($images as $img): ?>
                <div class="col-3">
                  <img class="w-100 rounded thumb" src="<?php echo htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="thumb">
                </div>
              <?php endforeach; else: ?>
                <div class="col-12 text-muted small">Tidak ada gambar.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body">
            <h2 class="h5 mb-3">Deskripsi</h2>
            <p class="mb-2"><?php echo nl2br(htmlspecialchars($ad['description'] ?? '-', ENT_QUOTES, 'UTF-8')); ?></p>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card card-hover">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h1 class="h5 m-0"><?php echo htmlspecialchars($ad['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
              <div class="price">Rp <?php echo number_format((float)$ad['price'], 0, ',', '.'); ?></div>
            </div>
            <div class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($ad['location'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
            <div class="d-grid gap-2">
              <?php if (!empty($sellerWhatsapp)): ?>
                <a class="btn btn-success" target="_blank" rel="noopener" href="https://wa.me/<?php echo rawurlencode($sellerWhatsapp); ?>?text=<?php echo rawurlencode('Halo, saya tertarik dengan: ' . ($ad['title'] ?? '')); ?>">
                  <i class="bi bi-whatsapp me-1"></i>Chat via WhatsApp
                </a>
              <?php else: ?>
                <button class="btn btn-success" disabled title="WhatsApp tidak tersedia"><i class="bi bi-whatsapp me-1"></i>WhatsApp tidak tersedia</button>
              <?php endif; ?>
              <button class="btn btn-outline-secondary"><i class="bi bi-share me-1"></i>Bagikan</button>
            </div>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body d-flex align-items-center gap-3">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($ad['seller_name']); ?>&background=0D6EFD&color=fff" width="56" height="56" class="rounded-circle" alt="Seller">
            <div class="flex-grow-1">
              <div class="fw-semibold"><?php echo htmlspecialchars($ad['seller_name'], ENT_QUOTES, 'UTF-8'); ?></div>
              <div class="text-muted small">Bergabung: <?php echo htmlspecialchars(date('Y', strtotime($ad['seller_joined'])), ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm disabled">Profil</a>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body">
            <h3 class="h6">Lokasi</h3>
            <div class="ratio ratio-16x9 bg-light rounded">
              <div class="d-flex align-items-center justify-content-center text-muted"><?php echo htmlspecialchars($ad['location'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 m-0">Iklan Serupa</h2>
        <a href="index.php?category_id=<?php echo (int)$ad['category_id']; ?>" class="text-decoration-none">Lihat semua</a>
      </div>
      <div class="row g-3">
        <?php if (!empty($similar)): ?>
          <?php foreach ($similar as $s): ?>
            <div class="col-12 col-sm-6 col-lg-3">
              <div class="card h-100 card-hover">
                <?php if (!empty($s['image_path'])): ?>
                  <img class="card-img-top ad-img" src="<?php echo htmlspecialchars($s['image_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Ad Image">
                <?php else: ?>
                  <img class="card-img-top ad-img" src="https://placehold.co/600x450?text=No+Image" alt="Ad Image">
                <?php endif; ?>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start">
                    <h3 class="h6 mb-1"><?php echo htmlspecialchars($s['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <div class="price">Rp <?php echo number_format((float)$s['price'], 0, ',', '.'); ?></div>
                  </div>
                  <div class="text-muted small"><?php echo htmlspecialchars($s['location'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                  <a class="stretched-link" href="detail.php?id=<?php echo (int)$s['id']; ?>" aria-label="Lihat detail"></a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12"><div class="alert alert-light border">Belum ada iklan serupa.</div></div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>
<?php endif; ?>

<footer class="py-4 bg-white border-top mt-4">
  <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
    <div class="text-muted small">  <?php echo date('Y'); ?> FLX</div>
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
