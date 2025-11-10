<?php

session_start();
require_once 'data_produk.php'; 

$response = [
    'success' => false,
    'message' => 'Terjadi kesalahan.',
    'newCount' => 0
];

if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];

    if ($aksi == 'hapus' && isset($_GET['id'])) {
        $id_produk = $_GET['id'];
        if (isset($_SESSION['keranjang'][$id_produk])) {
            unset($_SESSION['keranjang'][$id_produk]);
        }
        header('Location: keranjang.php'); 
        exit;
    } 
    elseif ($aksi == 'kosongkan') {
        $_SESSION['keranjang'] = [];
        header('Location: keranjang.php'); 
        exit;
    }
    elseif ($aksi == 'clear_paid' && isset($_GET['ids'])) {
        $ids_to_clear_string = $_GET['ids']; 
        $ids_to_clear = explode(',', $ids_to_clear_string); 

        if (isset($_SESSION['keranjang'])) {
            foreach ($ids_to_clear as $id_produk) {
                $id_produk = (int)$id_produk; 
                if (isset($_SESSION['keranjang'][$id_produk])) {
                    unset($_SESSION['keranjang'][$id_produk]);
                }
            }
        }
        $response['success'] = true;
        $response['message'] = 'Keranjang telah dibersihkan.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit; 
    }
} 
elseif (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    if (isset($products[$id_produk])) {
        if ($products[$id_produk]->isStokTersedia()) {
            if (!isset($_SESSION['keranjang'])) {
                $_SESSION['keranjang'] = [];
            }
            if (isset($_SESSION['keranjang'][$id_produk])) {
                $_SESSION['keranjang'][$id_produk]++;
            } else {
                $_SESSION['keranjang'][$id_produk] = 1;
            }
            $response['success'] = true;
            $response['message'] = 'Berhasil ditambahkan ke keranjang!';
        } else {
            $response['message'] = 'Maaf, stok produk ini habis.';
        }
    } else {
        $response['message'] = 'Produk tidak ditemukan.';
    }
}

if (isset($_SESSION['keranjang'])) {
    $response['newCount'] = array_sum($_SESSION['keranjang']);
}
header('Content-Type: application/json');
echo json_encode($response);
exit;
?>