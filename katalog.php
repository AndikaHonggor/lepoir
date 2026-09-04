<?php
require_once 'config/database.php';
require_once 'config/functions.php';

$products = fetchData("SELECT * FROM produk ORDER BY id DESC");
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1") ?: ['nama_perusahaan' => 'LEPOIR', 'logo' => ''];
$isAdmin = isset($_SESSION['admin_id']) && $_SESSION['admin_id'];

function colorHex($color) {
	$map = ['hitam'=>'#171717','black'=>'#171717','cokelat'=>'#7a4b2a','brunette'=>'#5c321e','copper'=>'#b87333','pirang'=>'#e5b94d','blonde'=>'#e5b94d','merah'=>'#c62828','burgundy'=>'#800020','ungu'=>'#7e3f98','biru'=>'#2563eb','navy'=>'#172554','pink'=>'#ec78a5','orange'=>'#ed8936','hijau'=>'#3f8f58','olive'=>'#71834b','abu-abu'=>'#94a3b8','grey'=>'#94a3b8','putih'=>'#f8fafc'];
	return $map[strtolower(trim($color))] ?? '#08abe5';
}

function productColors($id) {
	return fetchData("SELECT warna FROM warna_produk WHERE produk_id = '" . sanitize($id) . "' ORDER BY id ASC");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Katalog Cat Rambut - LE'POIR</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="catalog-page">
<nav class="navbar navbar-dark sticky-top py-3">
	<div class="container">
		<a class="navbar-brand d-flex align-items-center" href="index.php">
			<?php if (!empty($pengaturan['logo'])): ?><img src="<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo LE'POIR" class="brand-mark me-2"><?php else: ?><i class="bi bi-droplet-half text-info me-2"></i><?php endif; ?>
			<span>LE'POIR</span>
		</a>
		<div class="navbar-nav ms-auto align-items-lg-center">
			<a class="nav-link active" href="katalog.php">Katalog</a>
			<a class="nav-link" href="warna.php">Panduan Warna</a>
			<a class="nav-link" href="index.php#kontak">Kontak</a>
			<?php if ($isAdmin): ?><a class="nav-link admin-nav-link" href="admin/dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a><?php endif; ?>
		</div>
	</div>
</nav>

<header class="catalog-header">
	<div class="container py-5">
		<span class="eyebrow">LE'POIR / HAIR COLOR</span>
		<h1 class="display-5 fw-bold mt-2">Warna yang terasa seperti kamu.</h1>
		<p class="lead mb-0">Eksplorasi koleksi cat rambut dengan shade yang berani, lembut, dan mudah dipilih.</p>
	</div>
</header>

<main class="container py-5">
	<section class="catalog-toolbar mb-4" aria-label="Pencarian produk">
		<div><p class="section-kicker mb-1">KOLEKSI LE'POIR</p><h2 class="h3 fw-bold mb-0">Pilih warna favoritmu</h2></div>
		<div class="search-field"><i class="bi bi-search" aria-hidden="true"></i><input id="productSearch" type="search" placeholder="Cari nama produk..." aria-label="Cari nama produk"><button id="clearSearch" type="button" aria-label="Hapus pencarian" title="Hapus pencarian"><i class="bi bi-x-lg"></i></button></div>
	</section>
	<p id="searchResult" class="text-muted small mb-3" aria-live="polite"></p>

	<div class="row g-4" id="productGrid">
		<?php foreach ($products as $product): $colors = productColors($product['id']); ?>
		<div class="col-12 col-sm-6 col-lg-4 product-item" data-product-name="<?php echo htmlspecialchars(strtolower($product['nama'])); ?>">
			<article class="card product-card h-100">
				<button class="card-img-wrapper image-zoom-trigger" type="button" data-image="<?php echo htmlspecialchars($product['gambar'] ?: ''); ?>" data-title="<?php echo htmlspecialchars($product['nama']); ?>" aria-label="Perbesar gambar <?php echo htmlspecialchars($product['nama']); ?>">
					<?php if (!empty($product['gambar'])): ?><img src="<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>" loading="lazy" onerror="this.hidden=true; this.nextElementSibling.hidden=false"><span class="empty-image" hidden><i class="bi bi-image"></i></span><?php else: ?><span class="empty-image"><i class="bi bi-image"></i></span><?php endif; ?><span class="zoom-hint"><i class="bi bi-arrows-fullscreen"></i></span>
				</button>
				<div class="card-body">
					<span class="card-brand"><?php echo htmlspecialchars($product['merek']); ?></span>
					<h2 class="card-title"><?php echo htmlspecialchars($product['nama']); ?></h2>
					<p class="card-price"><?php echo formatRupiah($product['harga']); ?></p>
					<div class="swatch-list mb-3" aria-label="Pilihan warna">
						<?php foreach ($colors as $color): ?><span class="color-swatch" title="<?php echo htmlspecialchars($color['warna']); ?>" style="background: <?php echo colorHex($color['warna']); ?>"></span><?php endforeach; ?>
					</div>
					<a href="detail_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-primary-custom btn-modern mt-auto w-100">Detail Produk <i class="bi bi-arrow-up-right ms-2"></i></a>
				</div>
			</article>
		</div>
		<?php endforeach; ?>
		<div id="emptySearch" class="col-12 empty-search" hidden><i class="bi bi-search"></i><h3 class="h5 mt-3">Produk tidak ditemukan</h3><p class="text-muted mb-0">Coba gunakan nama produk yang lain.</p></div>
	</div>

	<section class="catalog-info mt-5">
		<div class="info-art" aria-hidden="true"><span class="info-icon"><i class="bi bi-gem"></i></span><span class="paint-stroke paint-stroke-one"></span><span class="paint-stroke paint-stroke-two"></span></div>
		<div class="info-copy"><p class="section-kicker mb-2"> LE'POIR COLOR</p><h2 class="h4 fw-bold">Temukan shade yang jadi signature-mu.</h2><p class="mb-3 text-muted">Setiap warna Lepoir hadir untuk menemani ekspresi yang berbeda. Pilih warna favorit, lalu biarkan karaktermu terlihat lebih hidup.</p><div class="info-tags">
		<div class="info-swatch" aria-hidden="true"><span style="background:#08abe5"></span><span style="background:#5bc9e9"></span><span style="background:#c8edf7"></span><span style="background:#0b5278"></span></div>
	</section>
</main>

<footer id="kontak" class="footer mt-5">
	<div class="container"><div class="row gy-4 align-items-center"><div class="col-md-6"><h4 class="fw-bold text-white mb-2">LE'POIR</h4><p class="text-secondary mb-0"><?php echo htmlspecialchars($pengaturan['deskripsi_perusahaan'] ?? 'Warna rambut untuk mengekspresikan karakter Anda.'); ?></p></div><div class="col-md-6 text-md-end"><h6 class="fw-bold text-white mb-3">Jelajahi LE'POIR</h6><p class="mb-2"><a class="footer-link" href="katalog.php">Katalog</a><a class="footer-link" href="warna.php">Panduan Warna</a><a class="footer-link" href="index.php#kontak">Kontak</a></p>
</div>
<hr class="my-4 border-secondary opacity-25">
<div class="text-center text-secondary small">
    &copy; 2026 LE'POIR. All Rights Reserved.
</div>

</div>
</footer>

<div class="modal fade" id="imageZoomModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content image-modal-content"><div class="modal-header border-0"><h2 id="zoomTitle" class="h5 mb-0"></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button></div><div class="modal-body pt-0"><img id="zoomImage" src="" alt="" class="zoom-image"></div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const searchInput = document.getElementById('productSearch');
const productItems = [...document.querySelectorAll('.product-item')];
function filterProducts() {
	const query = searchInput.value.trim().toLowerCase();
	let visible = 0;
	productItems.forEach((item) => { const match = item.dataset.productName.includes(query); item.hidden = !match; if (match) visible += 1; });
	document.getElementById('emptySearch').hidden = visible !== 0;
	document.getElementById('searchResult').textContent = query ? `${visible} produk ditemukan` : `${productItems.length} produk tersedia`;
}
searchInput.addEventListener('input', filterProducts);
document.getElementById('clearSearch').addEventListener('click', () => { searchInput.value = ''; filterProducts(); searchInput.focus(); });
filterProducts();
document.querySelectorAll('.image-zoom-trigger').forEach((trigger) => trigger.addEventListener('click', () => {
	const image = document.getElementById('zoomImage'); image.src = trigger.dataset.image; image.alt = trigger.dataset.title; document.getElementById('zoomTitle').textContent = trigger.dataset.title; new bootstrap.Modal(document.getElementById('imageZoomModal')).show();
}));
</script>
</body>
</html>
