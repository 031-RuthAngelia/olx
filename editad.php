<?php
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=login.php"></head><body><script>window.location.replace("login.php");</script></body></html>';
  exit;
}

$userId = (int)$_SESSION['user_id'];

$adId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($adId <= 0) {
  header('Location: iklansaya.php');
  exit;
}

$errors = [];

try {
  $catStmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
  $categories = $catStmt->fetchAll();
} catch (Throwable $e) {
  $categories = [];
}

try {
  $stmt = $pdo->prepare('SELECT id, category_id, title, description, price, location FROM ads WHERE id = ? AND user_id = ?');
  $stmt->execute([$adId, $userId]);
  $ad = $stmt->fetch();
  if (!$ad) {
    header('Location: iklansaya.php');
    exit;
  }
} catch (Throwable $e) {
  header('Location: iklansaya.php');
  exit;
}

$old = [
  'title' => $ad['title'] ?? '',
  'category_id' => (string)($ad['category_id'] ?? ''),
  'price' => (string)($ad['price'] ?? ''),
  'description' => $ad['description'] ?? '',
  'location' => $ad['location'] ?? '',
];

// lokasi list dari ads
try {
  $locStmt = $pdo->query("SELECT DISTINCT location FROM ads WHERE location IS NOT NULL AND location <> '' ORDER BY location");
  $locations = array_column($locStmt->fetchAll(), 'location');
} catch (Throwable $e) {
  $locations = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $old['title'] = trim($_POST['title'] ?? '');
  $old['category_id'] = (string)($_POST['category_id'] ?? '');
  $old['price'] = trim($_POST['price'] ?? '');
  $old['description'] = trim($_POST['description'] ?? '');
  $old['location'] = trim($_POST['location'] ?? '');

  if ($old['title'] === '' || mb_strlen($old['title']) > 150) {
    $errors[] = 'Judul wajib diisi (maks 150 karakter).';
  }
  if ($old['category_id'] === '' || !ctype_digit($old['category_id'])) {
    $errors[] = 'Kategori wajib dipilih.';
  }
  if ($old['price'] === '' || !is_numeric($old['price']) || (float)$old['price'] < 0) {
    $errors[] = 'Harga tidak valid.';
  }
  if ($old['location'] !== '' && mb_strlen($old['location']) > 100) {
    $errors[] = 'Lokasi terlalu panjang.';
  }

  if (!$errors) {
    try {
      $upd = $pdo->prepare('UPDATE ads SET category_id = ?, title = ?, description = ?, price = ?, location = ? WHERE id = ? AND user_id = ?');
      $upd->execute([
        (int)$old['category_id'],
        $old['title'],
        $old['description'] !== '' ? $old['description'] : null,
        number_format((float)$old['price'], 2, '.', ''),
        $old['location'] !== '' ? $old['location'] : null,
        $adId,
        $userId,
      ]);

      if ($upd->rowCount() > 0) {
        header('Location: detail.php?id=' . $adId);
      } else {
        header('Location: iklansaya.php');
      }
      echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=detail.php?id=' . htmlspecialchars((string)$adId, ENT_QUOTES, 'UTF-8') . '"></head><body><script>window.location.replace("detail.php?id=' . htmlspecialchars((string)$adId, ENT_QUOTES, 'UTF-8') . '");</script></body></html>';
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Terjadi kesalahan saat memperbarui iklan.';
    }
  }
}

