<?php
// FILE: wishlist.php

session_start();

if (!isset($_SESSION['user_nama'])) {
    header('Location: login.php');
    exit;
}
require_once 'data_produk.php'; 
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
$wishlist_ids = $_SESSION['wishlist'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { font-family: 'Nunito Sans', sans-serif; background-color: #FAF8F5; color: #4B4B4B; margin: 0; padding: 20px; }
        .container { max-width: 1200px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 10px; border: 1px solid #F0EAE4; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h1 { text-align: center; margin-bottom: 30px; color: #5D4037; font-weight: 700; display: flex; justify-content: center; align-items: center; gap: 10px; }
        .btn-kembali { display: inline-block; border: 2px solid #A98A74; color: #A98A74; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; margin-bottom: 20px; transition: all 0.3s ease; }
        .btn-kembali:hover { background-color: #A98A74; color: #ffffff; }
        .empty-wishlist { text-align: center; font-size: 1.2rem; color: #777; padding: 40px 0; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; }
        .product-card { background-color: #ffffff; border-radius: 10px; border: 1px solid #F0EAE4; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); overflow: hidden; }
        .product-image img { width: 100%; height: 350px; object-fit: cover; }
        .product-info { padding: 20px; }
        .product-name { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; color: #5D4037; }
        .product-price { font-size: 1.1rem; font-weight: 600; color: #8C6A5A; margin-bottom: 15px; }
        .btn-add-to-cart { display: block; width: 100%; padding: 12px; background-color: #A98A74; color: #ffffff; text-align: center; text-decoration: none; border: none; border-radius: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-add-to-cart:hover { background-color: #937460; }
        body.dark-mode { background-color: #22272E; color: #ADBAC7; }
        body.dark-mode .container { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode h1 { color: #E6EDF3; }
        body.dark-mode .btn-kembali { color: #ADBAC7; border-color: #ADBAC7; }
        body.dark-mode .btn-kembali:hover { background-color: #ADBAC7; color: #2D333B; }
        body.dark-mode .product-card { background-color: #22272E; border-color: #444C56; }
        body.dark-mode .product-name { color: #E6EDF3; }
        body.dark-mode .empty-wishlist { color: #ADBAC7; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-heart-fill" style="color: #e74c3c;"></i> Wishlist Saya</h1>
        <a href="index.php" class="btn-kembali">&larr; Kembali ke toko</a>

        <?php if (empty($wishlist_ids)): ?>
            <p class="empty-wishlist">Wishlist Anda masih kosong. <br>Cari produk dan klik ikon hati untuk menambahkannya.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php
                foreach ($wishlist_ids as $id_produk):
                    if (isset($products[$id_produk])) {
                        $produk = $products[$id_produk];
                ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($produk->getImageUrl()); ?>" alt="<?php echo htmlspecialchars($produk->getNama()); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($produk->getNama()); ?></h3>
                            <p class="product-price"><?php echo $produk->getHargaFormatted(); ?></p>
                            <a href="keranjang_aksi.php?id=<?php echo $id_produk; ?>" class="btn-add-to-cart btn-ajax-cart">
                                Tambah ke Keranjang
                            </a>
                        </div>
                    </div>
                <?php
                    } 
                endforeach; 
                ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function () {
            
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }
            
            $('body').on('click', '.btn-ajax-cart', function (event) {
                event.preventDefault();
                var url = $(this).attr('href');
                $.ajax({
                    url: url, type: 'GET', dataType: 'json', 
                    success: function (response) {
                        if (response.success) {
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
        });
    </script>
</body>
</html>