<?php
require_once __DIR__ . '/config.php';
session_start();

if (empty($_SESSION['user_id'])) {
  header('Location: login.php');
  echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=login.php"></head><body><script>window.location.replace("login.php");</script></body></html>';
  exit;
}

$errors = [];
$old = [
  'title' => '',
  'category_id' => '',
  'price' => '',
  'description' => '',
  'location' => '',
];

try {
  $catStmt = $pdo->query('SELECT id, name FROM categories ORDER BY name');
  $categories = $catStmt->fetchAll();
} catch (Throwable $e) {
  $categories = [];
}

// Ambil lokasi unik dari iklan yang sudah ada (tanpa menambah tabel baru)
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

  $uploadedFiles = $_FILES['photos'] ?? null;
  $filesToSave = [];
  if ($uploadedFiles && isset($uploadedFiles['name']) && is_array($uploadedFiles['name'])) {
    for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
      if ($uploadedFiles['error'][$i] === UPLOAD_ERR_NO_FILE) { continue; }
      if ($uploadedFiles['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gagal mengunggah salah satu file.';
        continue;
      }
      $tmp = $uploadedFiles['tmp_name'][$i];
      $name = $uploadedFiles['name'][$i];
      $finfo = @mime_content_type($tmp);
      if (!$finfo || strpos($finfo, 'image/') !== 0) {
        $errors[] = 'File harus berupa gambar.';
        continue;
      }
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
        $errors[] = 'Format gambar tidak didukung.';
        continue;
      }
      $filesToSave[] = [ 'tmp' => $tmp, 'ext' => $ext ];
    }
  }

  if (!$errors) {
    try {
      $pdo->beginTransaction();
      $ins = $pdo->prepare('INSERT INTO ads (user_id, category_id, title, description, price, location) VALUES (?, ?, ?, ?, ?, ?)');
      $ins->execute([
        (int)$_SESSION['user_id'],
        (int)$old['category_id'],
        $old['title'],
        $old['description'] !== '' ? $old['description'] : null,
        number_format((float)$old['price'], 2, '.', ''),
        $old['location'] !== '' ? $old['location'] : null,
      ]);
      $adId = (int)$pdo->lastInsertId();

      if (!empty($filesToSave)) {
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
        $imgIns = $pdo->prepare('INSERT INTO ad_images (ad_id, image_path) VALUES (?, ?)');
        foreach ($filesToSave as $idx => $f) {
          $filename = 'ad_' . $adId . '_' . ($idx+1) . '_' . bin2hex(random_bytes(4)) . '.' . $f['ext'];
          $dest = $uploadDir . DIRECTORY_SEPARATOR . $filename;
          if (!move_uploaded_file($f['tmp'], $dest)) {
            throw new RuntimeException('Gagal menyimpan file gambar.');
          }
          $relPath = 'uploads/' . $filename;
          $imgIns->execute([$adId, $relPath]);
        }
      }

      $pdo->commit();
      header('Location: detail.php?id=' . $adId);
      echo '<!DOCTYPE html><html><head><meta http-equiv="refresh" content="0;url=detail.php?id=' . htmlspecialchars((string)$adId, ENT_QUOTES, 'UTF-8') . '"></head><body><script>window.location.replace("detail.php?id=' . htmlspecialchars((string)$adId, ENT_QUOTES, 'UTF-8') . '");</script></body></html>';
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) { $pdo->rollBack(); }
      $errors[] = 'Terjadi kesalahan saat menyimpan iklan.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pasang Iklan - FLX</title>
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
        <a href="login.php" class="btn btn-outline-primary">Masuk</a>
        <a href="register.php" class="btn btn-outline-primary">Daftar</a>
        <a href="postad.php" class="btn btn-primary active" aria-current="page">Pasang Iklan</a>
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
              <h1 class="h4 m-0">Pasang Iklan</h1>
              <div class="text-muted small mt-1">Isi detail iklan Anda.</div>
            </div>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $err): ?>
                  <div><?php echo htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <form method="post" action="postad.php" enctype="multipart/form-data">
              <div class="row g-3">
                <div class="col-12">
                  <label for="adTitle" class="form-label">Judul Iklan</label>
                  <input type="text" class="form-control" id="adTitle" name="title" placeholder="Contoh: iPhone 13 Pro 128GB Mulus" value="<?php echo htmlspecialchars($old['title'], ENT_QUOTES, 'UTF-8'); ?>">
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
                    <input type="number" class="form-control" id="adPrice" name="price" placeholder="0" min="0" step="0.01" value="<?php echo htmlspecialchars($old['price'], ENT_QUOTES, 'UTF-8'); ?>">
                  </div>
                </div>

                <div class="col-12">
                  <label for="adDescription" class="form-label">Deskripsi</label>
                  <textarea id="adDescription" class="form-control" name="description" rows="6" placeholder="Tulis deskripsi lengkap tentang iklan Anda...">&ZeroWidthSpace;<?php echo htmlspecialchars($old['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <div class="col-12">
                  <label for="adPhotos" class="form-label">Foto</label>
                  <input class="form-control" type="file" id="adPhotos" name="photos[]" multiple accept="image/*">
                  <div class="form-text">Unggah beberapa foto (jpg, jpeg, png, gif, webp).</div>
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
                  <button type="submit" class="btn btn-primary fw-semibold"><i class="bi bi-cloud-upload me-1"></i>Pasang Iklan</button>
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

    // On form submit, if input mode is active, add option to select for consistency
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
