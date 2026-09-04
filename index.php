<?php
require_once 'config/database.php';
require_once 'config/functions.php';

// Ambil semua produk
$products_query = "SELECT * FROM produk ORDER BY id DESC";
$products = fetchData($products_query);

// Ambil pengaturan perusahaan
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");
if (!$pengaturan) {
    // Nilai default jika belum ada
    $pengaturan = [
        'nama_perusahaan' => 'LEPOIR',
        'deskripsi_perusahaan' => 'Penyedia pakaian berkualitas premium',
        'whatsapp' => '6281234567890',
        'instagram' => 'instagram.com',
        'facebook' => 'facebook.com',
        'tiktok' => 'tiktok.com',
        'email' => 'info@asterwear.com',
        'logo' => ''
    ];
}

// Fungsi untuk mengambil warna produk
function getProductColors($produk_id) {
    $colors = fetchData("SELECT warna FROM warna_produk WHERE produk_id = '$produk_id' ORDER BY id ASC");
    return $colors;
}

function getColorHex($color) {
    $color = strtolower(trim($color));
    $map = [
        'hitam' => '#171717', 'black' => '#171717', 'cokelat' => '#7a4b2a', 'brunette' => '#5c321e',
        'pirang' => '#e5b94d', 'blonde' => '#e5b94d', 'merah' => '#c62828', 'red' => '#c62828',
        'ungu' => '#7e3f98', 'purple' => '#7e3f98', 'biru' => '#2563eb', 'blue' => '#2563eb',
        'abu-abu' => '#94a3b8', 'grey' => '#94a3b8', 'gray' => '#94a3b8', 'putih' => '#f8fafc',
        'pink' => '#ec78a5', 'orange' => '#ed8936', 'hijau' => '#3f8f58', 'olive' => '#71834b',
        'navy' => '#172554', 'krem' => '#ead9bd', 'mocca' => '#a67b5b'
    ];
    return $map[$color] ?? '#155eef';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pengaturan['nama_perusahaan']; ?> - Katalog Produk</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="catalog-page">
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top py-3 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 text-white" href="index.php">
                <?php if (!empty($pengaturan['logo'])): ?><img src="<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo LE'POIR" class="brand-mark me-2"><?php else: ?><i class="bi bi-droplet-half text-info me-2"></i><?php endif; ?>LE'POIR
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto fs-6">
                    <li class="nav-item">
                        <a class="nav-link active fw-semibold" href="#katalog">Katalog Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="katalog.php">Lihat Semua</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warna.php">Panduan Warna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="#kontak">Kontak Perusahaan</a>
                    </li>
                    <?php if (isset($_SESSION['admin_id']) && $_SESSION['admin_id']): ?>
                        <li class="nav-item">
                            <a class="nav-link text-secondary" href="admin/dashboard.php">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <!-- HERO SECTION -->
        <section class="hero-section text-center mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 fw-bold mb-3">LE'POIR</h1>
                        <p class="lead opacity-90 mb-0"><?php echo $pengaturan['deskripsi_perusahaan'] ?? 'Temukan pilihan cat rambut berkualitas untuk warna yang hidup, berani, dan sesuai karakter Anda.'; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- KATALOG PRODUK -->
        <section id="katalog" class="container my-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                            <h3 class="fw-bold m-0">Pilihan Warna Cat Rambut</h3>
                            <p class="text-muted small m-0">Temukan shade favorit untuk penampilan baru Anda</p>
                </div>
            </div>

            <div class="row g-4" id="productGrid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): 
                        $colors = getProductColors($product['id']);
                    ?>
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="card product-card h-100">
                                <div class="card-img-wrapper">
                                    <?php if (!empty($product['gambar'])): ?>
                                        <img src="<?php echo $product['gambar']; ?>" alt="<?php echo $product['nama']; ?>" onerror="this.src='https://via.placeholder.com/300?text=No+Image'">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/300?text=No+Image" alt="<?php echo $product['nama']; ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <span class="card-brand"><?php echo $product['merek']; ?></span>
                                    <h5 class="card-title fw-bold mb-2"><?php echo $product['nama']; ?></h5>
                                    <p class="card-price"><?php echo formatRupiah($product['harga']); ?></p>
                                    
                                    <div class="mb-4">
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($colors as $color): ?>
                                                <span class="badge badge-color"><span class="color-dot" style="background-color: <?php echo getColorHex($color['warna']); ?>"></span><?php echo htmlspecialchars($color['warna']); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <button onclick="showDetail(<?php echo $product['id']; ?>)" class="btn btn-primary-custom btn-modern mt-auto w-100">
                                        Lihat Detail
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: var(--text-muted);"></i>
                            <p class="text-muted mt-3">Belum ada produk tersedia</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- MODAL DETAIL PRODUK -->
    <div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-md-6 bg-light">
                            <img id="modalImage" src="" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 350px;" alt="Detail Produk">
                        </div>
                        <div class="col-md-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span id="modalBrand" class="badge bg-secondary text-uppercase tracking-wider">Merek</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <h3 id="modalTitle" class="fw-bold mb-2">Nama Produk</h3>
                                <h4 id="modalPrice" class="text-primary fw-bold mb-4">Rp0</h4>
                                
                                <div class="mb-4">
                                    <h6 class="fw-bold text-uppercase fs-7 text-muted mb-2">Pilihan Warna:</h6>
                                    <div id="modalColors" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-bold text-uppercase fs-7 text-muted mb-2">Deskripsi Produk:</h6>
                                    <p id="modalDescription" class="text-secondary lh-base fs-6">Deskripsi produk...</p>
                                </div>
                            </div>
                            <button class="btn btn-secondary btn-modern w-100" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer id="kontak" class="footer pt-5 pb-4 mt-5">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-6">
                    <h4 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($pengaturan['nama_perusahaan']); ?></h4>
                    <p class="text-secondary mb-0"><?php echo $pengaturan['deskripsi_perusahaan'] ?? 'Penyedia pakaian berkualitas premium'; ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="fw-bold text-white mb-3">Hubungi & Ikuti Kami</h6>
                    <div class="d-flex justify-content-md-end gap-2">
                        <?php if (!empty($pengaturan['whatsapp'])): ?>
                            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $pengaturan['whatsapp']); ?>" target="_blank" class="social-link" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($pengaturan['instagram'])): ?>
                            <a href="https://instagram.com/<?php echo str_replace(['https://instagram.com/', 'instagram.com/'], '', $pengaturan['instagram']); ?>" target="_blank" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($pengaturan['facebook'])): ?>
                            <a href="https://facebook.com/<?php echo str_replace(['https://facebook.com/', 'facebook.com/'], '', $pengaturan['facebook']); ?>" target="_blank" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($pengaturan['tiktok'])): ?>
                            <a href="https://tiktok.com/@<?php echo str_replace(['https://tiktok.com/@', 'tiktok.com/@'], '', $pengaturan['tiktok']); ?>" target="_blank" class="social-link" title="TikTok"><i class="bi bi-tiktok"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($pengaturan['email'])): ?>
                            <a href="mailto:<?php echo $pengaturan['email']; ?>" class="social-link" title="Email"><i class="bi bi-envelope-fill"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary opacity-25">
            <div class="text-center text-secondary small">
               All Rights Reserved &copy;2026  <?php echo $pengaturan['nama_perusahaan'];  ?>.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script Data & Operasional Katalog -->
    <script>
        // Data dari PHP/Database
        const productsData = [
            <?php foreach ($products as $product): 
                $colors = getProductColors($product['id']);
                $color_array = array_map(function($c) { return '"' . $c['warna'] . '"'; }, $colors);
            ?>
            {
                id: <?php echo $product['id']; ?>,
                name: "<?php echo addslashes($product['nama']); ?>",
                brand: "<?php echo addslashes($product['merek']); ?>",
                price: <?php echo $product['harga']; ?>,
                colors: [<?php echo implode(', ', $color_array); ?>],
                image: "<?php echo $product['gambar']; ?>",
                description: "<?php echo addslashes($product['deskripsi']); ?>"
            },
            <?php endforeach; ?>
        ];

        // Format Angka ke Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        };

        // Tampilkan Modal Detail
        function showDetail(productId) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;

            document.getElementById('modalImage').src = product.image || 'https://via.placeholder.com/300?text=No+Image';
            document.getElementById('modalBrand').innerText = product.brand;
            document.getElementById('modalTitle').innerText = product.name;
            document.getElementById('modalPrice').innerText = formatRupiah(product.price);
            document.getElementById('modalDescription').innerText = product.description;

            const colorsContainer = document.getElementById('modalColors');
            const colorMap = { hitam: '#171717', cokelat: '#7a4b2a', brunette: '#5c321e', pirang: '#e5b94d', blonde: '#e5b94d', merah: '#c62828', ungu: '#7e3f98', biru: '#2563eb', 'abu-abu': '#94a3b8', putih: '#f8fafc', pink: '#ec78a5', orange: '#ed8936', hijau: '#3f8f58', olive: '#71834b', navy: '#172554', krem: '#ead9bd', mocca: '#a67b5b' };
            colorsContainer.innerHTML = product.colors.map(c => `<span class="badge badge-color fs-6 px-3 py-2"><span class="color-dot" style="background-color: ${colorMap[c.toLowerCase()] || '#155eef'}"></span>${c}</span>`).join(' ');

            const modal = new bootstrap.Modal(document.getElementById('productDetailModal'));
            modal.show();
        }
    </script>
</body>
</html>
