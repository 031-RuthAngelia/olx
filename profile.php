<?php
session_start();
require_once __DIR__ . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User tidak ditemukan";
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Saya - FLX</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{background-color:#f5f7f9}
    .navbar-brand{font-weight:700}
    .profile-container{
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      padding: 2rem;
      margin-top: 2rem;
    }
    .profile-img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #0d6efd;
      margin: 0 auto 1.5rem;
      display: block;
    }
    .form-label {
      font-weight: 500;
    }
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
            <span class="align-self-center text-muted small d-none d-md-inline">Halo, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Pengguna', ENT_QUOTES, 'UTF-8'); ?></span>
            <a href="profile.php" class="btn btn-outline-secondary active">Profil Saya</a>
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

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="profile-container">
          <h2 class="h4 mb-4">Profil Saya</h2>
          
          <?php if (isset($_SESSION['success'])): ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['success']); ?>
          <?php endif; ?>
          
          <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['error']); ?>
          <?php endif; ?>
          
          <form action="update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="text-center mb-4">
              <img src="<?php echo !empty($user['profile_image']) ? 'uploads/profiles/' . htmlspecialchars($user['profile_image']) : 'https://via.placeholder.com/150?text=FLX'; ?>" 
                   alt="Foto Profil" class="profile-img" id="profileImage">
              <input type="file" class="form-control d-none" id="profileImageInput" name="profile_image" accept="image/*">
              <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('profileImageInput').click()">
                <i class="bi bi-camera me-1"></i> Ganti Foto
              </button>
            </div>
            
            <div class="mb-3">
              <label for="name" class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label">Alamat Email</label>
              <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
              <div class="form-text text-muted">Email tidak dapat diubah</div>
            </div>
            
            <div class="card mb-4">
              <div class="card-header bg-light">
                <h5 class="mb-0">Ganti Password</h5>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label for="current_password" class="form-label">Password Saat Ini</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Kosongkan jika tidak ingin mengubah">
                </div>
                
                <div class="mb-3">
                  <label for="new_password" class="form-label">Password Baru</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Minimal 8 karakter">
                </div>
                
                <div class="mb-0">
                  <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                </div>
              </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
              <a href="index.php" class="btn btn-outline-secondary me-md-2">Kembali</a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Preview profile image before upload
    document.getElementById('profileImageInput').addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
          alert('Hanya file gambar (JPG, PNG, GIF) yang diizinkan');
          return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          alert('Ukuran file maksimal 2MB');
          return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('profileImage').src = e.target.result;
        }
        reader.readAsDataURL(file);
      }
    });
  </script>
</body>
</html>
