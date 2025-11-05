<?php
?><!DOCTYPE html>
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
        <li class="nav-item"><a class="nav-link" href="#">Kategori</a></li>
        <li class="nav-item"><a class="nav-link" href="#">Iklan Terbaru</a></li>
      </ul>
      <div class="d-flex gap-2">
        <a href="#" class="btn btn-outline-primary">Masuk</a>
        <a href="#" class="btn btn-primary">Pasang Iklan</a>
      </div>
    </div>
  </div>
</nav>

<section class="py-3 bg-white border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb small m-0">
        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Beranda</a></li>
        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Kategori</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Iklan</li>
      </ol>
    </nav>
  </div>
</section>

<section class="py-4">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-7">
        <div class="card card-hover">
          <div class="ratio ratio-4x3 bg-light">
            <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1400&auto=format&fit=crop" alt="Gambar Utama" class="w-100 h-100 rounded-top-2" style="object-fit:cover">
          </div>
          <div class="p-3">
            <div class="row g-2">
              <div class="col-3">
                <img class="w-100 rounded thumb" src="https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=600&auto=format&fit=crop" alt="thumb 1">
              </div>
              <div class="col-3">
                <img class="w-100 rounded thumb" src="https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=600&auto=format&fit=crop" alt="thumb 2">
              </div>
              <div class="col-3">
                <img class="w-100 rounded thumb" src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=600&auto=format&fit=crop" alt="thumb 3">
              </div>
              <div class="col-3">
                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded border">+2</div>
              </div>
            </div>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body">
            <h2 class="h5 mb-3">Deskripsi</h2>
            <p class="mb-2">Teks deskripsi iklan ditempatkan di sini. Ini hanya placeholder untuk konten yang akan datang.</p>
            <ul class="mb-0">
              <li>Kondisi: Placeholder</li>
              <li>Kelengkapan: Placeholder</li>
              <li>Catatan: Placeholder</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card card-hover">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h1 class="h5 m-0">Judul Iklan Placeholder</h1>
              <div class="price">Rp 0</div>
            </div>
            <div class="text-muted small mb-3"><i class="bi bi-geo-alt me-1"></i>Kota • Waktu lalu</div>
            <div class="d-grid gap-2">
              <button class="btn btn-primary"><i class="bi bi-whatsapp me-1"></i>Chat Penjual</button>
              <button class="btn btn-outline-primary"><i class="bi bi-telephone me-1"></i>Telepon</button>
              <button class="btn btn-outline-secondary"><i class="bi bi-share me-1"></i>Bagikan</button>
            </div>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body d-flex align-items-center gap-3">
            <img src="https://ui-avatars.com/api/?name=Seller&background=0D6EFD&color=fff" width="56" height="56" class="rounded-circle" alt="Seller">
            <div class="flex-grow-1">
              <div class="fw-semibold">Nama Penjual</div>
              <div class="text-muted small">Bergabung: 2024</div>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm">Lihat Profil</a>
          </div>
        </div>

        <div class="card mt-3 card-hover">
          <div class="card-body">
            <h3 class="h6">Lokasi</h3>
            <div class="ratio ratio-16x9 bg-light rounded">
              <div class="d-flex align-items-center justify-content-center text-muted">Map Placeholder</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 m-0">Iklan Serupa</h2>
        <a href="#" class="text-decoration-none">Lihat semua</a>
      </div>
      <div class="row g-3">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100 card-hover">
            <img class="card-img-top ad-img" src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop" alt="Ad Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 mb-1">Judul Placeholder</h3>
                <div class="price">Rp 0</div>
              </div>
              <div class="text-muted small">Kota • waktu lalu</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100 card-hover">
            <img class="card-img-top ad-img" src="https://images.unsplash.com/photo-1512496015851-a90fb38ba796?q=80&w=1200&auto=format&fit=crop" alt="Ad Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 mb-1">Judul Placeholder</h3>
                <div class="price">Rp 0</div>
              </div>
              <div class="text-muted small">Kota • waktu lalu</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100 card-hover">
            <img class="card-img-top ad-img" src="https://images.unsplash.com/photo-1493238792000-8113da705763?q=80&w=1200&auto=format&fit=crop" alt="Ad Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 mb-1">Judul Placeholder</h3>
                <div class="price">Rp 0</div>
              </div>
              <div class="text-muted small">Kota • waktu lalu</div>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100 card-hover">
            <img class="card-img-top ad-img" src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?q=80&w=1200&auto=format&fit=crop" alt="Ad Image">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <h3 class="h6 mb-1">Judul Placeholder</h3>
                <div class="price">Rp 0</div>
              </div>
              <div class="text-muted small">Kota • waktu lalu</div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

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
