<?php
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
              <div class="text-muted small mt-1">Isi detail iklan Anda. Ini hanya template.</div>
            </div>

            <form>
              <div class="row g-3">
                <div class="col-12">
                  <label for="adTitle" class="form-label">Judul Iklan</label>
                  <input type="text" class="form-control" id="adTitle" placeholder="Contoh: iPhone 13 Pro 128GB Mulus">
                </div>

                <div class="col-12 col-md-6">
                  <label for="adCategory" class="form-label">Kategori</label>
                  <select id="adCategory" class="form-select">
                    <option value="">Pilih kategori</option>
                    <option value="1">Elektronik</option>
                    <option value="2">Kendaraan</option>
                    <option value="3">Properti</option>
                    <option value="4">Fashion</option>
                    <option value="5">Komputer</option>
                    <option value="6">Hobi & Olahraga</option>
                  </select>
                </div>

                <div class="col-12 col-md-6">
                  <label for="adPrice" class="form-label">Harga</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" class="form-control" id="adPrice" placeholder="0" min="0" step="1000">
                  </div>
                </div>

                <div class="col-12 col-md-6">
                  <label for="adCondition" class="form-label">Kondisi</label>
                  <select id="adCondition" class="form-select">
                    <option value="">Pilih kondisi</option>
                    <option value="new">Baru</option>
                    <option value="used">Bekas</option>
                  </select>
                </div>

                <div class="col-12">
                  <label for="adDescription" class="form-label">Deskripsi</label>
                  <textarea id="adDescription" class="form-control" rows="6" placeholder="Tulis deskripsi lengkap tentang iklan Anda..."></textarea>
                </div>

                <div class="col-12">
                  <label for="adPhotos" class="form-label">Foto</label>
                  <input class="form-control" type="file" id="adPhotos" multiple>
                  <div class="form-text">Unggah hingga beberapa foto. Ini hanya placeholder, belum ada proses unggah.</div>
                </div>

                <div class="col-12 col-md-6">
                  <label for="adLocation" class="form-label">Lokasi</label>
                  <input type="text" class="form-control" id="adLocation" placeholder="Kota/Kabupaten">
                </div>

                <div class="col-12 col-md-6">
                  <label for="adContact" class="form-label">Kontak</label>
                  <input type="text" class="form-control" id="adContact" placeholder="Nomor WhatsApp atau telepon">
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                  <button type="button" class="btn btn-outline-secondary"><i class="bi bi-eye me-1"></i>Pratinjau</button>
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
</body>
</html>
