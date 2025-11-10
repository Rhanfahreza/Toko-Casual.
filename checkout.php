<?php

session_start();
require_once 'data_produk.php'; 

if (!isset($_SESSION['user_nama'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_GET['ids']) || empty($_GET['ids'])) {
    header('Location: keranjang.php');
    exit;
}

$selected_ids_string = $_GET['ids'];
$selected_ids = explode(',', $selected_ids_string);
$keranjang_asli = $_SESSION['keranjang'];
$items_to_checkout = [];
$total_final = 0;

foreach ($selected_ids as $id_produk) {
    if (isset($keranjang_asli[$id_produk])) {
        $kuantitas = $keranjang_asli[$id_produk];
        $produk = $products[$id_produk];
        $subtotal = $produk->getHarga() * $kuantitas;
        $total_final += $subtotal;
        $items_to_checkout[$id_produk] = [
            'produk' => $produk,
            'kuantitas' => $kuantitas,
            'subtotal' => $subtotal
        ];
    }
}
if (empty($items_to_checkout)) {
    header('Location: keranjang.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body { font-family: 'Nunito Sans', sans-serif; background-color: #FAF8F5; color: #4B4B4B; margin: 0; padding: 20px; transition: background-color 0.3s ease, color 0.3s ease; }
        .container { max-width: 900px; margin: 40px auto; padding: 30px; background: #fff; border-radius: 10px; border: 1px solid #F0EAE4; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: background-color 0.3s ease, border-color 0.3s ease; }
        h1 { text-align: center; margin-bottom: 30px; color: #5D4037; font-weight: 700; display: flex; justify-content: center; align-items: center; gap: 10px; transition: color 0.3s ease; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 15px; border-bottom: 1px solid #F0EAE4; text-align: left; vertical-align: middle; transition: border-color 0.3s ease; }
        th { background-color: #FAF8F5; font-weight: 700; color: #5D4037; transition: background-color 0.3s ease, color 0.3s ease; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px; border: 1px solid #F0EAE4; }
        .total-row td { font-size: 1.3rem; font-weight: 700; text-align: right; color: #5D4037; border-bottom: none; }
        .btn-bayar { display: block; width: 100%; background: #A98A74; color: #fff; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 1.2rem; text-align: center; transition: background-color 0.3s ease; border: none; cursor: pointer; margin-top: 30px; }
        .btn-bayar:hover { background: #937460; color: #fff; }
        body.dark-mode { background-color: #22272E; color: #ADBAC7; }
        body.dark-mode .container { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode h1 { color: #E6EDF3; }
        body.dark-mode th { background-color: #22272E; color: #E6EDF3; border-color: #444C56; }
        body.dark-mode td { border-color: #444C56; }
        body.dark-mode .total-row td { color: #E6EDF3; }
        body.dark-mode .product-img { border-color: #444C56; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-shield-check"></i> Ringkasan Checkout</h1>
        <p style="text-align: center; font-size: 1.1rem;">Harap periksa kembali pesanan Anda sebelum melanjutkan.</p>

        <table>
            <thead>
                <tr>
                    <th colspan="2">Produk</th>
                    <th>Harga</th>
                    <th>Kuantitas</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items_to_checkout as $id_produk => $item): ?>
                    <tr>
                        <td>
                            <img src="<?php echo htmlspecialchars($item['produk']->getImageUrl()); ?>" alt="" class="product-img">
                        </td>
                        <td>
                            <?php echo htmlspecialchars($item['produk']->getNama()); ?>
                        </td>
                        <td><?php echo $item['produk']->getHargaFormatted(); ?></td>
                        <td><?php echo $item['kuantitas']; ?></td>
                        <td>Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4">Total Pembayaran</td>
                    <td>Rp <?php echo number_format($total_final, 0, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>
        
        <button id="btn-bayar" class="btn-bayar" data-ids="<?php echo htmlspecialchars($selected_ids_string); ?>">
            Bayar Sekarang (Simulasi)
        </button>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function () {
            
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }

            $('#btn-bayar').on('click', function() {
                var idsToClear = $(this).data('ids'); 

                Swal.fire({
                    title: 'Pembayaran Berhasil!',
                    text: 'Ini hanya simulasi. Pesanan Anda akan kami proses.',
                    icon: 'success',
                    confirmButtonText: 'Kembali ke Toko',
                    customClass: { popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : '' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'keranjang_aksi.php',
                            type: 'GET',
                            data: {
                                aksi: 'clear_paid',
                                ids: idsToClear
                            },
                            dataType: 'json',
                            success: function(response) {
                                window.location.href = 'index.php';
                            },
                            error: function() {
                                alert('Gagal membersihkan keranjang, tapi pembayaran (simulasi) berhasil.');
                                window.location.href = 'index.php';
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>