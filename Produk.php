<?php
class Produk {
    // Properti (Atribut)
    private $nama;
    private $harga;
    private $stok;
    private $deskripsi;
    private $kategori;
    private $imageUrl; // <-- 1. PROPERTI DITAMBAHKAN

    // Constructor
    // 2. CONSTRUCTOR DIPERBARUI untuk menerima $imageUrl
    public function __construct($nama, $harga, $stok, $deskripsi, $kategori, $imageUrl) {
        $this->nama = $nama;
        $this->harga = $harga;
        $this->stok = $stok;
        $this->deskripsi = $deskripsi;
        $this->kategori = $kategori;
        $this->imageUrl = $imageUrl; // <-- 3. NILAI DISIMPAN
    }

    // --- Getter Methods ---
    public function getNama() {
        return $this->nama;
    }

    public function getHarga() {
        return $this->harga; // Mengembalikan angka (untuk kalkulasi)
    }

    public function getHargaFormatted() {
        // Mengembalikan string (untuk tampilan)
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

    // 4. METHOD GETTER BARU DITAMBAHKAN
    public function getImageUrl() {
        return $this->imageUrl;
    }


    // --- Logic Methods ---
    public function isStokTersedia() {
        return $this->stok > 0;
    }
}
?>