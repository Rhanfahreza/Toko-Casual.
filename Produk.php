<?php
class Produk {
    private $nama;
    private $harga;
    private $stok;
    private $deskripsi;
    private $kategori;
    private $imageUrl;


    public function __construct($nama, $harga, $stok, $deskripsi, $kategori, $imageUrl) {
        $this->nama = $nama;
        $this->harga = $harga;
        $this->stok = $stok;
        $this->deskripsi = $deskripsi;
        $this->kategori = $kategori;
        $this->imageUrl = $imageUrl;
    }


    public function getNama() {
        return $this->nama;
    }

    public function getHarga() {
        return $this->harga; 
    }

    public function getHargaFormatted() {
        return "Rp " . number_format($this->harga, 0, ',', '.');
    }

    public function getStok() {
        return $this->stok;
    }

    public function getDeskripsi() {
        return $this->deskripsi;
    }

    public function getKategori() {
        return $this->kategori;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function isStokTersedia() {
        return $this->stok > 0;
    }
}
?>