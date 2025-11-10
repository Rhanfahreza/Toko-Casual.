<?php
session_start(); // Baris 1: Selalu buka "ransel" session

// --- INI DIA "PENJAGA"-NYA ---
if (!isset($_SESSION['user_nama'])) {
    header('Location: login.php'); 
    exit;
}
// --- BATAS PENJAGA ---

require_once 'data_produk.php'; 

$jumlah_item_di_keranjang = (isset($_SESSION['keranjang'])) ? array_sum($_SESSION['keranjang']) : 0;
$wishlist_ids = (isset($_SESSION['wishlist'])) ? $_SESSION['wishlist'] : [];
$recently_viewed_ids = (isset($_SESSION['recently_viewed'])) ? $_SESSION['recently_viewed'] : [];

$kategori_produk = [];
foreach ($products as $produk) { $kategori_produk[] = $produk->getKategori(); }
$kategori_unik = array_unique($kategori_produk);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Casual</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* css */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Nunito Sans', sans-serif; background-color: #FAF8F5; color: #4B4B4B; transition: background-color 0.3s ease, color 0.3s ease; }
        .navbar { background-color: #ffffff; padding: 20px 40px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 1000; transition: background-color 0.3s ease; }
        .logo { font-size: 1.8rem; font-weight: 700; color: #5D4037; text-decoration: none; }
        .nav-links { list-style: none; display: flex; align-items: center; gap: 20px; margin: 0; padding: 0; }
        .nav-links li { display: inline-block; }
        .nav-links a { text-decoration: none; color: #555; font-weight: 600; transition: color 0.3s ease; display: flex; align-items: center; gap: 8px; font-size: 1.1rem; }
        .nav-links a:hover { color: #A98A74; }
        .welcome-text { color: #5D4037; font-weight: 600; font-size: 1.1rem; margin-right: 10px; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .section-title { text-align: center; font-size: 2.5rem; margin-bottom: 40px; font-weight: 700; color: #5D4037; }
        .filter-container { margin-bottom: 30px; display: flex; flex-direction: column; gap: 20px; }
        #search-bar { width: 100%; padding: 12px 15px; font-size: 1rem; font-family: 'Nunito Sans', sans-serif; border: 1px solid #F0EAE4; border-radius: 8px; background-color: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); }
        #search-bar:focus { outline: none; border-color: #A98A74; box-shadow: 0 0 0 3px rgba(169, 138, 116, 0.2); }
        .category-buttons { text-align: center; display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; }
        .btn-filter { padding: 8px 16px; font-family: 'Nunito Sans', sans-serif; font-size: 0.9rem; font-weight: 700; border: 2px solid #DCD3CB; background-color: #fff; color: #8C6A5A; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; }
        .btn-filter:hover { background-color: #F0EAE4; border-color: #A98A74; }
        .btn-filter.active { background-color: #A98A74; color: #fff; border-color: #A98A74; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .product-card { background-color: #ffffff; border-radius: 10px; border: 1px solid #F0EAE4; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); overflow: hidden; transition: all 0.3s ease; }
        .product-card.hidden { display: none; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07); }
        .product-image { position: relative; overflow: hidden; }
        .product-image img { width: 100%; height: 350px; object-fit: cover; display: block; }
        .btn-quick-view { position: absolute; top: 10px; right: 10px; background-color: rgba(255, 255, 255, 0.9); color: #5D4037; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 1.2rem; display: flex; justify-content: center; align-items: center; cursor: pointer; text-decoration: none; opacity: 0; transform: scale(0.8); transition: all 0.3s ease; }
        .product-card:hover .btn-quick-view { opacity: 1; transform: scale(1); }
        .btn-quick-view:hover { background-color: #5D4037; color: #fff; }
        .product-info { padding: 20px; }
        .product-name { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; color: #5D4037; }
        .product-price { font-size: 1.1rem; font-weight: 600; color: #8C6A5A; margin-bottom: 0; }
        .btn-add-to-cart { display: block; width: 100%; padding: 12px; background-color: #A98A74; color: #ffffff; text-align: center; text-decoration: none; border: none; border-radius: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-add-to-cart:hover { background-color: #937460; }
        .btn-disabled { background-color: #CFCBC7; cursor: not-allowed; }
        .price-wishlist-wrapper { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .btn-wishlist { font-size: 1.4rem; color: #A98A74; transition: all 0.2s ease; cursor: pointer; }
        .btn-wishlist:hover { color: #e74c3c; transform: scale(1.1); }
        .btn-wishlist .icon-heart-filled { display: none; }
        .btn-wishlist .icon-heart-empty { display: inline-block; }
        .btn-wishlist.active .icon-heart-filled { display: inline-block; color: #e74c3c; }
        .btn-wishlist.active .icon-heart-empty { display: none; }
        .swal2-popup .quick-view-content { display: flex; gap: 20px; text-align: left; }
        .swal2-popup .quick-view-image { width: 40%; flex-shrink: 0; }
        .swal2-popup .quick-view-image img { width: 100%; border-radius: 8px; }
        .swal2-popup .quick-view-details { flex-grow: 1; }
        .swal2-popup .quick-view-details h2 { font-size: 1.5rem; color: #5D4037; margin-bottom: 10px; }
        .swal2-popup .quick-view-details .price { font-size: 1.3rem; font-weight: 700; color: #8C6A5A; margin-bottom: 15px; }
        .swal2-popup .quick-view-details .stock { font-weight: 600; margin-bottom: 15px; }
        .swal2-popup .quick-view-details .stock.available { color: #27ae60; }
        .swal2-popup .quick-view-details .stock.unavailable { color: #e74c3c; }
        .swal2-popup .quick-view-details .description { font-size: 0.95rem; line-height: 1.6; color: #4B4B4B; }
        .recently-viewed-section { background-color: #ffffff; border: 1px solid #F0EAE4; border-radius: 10px; margin-top: 40px; padding: 30px; }
        .recently-viewed-section .section-title { text-align: left; font-size: 1.8rem; margin-bottom: 20px; }
        .recently-viewed-section .product-grid { grid-template-columns: repeat(4, 1fr); overflow-x: auto; }
        #dark-mode-toggle .bi-sun-fill { display: none; }
        body.dark-mode { background-color: #22272E; color: #ADBAC7; }
        body.dark-mode .navbar { background-color: #2D333B; border-bottom: 1px solid #444C56; }
        body.dark-mode .logo { color: #E6EDF3; }
        body.dark-mode .nav-links a { color: #ADBAC7; }
        body.dark-mode .nav-links a:hover { color: #E6EDF3; }
        body.dark-mode .welcome-text { color: #E6EDF3; }
        body.dark-mode .section-title { color: #E6EDF3; }
        body.dark-mode .product-card { background-color: #2D333B; border-color: #444C56; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        body.dark-mode .product-name { color: #E6EDF3; }
        body.dark-mode .product-price { color: #A98A74; }
        body.dark-mode .btn-disabled { background-color: #444C56; color: #ADBAC7; }
        body.dark-mode #dark-mode-toggle .bi-moon-fill { display: none; }
        body.dark-mode #dark-mode-toggle .bi-sun-fill { display: inline-block; }
        body.dark-mode .btn-wishlist { color: #ADBAC7; }
        body.dark-mode .btn-wishlist:hover { color: #e74c3c; }
        body.dark-mode .btn-wishlist.active .icon-heart-filled { color: #e74c3c; }
        body.dark-mode .btn-quick-view { background-color: rgba(45, 51, 59, 0.9); color: #E6EDF3; }
        body.dark-mode .btn-quick-view:hover { background-color: #E6EDF3; color: #2D333B; }
        body.dark-mode .swal2-popup { background: #2D333B; color: #ADBAC7; }
        body.dark-mode .swal2-popup .quick-view-details h2 { color: #E6EDF3; }
        body.dark-mode .swal2-popup .quick-view-details .description { color: #ADBAC7; }
        body.dark-mode .recently-viewed-section { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode #search-bar { background-color: #2D333B; border-color: #444C56; color: #E6EDF3; }
        body.dark-mode #search-bar:focus { border-color: #A98A74; box-shadow: 0 0 0 3px rgba(169, 138, 116, 0.2); }
        body.dark-mode .btn-filter { background-color: #2D333B; border-color: #444C56; color: #ADBAC7; }
        body.dark-mode .btn-filter:hover { background-color: #444C56; }
        body.dark-mode .btn-filter.active { background-color: #A98A74; color: #fff; border-color: #A98A74; }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="index.php" class="logo">CASUALS.</a>
            <ul class="nav-links">
                <li><span class="welcome-text">Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?>!</span></li>
                <li><a href="index.php"><i class="bi bi-house-door-fill"></i> <span class="nav-text">Home</span></a></li>
                <li><a href="keranjang.php"><i class="bi bi-bag"></i> <span class="nav-text">Keranjang (<span id="cart-count"><?php echo $jumlah_item_di_keranjang; ?></span>)</span></a></li>
                <li><a href="wishlist.php" title="Wishlist"><i class="bi bi-heart-fill"></i></a></li>
                
                <li><a href="logout.php" id="logout-link" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a></li>
                
                <li><a href="#" id="dark-mode-toggle" title="Toggle Dark Mode"><i class="bi bi-moon-fill"></i><i class="bi bi-sun-fill"></i></a></li>
            </ul>
        </nav>
    </header>
    
    <main class="container">
        <div class="filter-container">
            <input type="text" id="search-bar" placeholder="Cari nama produk (contoh: 'jeans', 'sepatu', 'jaket')...">
            <div class="category-buttons">
                <button class="btn-filter active" data-filter="all">Semua Kategori</button>
                <?php foreach ($kategori_unik as $kategori): ?>
                    <button class="btn-filter" data-filter="<?php echo htmlspecialchars($kategori); ?>">
                        <?php echo htmlspecialchars($kategori); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <h2 class="section-title">Produk Terbaru</h2>
        <div class="product-grid">
            <?php foreach ($products as $id => $produk): ?>
                <div class="product-card" data-category="<?php echo htmlspecialchars($produk->getKategori()); ?>">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($produk->getImageUrl()); ?>" alt="<?php echo htmlspecialchars($produk->getNama()); ?>">
                        <a href="quick_view_aksi.php?id=<?php echo $id; ?>" class="btn-quick-view" title="Lihat Cepat">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($produk->getNama()); ?></h3>
                        <div class="price-wishlist-wrapper">
                            <p class="product-price"><?php echo $produk->getHargaFormatted(); ?></p>
                            <?php $is_in_wishlist = in_array($id, $wishlist_ids); ?>
                            <a href="wishlist_aksi.php?id=<?php echo $id; ?>" 
                               class="btn-wishlist <?php echo ($is_in_wishlist ? 'active' : ''); ?>" 
                               title="Tambah ke Wishlist">
                                <i class="bi bi-heart icon-heart-empty"></i>
                                <i class="bi bi-heart-fill icon-heart-filled"></i>
                            </a>
                        </div>
                        <?php if ($produk->isStokTersedia()): ?>
                            <a href="keranjang_aksi.php?id=<?php echo $id; ?>" class="btn-add-to-cart btn-ajax-cart">
                                Tambah ke Keranjang
                            </a>
                        <?php else: ?>
                            <a href="#" class="btn-add-to-cart btn-disabled" onclick="return false;">
                                Stok Habis
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> </main>
    
    <?php if (!empty($recently_viewed_ids)): ?>
    <div class="container">
        <section class="recently-viewed-section">
            <h2 class="section-title">Baru Saja Dilihat</h2>
            <div class="product-grid">
                <?php foreach ($recently_viewed_ids as $id_produk): ?>
                    <?php if (isset($products[$id_produk])): 
                        $produk = $products[$id_produk]; 
                        $is_in_wishlist = in_array($id_produk, $wishlist_ids);
                    ?>
                        <div class="product-card" data-category="<?php echo htmlspecialchars($produk->getKategori()); ?>">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($produk->getImageUrl()); ?>" alt="<?php echo htmlspecialchars($produk->getNama()); ?>">
                                <a href="quick_view_aksi.php?id=<?php echo $id_produk; ?>" class="btn-quick-view" title="Lihat Cepat">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($produk->getNama()); ?></h3>
                                <div class="price-wishlist-wrapper">
                                    <p class="product-price"><?php echo $produk->getHargaFormatted(); ?></p>
                                    <a href="wishlist_aksi.php?id=<?php echo $id_produk; ?>" 
                                       class="btn-wishlist <?php echo ($is_in_wishlist ? 'active' : ''); ?>" 
                                       title="Tambah ke Wishlist">
                                        <i class="bi bi-heart icon-heart-empty"></i>
                                        <i class="bi bi-heart-fill icon-heart-filled"></i>
                                    </a>
                                </div>
                                <?php if ($produk->isStokTersedia()): ?>
                                    <a href="keranjang_aksi.php?id=<?php echo $id_produk; ?>" class="btn-add-to-cart btn-ajax-cart">
                                        Tambah ke Keranjang
                                    </a>
                                <?php else: ?>
                                    <a href="#" class="btn-add-to-cart btn-disabled" onclick="return false;">
                                        Stok Habis
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <?php endif; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function () {
            
            // --- 1. Logika Dark Mode ---
            var darkModeToggle = $('#dark-mode-toggle');
            var body = $('body');
            if (localStorage.getItem('theme') === 'dark') { body.addClass('dark-mode'); }
            darkModeToggle.on('click', function(event) {
                event.preventDefault();
                body.toggleClass('dark-mode');
                localStorage.setItem('theme', body.hasClass('dark-mode') ? 'dark' : 'light');
            });
            
            // --- 2. Logika Filter & Search ---
            var searchInput = $('#search-bar');
            var filterButtons = $('.btn-filter');
            var productCards = $('.product-card'); 
            function filterProducts() {
                var searchTerm = searchInput.val().toLowerCase();
                var activeCategory = $('.btn-filter.active').data('filter');
                productCards.each(function() {
                    var card = $(this);
                    var productName = card.find('.product-name').text().toLowerCase();
                    var productCategory = card.data('category');
                    var categoryMatch = (activeCategory === 'all' || productCategory === activeCategory);
                    var searchMatch = (productName.includes(searchTerm));
                    if (categoryMatch && searchMatch) {
                        card.removeClass('hidden'); 
                    } else {
                        card.addClass('hidden'); 
                    }
                });
            }
            searchInput.on('keyup', filterProducts);
            filterButtons.on('click', function() {
                filterButtons.removeClass('active'); 
                $(this).addClass('active'); 
                filterProducts(); 
            });
            
            // --- 3. Logika AJAX Keranjang ---
            $('body').on('click', '.btn-ajax-cart', function (event) {
                event.preventDefault();
                var url = $(this).attr('href');
                $.ajax({
                    url: url, type: 'GET', dataType: 'json', 
                    success: function (response) {
                        if (response.success) {
                            $('#cart-count').text(response.newCount);
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: response.message, showConfirmButton: false,
                                timer: 1500, timerProgressBar: true
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: response.message });
                        }
                    },
                    error: function () { Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Server error.' }); }
                });
            });

            // --- 4. Logika AJAX Wishlist ---
            $('body').on('click', '.btn-wishlist', function(event) {
                event.preventDefault();
                var button = $(this);
                var url = button.attr('href');
                $.ajax({
                    url: url, type: 'GET', dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            if (response.action === 'added') button.addClass('active');
                            else if (response.action === 'removed') button.removeClass('active');
                            Swal.fire({
                                toast: true, position: 'top-end',
                                icon: 'success', title: response.message,
                                showConfirmButton: false, timer: 1500
                            });
                        } else {
                            Swal.fire({ icon: 'warning', title: 'Ups!', text: 'Anda harus login dulu.'});
                        }
                    },
                    error: function() { Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Server error.' }); }
                });
            });
            
            // --- 5. Logika AJAX Quick View ---
            $('body').on('click', '.btn-quick-view', function(event) {
                event.preventDefault();
                var url = $(this).attr('href'); 
                $.ajax({
                    url: url, type: 'GET', dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            var product = response.product;
                            var stockStatus = product.isStokTersedia ? 
                                `<span class="stock available">Stok Tersedia (${product.stok})</span>` :
                                `<span class="stock unavailable">Stok Habis</span>`;
                            Swal.fire({
                                html: `
                                <div class="quick-view-content">
                                    <div class="quick-view-image">
                                        <img src="${product.imageUrl}" alt="${product.nama}">
                                    </div>
                                    <div class="quick-view-details">
                                        <h2>${product.nama}</h2>
                                        <p class="price">${product.hargaFormatted}</p>
                                        <p class="stock">${stockStatus}</p>
                                        <p class="description">${product.deskripsi}</p>
                                        <p style="margin-top: 15px;"><strong>Kategori:</strong> ${product.kategori}</p>
                                    </div>
                                </div>
                            `,
                                width: '800px',
                                showCloseButton: true,
                                showConfirmButton: false, 
                                customClass: {
                                    popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                                }
                            }).then((result) => {
                                location.reload(); // Reload untuk update "Baru Dilihat"
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Oops...', text: response.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Server error.' });
                    }
                });
            });

            // --- 6.  CUSTOM ALERT LOGOUT ---
            $('body').on('click', '#logout-link', function(event) {
                // 1. Hentikan link agar tidak langsung pindah
                event.preventDefault(); 
                var logoutUrl = $(this).attr('href'); // Ambil URL (logout.php)

                // 2. Tampilkan pop-up konfirmasi
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan keluar dari sesi ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#A98A74', // Warna tema
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal',
                    // Cek dark mode
                    customClass: { popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : '' }
                }).then((result) => {
                    // 3. Jika pengguna klik "Ya, Logout!"
                    if (result.isConfirmed) {
                        // Arahkan browser ke logout.php
                        window.location.href = logoutUrl;
                    }
                });
            });

        });
    </script>
</body>
</html>