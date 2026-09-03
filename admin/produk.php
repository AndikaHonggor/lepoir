<?php
require_once '../config/database.php';
require_once '../config/functions.php';

checkAdminLogin();

$admin_name = $_SESSION['admin_name'];
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");

// Ambil daftar produk dengan warna
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
    <title>Kelola Produk - Aster Wear Admin</title>
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
                        <a href="produk.php" class="nav-link active">
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
                            <h2 class="fw-bold m-0">Kelola Produk</h2>
                            <p class="text-muted small m-0">Daftar semua produk yang tersedia</p>
                        </div>
                        <a href="tambah_produk.php" class="btn btn-primary-custom btn-modern">
                            <i class="bi bi-plus-lg me-2"></i> Tambah Produk
                        </a>
                    </div>

                    <!-- Product Table -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Merek</th>
                                    <th>Harga</th>
                                    <th>Warna</th>
                                    <th>Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($products) > 0): ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($product['gambar'])): ?>
                                                        <?php $gambar_url = strpos($product['gambar'], 'http') === 0 ? $product['gambar'] : '../' . ltrim($product['gambar'], '/'); ?>
                                                        <img src="<?php echo htmlspecialchars($gambar_url); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div style="display: none; width: 40px; height: 40px; background-color: var(--card-border); border-radius: 6px; align-items: center; justify-content: center;"><i class="bi bi-image text-muted"></i></div>
                                                    <?php else: ?>
                                                        <div style="width: 40px; height: 40px; background-color: var(--card-border); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <strong><?php echo $product['nama']; ?></strong>
                                                </div>
                                            </td>
                                            <td><?php echo $product['merek']; ?></td>
                                            <td><?php echo formatRupiah($product['harga']); ?></td>
                                            <td><span class="badge bg-info"><?php echo $product['jumlah_warna']; ?> warna</span></td>
                                            <td><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></td>
                                            <td>
                                                <a href="edit_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary-custom">
                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                </a>
                                                <a href="hapus_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger-custom" onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Tidak ada produk. <a href="tambah_produk.php">Tambah produk pertama Anda</a>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
