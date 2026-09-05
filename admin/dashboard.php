<?php
require_once '../config/database.php';
require_once '../config/functions.php';

// Check admin login
checkAdminLogin();

// Ambil informasi admin
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");

// Ambil statistik
$total_produk_query = "SELECT COUNT(*) as total FROM produk";
$total_produk_result = executeQuery($total_produk_query);
$total_produk = $total_produk_result->fetch_assoc()['total'];

// Ambil daftar produk
$products_query = "SELECT p.*, COUNT(wp.id) as jumlah_warna 
                   FROM produk p 
                   LEFT JOIN warna_produk wp ON p.id = wp.produk_id 
                   GROUP BY p.id 
                   ORDER BY p.created_at DESC";
$products = fetchData($products_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - LE'POIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body style="background-color: var(--light-gray);">
    <!-- Navbar Admin -->
    <nav class="navbar navbar-dark bg-dark sticky-top py-3 shadow-sm">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Elemen Kiri: Logo & Nama Brand -->
        <a class="navbar-brand fw-bold fs-4 text-white" href="dashboard.php">
            <?php if (!empty($pengaturan['logo'])): ?>
                <img src="../<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo" class="brand-mark me-2">
            <?php else: ?>
                <i class="bi bi-droplet-half text-info me-2"></i>
            <?php endif; ?>
            <?php echo htmlspecialchars($pengaturan['nama_perusahaan'] ?? "LE'POIR Color"); ?> Admin
        </a>

        <!-- Elemen Kanan: User Info & Tombol Logout -->
        <div class="d-flex align-items-center gap-3">
            <span class="text-light text-sm">Hallo, <strong><?php echo htmlspecialchars($admin_name, ENT_QUOTES, 'UTF-8'); ?></strong></span>
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
                        <a href="dashboard.php" class="nav-link active">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a href="produk.php" class="nav-link">
                            <i class="bi bi-box-seam me-2"></i> Kelola Produk
                        </a>
                        <a href="tambah_produk.php" class="nav-link">
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold m-0">Dashboard</h2>
                            <p class="text-muted small m-0">Selamat datang di Panel admin LE'POIR</p>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm" style="border-left: 4px solid var(--primary-blue);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted small mb-1">Total Produk</p>
                                            <h3 class="fw-bold mb-0"><?php echo $total_produk; ?></h3>
                                        </div>
                                        <i class="bi bi-box-seam text-primary" style="font-size: 2rem; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm" style="border-left: 4px solid #16a34a;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted small mb-1">Status Website</p>
                                            <h3 class="fw-bold mb-0">Online</h3>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm" style="border-left: 4px solid #f59e0b;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted small mb-1">Akun Admin</p>
                                            <h3 class="fw-bold mb-0">1 Aktif</h3>
                                        </div>
                                        <i class="bi bi-person-fill text-warning" style="font-size: 2rem; opacity: 0.5;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3">Aksi Cepat</h5>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <a href="tambah_produk.php" class="btn btn-primary-custom btn-modern w-100 py-3">
                                    <i class="bi bi-plus-lg me-2"></i> Tambah Produk Baru
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="produk.php" class="btn btn-secondary-custom btn-modern w-100 py-3">
                                    <i class="bi bi-list-ul me-2"></i> Lihat Semua Produk
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="pengaturan.php" class="btn btn-secondary-custom btn-modern w-100 py-3">
                                    <i class="bi bi-gear me-2"></i> Atur Perusahaan
                                </a>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <a href="../index.php" class="btn btn-secondary-custom btn-modern w-100 py-3" target="_blank">
                                    <i class="bi bi-eye me-2"></i> Lihat Website
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Products -->
                    <div>
                        <h5 class="fw-bold mb-3">Produk Terbaru</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Merek</th>
                                        <th>Harga</th>
                                        <th>Warna</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($products) > 0): ?>
                                        <?php foreach (array_slice($products, 0, 5) as $product): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo $product['nama']; ?></strong>
                                                </td>
                                                <td><?php echo $product['merek']; ?></td>
                                                <td><?php echo formatRupiah($product['harga']); ?></td>
                                                <td>
                                                    <span class="badge bg-info"><?php echo $product['jumlah_warna']; ?> warna</span>
                                                </td>
                                                <td>
                                                    <a href="edit_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary-custom">Edit</a>
                                                    <a href="hapus_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger-custom" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Belum ada produk. <a href="tambah_produk.php">Tambah sekarang</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
