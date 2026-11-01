<?php
session_start();
require_once '../includes/koneksi.php';
require_once '../includes/auth.php'; 

if (isset($_GET['id'])) {
    $id_barang = $_GET['id'];
    
    $sql = "DELETE FROM barang WHERE id_barang = :id";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([':id' => $id_barang]);
        $_SESSION['pesan_sukses'] = "Barang berhasil dihapus!";
        header("Location: list.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = "Gagal! Barang ini tidak bisa dihapus karena masih tercatat di riwayat penyewaan.";
        header("Location: list.php");
        exit;
    }
} else {
    header("Location: list.php");
    exit;
}
?>