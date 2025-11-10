<?php
// FILE: wishlist_aksi.php

session_start();

$response = [
    'success' => false,
    'action' => 'none', // 'added' or 'removed'
    'message' => 'Login_dulu'
];

if (!isset($_SESSION['user_nama'])) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if (isset($_GET['id'])) {
    $id_produk = (int)$_GET['id'];
    
    $key = array_search($id_produk, $_SESSION['wishlist']);
    
    if ($key !== false) {
        unset($_SESSION['wishlist'][$key]);
        $response['success'] = true;
        $response['action'] = 'removed';
        $response['message'] = 'Dihapus dari wishlist.';
    } else {
        $_SESSION['wishlist'][] = $id_produk;
        $response['success'] = true;
        $response['action'] = 'added';
        $response['message'] = 'Ditambahkan ke wishlist!';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>