<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkAdminLogin();

$admin_name = $_SESSION['admin_name'];
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = sanitize($_POST['nama']);
    $merek = sanitize($_POST['merek']);
    $harga = sanitize($_POST['harga']);
    $deskripsi = sanitize($_POST['deskripsi']);
    $warna_arr = isset($_POST['warna']) ? $_POST['warna'] : [];
    
    // Validasi
    if (empty($nama) || empty($merek) || empty($harga) || empty($deskripsi)) {
        $error = 'Semua field harus diisi!';
    } elseif (empty($warna_arr)) {
        $error = 'Minimal harus ada satu warna!';
    } else {
        $gambar = '';
        
        // Handle upload gambar
        if (isset($_FILES['gambar']) && $_FILES['gambar']['size'] > 0) {
            $upload = uploadImage($_FILES['gambar']);
            if (!$upload['success']) {
                $error = $upload['message'];
            } else {
                $gambar = $upload['path'];
            }
        }
        
        if (empty($error)) {
            // Insert produk
            $query = "INSERT INTO produk (nama, merek, harga, deskripsi, gambar) 
                      VALUES ('$nama', '$merek', '$harga', '$deskripsi', '$gambar')";
            
            if (executeQuery($query)) {
                $produk_id = $conn->insert_id;
                
                // Insert warna
                foreach ($warna_arr as $warna) {
                    $warna = sanitize(trim($warna));
                    if (!empty($warna)) {
                        $query_warna = "INSERT INTO warna_produk (produk_id, warna) VALUES ('$produk_id', '$warna')";
                        executeQuery($query_warna);
                    }
                }
                
                $success = 'Produk berhasil ditambahkan!';
                // Reset form
                $nama = $merek = $harga = $deskripsi = '';
                $warna_arr = ['', '', ''];
            } else {
                $error = 'Gagal menambahkan produk!';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Aster Wear Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background-color: var(--light-gray);">
    <!-- Navbar Admin -->
    <nav class="navbar navbar-dark bg-dark sticky-top py-3 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4 text-white" href="dashboard.php">
                <?php if (!empty($pengaturan['logo'])): ?><img src="../<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo" class="brand-mark me-2"><?php else: ?><i class="bi bi-droplet-half text-info me-2"></i><?php endif; ?><?php echo htmlspecialchars($pengaturan['nama_perusahaan'] ?? 'Aster Color'); ?> Admin
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
                        <a href="tambah_produk.php" class="nav-link active">
                            <i class="bi bi-plus-circle me-2"></i> Tambah Produk
                        </a>
                        <hr class="my-2">
                        <a href="pengaturan.php" class="nav-link">
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
                        <h2 class="fw-bold m-0">Tambah Produk Baru</h2>
                        <p class="text-muted small m-0">Isi formulir di bawah untuk menambahkan produk baru</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i> <?php echo $success; ?>
                            <a href="produk.php" class="ms-2">Lihat daftar produk</a>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-4">
                            <!-- Kolom Kiri -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Informasi Produk</h5>
                                
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $nama ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="merek" class="form-label">Merek <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="merek" name="merek" value="<?php echo $merek ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $harga ?? ''; ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi Produk <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6" required><?php echo $deskripsi ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-lg-6">
                                <h5 class="fw-bold mb-3">Gambar & Warna</h5>

                                <div class="mb-3">
                                    <label for="gambar" class="form-label">Gambar Produk</label>
                                    <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                    <small class="text-muted">Format: JPG, PNG, GIF (Max 5MB)</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Warna Produk <span class="text-danger">*</span></label>
                                    <div id="warna-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="warna[]" placeholder="Masukkan warna" value="">
                                            <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="warna[]" placeholder="Masukkan warna" value="">
                                            <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="warna[]" placeholder="Masukkan warna" value="">
                                            <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWarna()">
                                        <i class="bi bi-plus-lg me-1"></i> Tambah Warna
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2 justify-content-end">
                            <a href="produk.php" class="btn btn-secondary-custom btn-modern">
                                <i class="bi bi-x-lg me-2"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary-custom btn-modern">
                                <i class="bi bi-check-lg me-2"></i> Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addWarna() {
            const container = document.getElementById('warna-container');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="warna[]" placeholder="Masukkan warna">
                <button class="btn btn-outline-danger" type="button" onclick="this.parentElement.remove()">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            container.appendChild(div);
        }
    </script>
</body>
</html>
