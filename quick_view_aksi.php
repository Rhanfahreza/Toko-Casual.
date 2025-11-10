<?php
// FILE: quick_view_aksi.php

session_start();
require_once 'data_produk.php'; 

if (!isset($_SESSION['user_nama'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Anda harus login untuk melihat produk.']);
    exit;
}

$response = [
    'success' => false,
    'message' => 'Produk tidak ditemukan.'
];

if (isset($_GET['id'])) {
    $id_produk = (int)$_GET['id'];
    
    if (isset($products[$id_produk])) {
        $produk = $products[$id_produk];
        
        // Fitur "Baru Dilihat"
        if (!isset($_SESSION['recently_viewed'])) {
            $_SESSION['recently_viewed'] = [];
        }
        array_unshift($_SESSION['recently_viewed'], $id_produk);
        $_SESSION['recently_viewed'] = array_unique($_SESSION['recently_viewed']);
        $_SESSION['recently_viewed'] = array_slice($_SESSION['recently_viewed'], 0, 5);
        
        // Siapkan data untuk dikirim sebagai JSON
        $response['success'] = true;
        $response['message'] = 'Produk ditemukan.';
        $response['product'] = [
            'id' => $id_produk,
            'nama' => $produk->getNama(),
            'harga' => $produk->getHarga(),
            'hargaFormatted' => $produk->getHargaFormatted(),
            'stok' => $produk->getStok(),
            'isStokTersedia' => $produk->isStokTersedia(),
            'deskripsi' => $produk->getDeskripsi(),
            'kategori' => $produk->getKategori(),
            'imageUrl' => $produk->getImageUrl()
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>