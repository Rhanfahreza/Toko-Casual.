<?php
// FILE: keranjang.php (LENGKAP DENGAN CUSTOM ALERT)
session_start();
require_once 'data_produk.php';
if (!isset($_SESSION['user_nama'])) {
    header('Location: login.php');
    exit;
}
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}
$keranjang = $_SESSION['keranjang'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja Anda</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* (SEMUA CSS LENGKAP ANDA DARI SEBELUMNYA DI SINI) */
        body {
            font-family: 'Nunito Sans', sans-serif;
            background-color: #FAF8F5;
            color: #4B4B4B;
            margin: 0;
            padding: 20px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            border: 1px solid #F0EAE4;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #5D4037;
            font-weight: 700;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: color 0.3s ease;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 15px;
            border-bottom: 1px solid #F0EAE4;
            text-align: left;
            vertical-align: middle;
            transition: border-color 0.3s ease;
        }

        th {
            background-color: #FAF8F5;
            font-weight: 700;
            color: #5D4037;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
            border: 1px solid #F0EAE4;
        }

        a {
            color: #A98A74;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #5D4037;
        }

        .empty-cart {
            text-align: center;
            font-size: 1.2rem;
            color: #777;
            padding: 40px 0;
        }

        .btn-kembali {
            display: inline-block;
            border: 2px solid #A98A74;
            color: #A98A74;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .btn-kembali:hover {
            background-color: #A98A74;
            color: #ffffff;
        }

        .link-hapus {
            color: #e74c3c;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* (BARU) Tambahkan cursor:pointer */
        .link-hapus:hover {
            color: #c0392b;
            text-decoration: underline;
        }

        .cart-checkbox {
            width: 1.3em;
            height: 1.3em;
            accent-color: #5D4037;
        }

        .checkout-footer {
            position: sticky;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.08);
            border-top: 1px solid #F0EAE4;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 -30px -30px -30px;
            /* Disesuaikan dengan padding .container */
            padding-left: 50px;
            padding-right: 50px;
            border-bottom-left-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        .checkout-total {
            text-align: right;
        }

        .checkout-total span {
            font-size: 1.3rem;
            font-weight: 700;
            color: #5D4037;
            margin-left: 10px;
        }

        .btn-checkout {
            display: inline-block;
            background: #A98A74;
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-checkout:hover {
            background: #937460;
            color: #fff;
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        body.dark-mode {
            background-color: #22272E;
            color: #ADBAC7;
        }

        body.dark-mode .container {
            background-color: #2D333B;
            border-color: #444C56;
        }

        body.dark-mode h1 {
            color: #E6EDF3;
        }

        body.dark-mode th {
            background-color: #22272E;
            color: #E6EDF3;
            border-color: #444C56;
        }

        body.dark-mode td {
            border-color: #444C56;
        }

        body.dark-mode .btn-kembali {
            color: #ADBAC7;
            border-color: #ADBAC7;
        }

        body.dark-mode .btn-kembali:hover {
            background-color: #ADBAC7;
            color: #2D333B;
        }

        body.dark-mode a {
            color: #A98A74;
        }

        body.dark-mode a:hover {
            color: #E6EDF3;
        }

        body.dark-mode .link-hapus {
            color: #e74c3c;
        }

        body.dark-mode .empty-cart {
            color: #ADBAC7;
        }

        body.dark-mode .product-img {
            border-color: #444C56;
        }

        body.dark-mode .checkout-footer {
            background-color: #2D333B;
            border-color: #444C56;
        }

        body.dark-mode .checkout-total span {
            color: #E6EDF3;
        }

        /* --- RESPONSIVE MOBILE STYLE --- */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #F0EAE4;
            margin-top: 20px;
        }
        body.dark-mode .table-responsive {
            border-color: #444C56;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            .container {
                padding: 15px;
                margin: 20px auto;
            }
            th, td {
                padding: 10px;
                font-size: 0.85rem;
            }
            .product-img {
                width: 45px;
                height: 45px;
                margin-right: 8px;
            }
            .checkout-footer {
                flex-direction: column;
                gap: 15px;
                padding: 15px;
                margin: 20px 0 0 0;
                position: static;
                border-radius: 8px;
                text-align: center;
            }
            .checkout-total {
                text-align: center;
            }
            #btn-checkout {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="bi bi-bag-check-fill"></i> Keranjang Belanja Anda</h1>
        <a href="index.php" class="btn-kembali">&larr; Kembali ke toko</a>

        <?php if (empty($keranjang)): ?>
            <p class="empty-cart">Keranjang Anda masih kosong.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" class="cart-checkbox"></th>
                        <th colspan="2">Produk</th>
                        <th>Harga</th>
                        <th>Kuantitas</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_global = 0;
                    foreach ($keranjang as $id_produk => $kuantitas):
                        if (isset($products[$id_produk])) {
                            $produk = $products[$id_produk];
                            $subtotal = $produk->getHarga() * $kuantitas;
                            $total_global += $subtotal;
                            ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="cart-checkbox item-checkbox" data-id="<?php echo $id_produk; ?>"
                                        data-subtotal="<?php echo $subtotal; ?>">
                                </td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($produk->getImageUrl()); ?>"
                                        alt="<?php echo htmlspecialchars($produk->getNama()); ?>" class="product-img">
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($produk->getNama()); ?>
                                    <br>
                                    <a href="keranjang_aksi.php?aksi=hapus&id=<?php echo $id_produk; ?>"
                                        class="link-hapus link-hapus-item">Hapus</a>
                                </td>
                                <td><?php echo $produk->getHargaFormatted(); ?></td>
                                <td><?php echo $kuantitas; ?></td>
                                <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                            <?php
                        }
                    endforeach;
                    ?>
                </tbody>
            </table>
            </div> <!-- End of .table-responsive -->
        <?php endif; ?>

    </div> <?php if (!empty($keranjang)): ?>
        <footer class="checkout-footer">
            <div classs="cart-actions">
                <a href="keranjang_aksi.php?aksi=kosongkan" class="link-hapus" id="link-kosongkan">Kosongkan Keranjang</a>
            </div>
            <div class="checkout-total">
                Total Dipilih (0 item):
                <span id="checkout-total-display">Rp 0</span>
            </div>
            <button id="btn-checkout" class="btn-checkout">Lanjut ke Checkout</button>
        </footer>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function () {

            // --- 1. Logika Dark Mode ---
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }

            // --- 2. Logika Hitung Total (Tidak Berubah) ---
            function updateCheckoutTotal() {
                var totalCheckout = 0;
                var totalItems = 0;
                $('.item-checkbox:checked').each(function () {
                    totalCheckout += $(this).data('subtotal');
                    totalItems++;
                });
                $('#checkout-total-display').text('Rp ' + totalCheckout.toLocaleString('id-ID'));
                $('.checkout-total').contents()[0].nodeValue = 'Total Dipilih (' + totalItems + ' item): ';
            }
            $('#select-all').on('click', function () {
                $('.item-checkbox').prop('checked', this.checked);
                updateCheckoutTotal();
            });
            $('.item-checkbox').on('click', function () {
                if ($('.item-checkbox:checked').length < $('.item-checkbox').length) {
                    $('#select-all').prop('checked', false);
                } else {
                    $('#select-all').prop('checked', true);
                }
                updateCheckoutTotal();
            });

            // --- 3. Logika Tombol Checkout (Tidak Berubah) ---
            $('#btn-checkout').on('click', function (event) {
                event.preventDefault();
                var selectedIds = [];
                $('.item-checkbox:checked').each(function () {
                    selectedIds.push($(this).data('id'));
                });
                if (selectedIds.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Anda belum memilih produk untuk di-checkout.',
                        customClass: { popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : '' }
                    });
                } else {
                    window.location.href = 'checkout.php?ids=' + selectedIds.join(',');
                }
            });

            // --- 4. (BARU) CUSTOM ALERT HAPUS ITEM ---
            $('body').on('click', '.link-hapus-item', function (event) {
                event.preventDefault(); // Hentikan link
                var deleteUrl = $(this).attr('href'); // Ambil URL hapus
                var productName = $(this).closest('td').contents().filter(function () {
                    return this.nodeType === 3; // Ambil teks saja
                }).text().trim();

                Swal.fire({
                    title: 'Hapus Produk?',
                    text: `Anda yakin ingin menghapus "${productName}" dari keranjang?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#A98A74',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : '' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = deleteUrl;
                    }
                });
            });

            // --- 5. (BARU) CUSTOM ALERT KOSONGKAN KERANJANG ---
            $('body').on('click', '#link-kosongkan', function (event) {
                event.preventDefault(); // Hentikan link
                var clearUrl = $(this).attr('href'); // Ambil URL kosongkan

                Swal.fire({
                    title: 'Kosongkan Keranjang?',
                    text: "Anda yakin ingin menghapus SEMUA produk dari keranjang?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#A98A74',
                    confirmButtonText: 'Ya, Kosongkan!',
                    cancelButtonText: 'Batal',
                    customClass: { popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : '' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = clearUrl;
                    }
                });
            });

        });
    </script>
</body>

</html>