?><!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Iklan - FLX</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body{background-color:#f5f7f9}
  .navbar-brand{font-weight:700}
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
        <a href="iklansaya.php" class="btn btn-outline-secondary active" aria-current="page">Iklan Saya</a>
        <a href="logout.php" class="btn btn-outline-secondary">Logout</a>
        <a href="postad.php" class="btn btn-primary">Pasang Iklan</a>
      </div>
    </div>
  </div>
</nav>

<main class="py-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-lg-10">
        <div class="card shadow-sm card-hover">
          <div class="card-body p-4 p-md-5">
            <div class="mb-4">
              <h1 class="h4 m-0">Edit Iklan</h1>
              <div class="text-muted small mt-1">Perbarui detail iklan Anda.</div>
            </div>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $err): ?>
                  <div><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <form method="post" action="editad.php?id=<?php echo (int)$adId; ?>">
              <div class="row g-3">
                <div class="col-12">
                  <label for="adTitle" class="form-label">Judul Iklan</label>
                  <input type="text" class="form-control" id="adTitle" name="title" value="<?php echo htmlspecialchars($old['title'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div class="col-12 col-md-6">
                  <label for="adCategory" class="form-label">Kategori</label>
                  <select id="adCategory" class="form-select" name="category_id" required>
                    <option value="" disabled <?php echo ($old['category_id'] === '' ? 'selected' : ''); ?>>Pilih kategori</option>
                    <?php foreach ($categories as $cat): ?>
                      <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($old['category_id'] !== '' && (int)$old['category_id'] === (int)$cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label for="adPrice" class="form-label">Harga</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="adPrice" name="price" min="0" step="0.01" value="<?php echo htmlspecialchars($old['price'], ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                </div>

                <div class="col-12">
                  <label for="adDescription" class="form-label">Deskripsi</label>
                  <textarea id="adDescription" class="form-control" name="description" rows="6"><?php echo htmlspecialchars($old['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="col-12 col-md-6">
                  <label for="adLocationSelect" class="form-label">Lokasi</label>
                  <select class="form-select" id="adLocationSelect" name="location">
                    <option value="" disabled <?php echo ($old['location'] === '' ? 'selected' : ''); ?>>Pilih lokasi</option>
                    <?php foreach ($locations as $loc): ?>
                      <option value="<?php echo htmlspecialchars($loc, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($old['location'] !== '' && $old['location'] === $loc ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($loc, ENT_QUOTES, 'UTF-8'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <input type="text" class="form-control mt-2 d-none" id="adLocationInput" placeholder="Masukkan lokasi baru">
                  <div class="small mt-1">
                    <a href="#" class="text-decoration-none" id="addLocationLink">Atau tambahkan lokasi baru</a>
                    <a href="#" class="text-decoration-none d-none" id="cancelNewLocationLink">Batal</a>
                  </div>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                  <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                  <a href="iklansaya.php" class="btn btn-outline-secondary">Batal</a>
                </div>
              </div>
            </form>

          </div>
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
<script>
  (function(){
    const sel = document.getElementById('adLocationSelect');
    const inp = document.getElementById('adLocationInput');
    const add = document.getElementById('addLocationLink');
    const cancel = document.getElementById('cancelNewLocationLink');
    if (!sel || !inp || !add || !cancel) return;

    function useInputMode(){
      sel.classList.add('d-none');
      sel.removeAttribute('name');
      inp.classList.remove('d-none');
      inp.setAttribute('name','location');
      cancel.classList.remove('d-none');
      add.classList.add('d-none');
      inp.focus();
    }
    function useSelectMode(){
      inp.classList.add('d-none');
      inp.removeAttribute('name');
      sel.classList.remove('d-none');
      sel.setAttribute('name','location');
      add.classList.remove('d-none');
      cancel.classList.add('d-none');
    }

    add.addEventListener('click', function(e){
      e.preventDefault();
      useInputMode();
    });
    cancel.addEventListener('click', function(e){
      e.preventDefault();
      useSelectMode();
    });

    const form = sel.closest('form');
    if (form){
      form.addEventListener('submit', function(){
        if (!inp.classList.contains('d-none')){
          const val = inp.value.trim();
          if (val){
            const opt = document.createElement('option');
            opt.value = val; opt.textContent = val; opt.selected = true;
            sel.appendChild(opt);
          }
        }
      });
    }
  })();
</script>
</body>
</html>
