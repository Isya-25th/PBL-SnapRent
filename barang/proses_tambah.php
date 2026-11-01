<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
require_once '../includes/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit;
}

$nama_barang         = trim($_POST['nama_barang'] ?? '');
$id_kategori         = (int)($_POST['id_kategori'] ?? 0);
$harga_sewa_per_hari = (float)($_POST['harga_sewa_per_hari'] ?? 0);
$kondisi_fisik       = trim($_POST['kondisi_fisik'] ?? '');
$status_barang       = trim($_POST['status_barang'] ?? 'aktif');

if (empty($nama_barang) || $id_kategori <= 0 || $harga_sewa_per_hari <= 0) {
    $_SESSION['pesan_error'] = "Nama kamera, kategori, dan harga sewa wajib diisi dengan benar!";
    header("Location: tambah.php");
    exit;
}

try {
    // Handle upload foto jika ada
    $foto_barang = handleUploadFotoBarang('foto_barang');

    $sql = "INSERT INTO barang (id_kategori, nama_barang, foto_barang, kondisi_fisik, harga_sewa_per_hari, status_barang) 
            VALUES (:id_kategori, :nama_barang, :foto_barang, :kondisi_fisik, :harga, :status)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id_kategori'   => $id_kategori,
        ':nama_barang'   => $nama_barang,
        ':foto_barang'   => $foto_barang,
        ':kondisi_fisik' => $kondisi_fisik,
        ':harga'         => $harga_sewa_per_hari,
        ':status'        => $status_barang
    ]);

    $_SESSION['pesan_sukses'] = "Unit kamera '{$nama_barang}' berhasil ditambahkan ke inventaris!";
    header("Location: list.php");
    exit;

} catch (Exception $e) {
    $_SESSION['pesan_error'] = "Gagal menambahkan kamera: " . $e->getMessage();
    header("Location: tambah.php");
    exit;
}
