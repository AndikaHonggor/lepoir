<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkAdminLogin();

$admin_name = $_SESSION['admin_name'];
$error = '';
$success = '';
$admin = fetchSingleData("SELECT * FROM admin WHERE id = '" . sanitize($_SESSION['admin_id']) . "'");

// Ambil pengaturan saat ini
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
if (!$pengaturan) {
    // Jika belum ada, buat yang baru
    executeQuery("INSERT INTO pengaturan (nama_perusahaan) VALUES ('LE'POIR')");
    $pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_perusahaan = sanitize($_POST['nama_perusahaan']);
    $deskripsi_perusahaan = sanitize($_POST['deskripsi_perusahaan']);
    $whatsapp = sanitize($_POST['whatsapp']);
    $instagram = sanitize($_POST['instagram']);
    $facebook = sanitize($_POST['facebook']);
    $tiktok = sanitize($_POST['tiktok']);
    $email = sanitize($_POST['email']);
    $new_admin_email = sanitize($_POST['admin_email']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $logo = $pengaturan['logo'] ?? '';

    if (isset($_FILES['logo']) && $_FILES['logo']['size'] > 0) {
        $upload_logo = uploadLogo($_FILES['logo']);
        if (!$upload_logo['success']) {
            $error = $upload_logo['message'];
        } else {
            if (!empty($logo)) {
                deleteImage(basename($logo));
            }
            $logo = $upload_logo['path'];
        }
    }
    
    if (empty($nama_perusahaan)) {
        $error = 'Nama perusahaan tidak boleh kosong!';
    } elseif (!filter_var($new_admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email admin tidak valid!';
    } elseif (($new_admin_email !== $admin['email'] || !empty($new_password)) && !verifyPassword($current_password, $admin['password'])) {
        $error = 'Password saat ini salah!';
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = 'Konfirmasi password baru tidak cocok!';
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        $query = "UPDATE pengaturan SET 
                  nama_perusahaan = '$nama_perusahaan',
                  deskripsi_perusahaan = '$deskripsi_perusahaan',
                  whatsapp = '$whatsapp',
                  instagram = '$instagram',
                  facebook = '$facebook',
                  tiktok = '$tiktok',
                  email = '$email',
                  logo = '$logo'
                  WHERE id = '" . $pengaturan['id'] . "'";
        
        if (executeQuery($query)) {
            $password_sql = !empty($new_password) ? ", password = '" . sanitize(hashPassword($new_password)) . "'" : '';
            $admin_updated = executeQuery("UPDATE admin SET email = '$new_admin_email' $password_sql WHERE id = '" . $admin['id'] . "'");
            if ($admin_updated) {
                $_SESSION['admin_email'] = $new_admin_email;
                $admin['email'] = $new_admin_email;
                if (!empty($new_password)) {
                    $admin['password'] = hashPassword($new_password);
                }
                $success = 'Pengaturan berhasil disimpan!';
            } else {
                $error = 'Profil tersimpan, tetapi akun admin gagal diperbarui. Pastikan email belum digunakan.';
            }
            // Refresh data
            $pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
        } else {
            $error = 'Gagal menyimpan pengaturan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Perusahaan - LE'POIR Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background-color: var(--light-gray);">
    <!-- Navbar Admin -->
    <a class="navbar-brand fw-bold fs-4 text-white" href="dashboard.php">
    <?php if (!empty($pengaturan['logo'])): ?>
        <img src="../<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo" class="brand-mark me-2">
    <?php else: ?>
        <i class="bi bi-droplet-half text-info me-2"></i>
    <?php endif; ?>
    <?php echo htmlspecialchars($pengaturan['nama_perusahaan'] ?? "LE'POIR"); ?> Admin
</a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-light text-sm">Halo, <strong><?php echo $admin_name; ?></strong></span>
                <button class="btn btn-outline-light btn-sm" onclick="if(confirm('Yakin ingin logout?')) window.location='../logout.php'">
                    <i class="bi bi-door-open me-1"></i> Logout
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 mb-3 mb-md-0">
                <div class="admin-sidebar">
                    <nav class="nav flex-column">
                        <a href="dashboard.php" class="nav-link">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a href="produk.php" class="nav-link">
                            <i class="bi bi-box-seam me-2"></i> Kelola Produk
                        </a>
                        <a href="tambah_produk.php" class="nav-link">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Produk
                        </a>
                        <hr class="my-2">
                        <a href="pengaturan.php" class="nav-link active">
                            <i class="bi bi-gear me-2"></i> Pengaturan Perusahaan
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="admin-content">
                    <!-- Header -->
                    <div class="mb-4">
                        <h2 class="fw-bold m-0">Pengaturan Perusahaan</h2>
                        <p class="text-muted small m-0">Kelola informasi perusahaan dan media sosial</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-4">
                            <!-- Kolom Kiri -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Informasi Perusahaan</h5>
                                
                                <div class="mb-3">
                                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?php echo $pengaturan['nama_perusahaan'] ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi_perusahaan" class="form-label">Deskripsi Perusahaan</label>
                                    <textarea class="form-control" id="deskripsi_perusahaan" name="deskripsi_perusahaan" rows="5"><?php echo $pengaturan['deskripsi_perusahaan'] ?? ''; ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="logo" class="form-label">Logo Perusahaan</label>
                                    <?php if (!empty($pengaturan['logo'])): ?>
                                        <div class="mb-2"><img src="../<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo perusahaan" class="brand-mark"></div>
                                    <?php endif; ?>
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    <small class="text-muted">PNG transparan direkomendasikan.</small>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Media Sosial & Kontak</h5>
                                
                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label">
                                        <i class="bi bi-whatsapp text-success me-2"></i> WhatsApp
                                    </label>
                                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="628xxxxxxxxx" value="<?php echo $pengaturan['whatsapp'] ?? ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="instagram" class="form-label">
                                        <i class="bi bi-instagram text-danger me-2"></i> Instagram
                                    </label>
                                    <input type="text" class="form-control" id="instagram" name="instagram" placeholder="username atau URL Instagram" value="<?php echo $pengaturan['instagram'] ?? ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="facebook" class="form-label">
                                        <i class="bi bi-facebook text-primary me-2"></i> Facebook
                                    </label>
                                    <input type="text" class="form-control" id="facebook" name="facebook" placeholder="username atau URL Facebook" value="<?php echo $pengaturan['facebook'] ?? ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="tiktok" class="form-label">
                                        <i class="bi bi-tiktok text-dark me-2"></i> TikTok
                                    </label>
                                    <input type="text" class="form-control" id="tiktok" name="tiktok" placeholder="username atau URL TikTok" value="<?php echo $pengaturan['tiktok'] ?? ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="bi bi-envelope-fill text-info me-2"></i> Email
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="email@perusahaan.com" value="<?php echo $pengaturan['email'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Akun Administrator</h5>
                                <div class="mb-3">
                                    <label for="admin_email" class="form-label">Email Login</label>
                                    <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" placeholder="Wajib diisi jika mengubah akun">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Ganti Password</h5>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" minlength="6" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" autocomplete="new-password">
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="dashboard.php" class="btn btn-secondary-custom btn-modern">
                                <i class="bi bi-x-lg me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary-custom btn-modern">
                                <i class="bi bi-check-lg me-2"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
