<?php
// Backend (Rafi'/Isya): Proses UPDATE data barang ke PostgreSQL
// Dipanggil via POST dari barang/edit.php

require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
require_once '../includes/upload.php';

// ---------------------------------------------------------------
// 1. Tolak akses selain POST
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

// ---------------------------------------------------------------
// 2. Ambil & sanitasi input dari form edit
// ---------------------------------------------------------------
$id_barang           = filter_input(INPUT_POST, 'id_barang',           FILTER_VALIDATE_INT);
$id_kategori         = filter_input(INPUT_POST, 'id_kategori',         FILTER_VALIDATE_INT);
$nama_barang         = trim($_POST['nama_barang']         ?? '');
$kondisi_fisik       = trim($_POST['kondisi_fisik']       ?? '');
$harga_sewa_per_hari = filter_input(INPUT_POST, 'harga_sewa_per_hari', FILTER_VALIDATE_FLOAT);
$status_barang       = trim($_POST['status_barang']       ?? '');

$allowed_status = ['aktif', 'rusak', 'maintenance', 'nonaktif'];

// ---------------------------------------------------------------
// 3. Validasi per-field
// ---------------------------------------------------------------
$errors = [];

if (!$id_barang || $id_barang <= 0) {
    $errors[] = 'ID barang tidak valid.';
}
if (!$id_kategori || $id_kategori <= 0) {
    $errors[] = 'Kategori wajib dipilih.';
}
if ($nama_barang === '') {
    $errors[] = 'Nama barang tidak boleh kosong.';
} elseif (mb_strlen($nama_barang) > 100) {
    $errors[] = 'Nama barang maksimal 100 karakter.';
}
if ($harga_sewa_per_hari === false || $harga_sewa_per_hari < 0) {
    $errors[] = 'Harga sewa harus berupa angka positif.';
}
if (!in_array($status_barang, $allowed_status, true)) {
    $errors[] = 'Status barang tidak valid.';
}
if ($kondisi_fisik !== '' && mb_strlen($kondisi_fisik) > 255) {
    $errors[] = 'Kondisi fisik maksimal 255 karakter.';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(' ', $errors);
    header('Location: edit.php?id=' . (int) $id_barang);
    exit;
}

// ---------------------------------------------------------------
// 4. Eksekusi UPDATE dalam transaksi DB
// ---------------------------------------------------------------
try {
    $pdo->beginTransaction();

    // 4a. Kunci baris & ambil foto lama, sekaligus validasi keberadaan barang
    $stmt_cek = $pdo->prepare(
        'SELECT foto_barang FROM barang WHERE id_barang = :id FOR UPDATE'
    );
    $stmt_cek->execute([':id' => $id_barang]);
    $barang_lama = $stmt_cek->fetch(PDO::FETCH_ASSOC);

    if (!$barang_lama) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Data barang tidak ditemukan.';
        header('Location: list.php');
        exit;
    }

    // 4b. Proses upload foto — delegasi ke includes/upload.php
    //     handleUploadFotoBarang($inputName, $existingFile):
    //       - Tidak ada file baru  → return foto lama (tidak berubah)
    //       - Ada file baru        → validasi MIME & ukuran maks 2 MB
    //                             → simpan ke uploads/barang/ dengan nama unik
    //                             → hapus foto lama otomatis
    //                             → return nama file baru
    //       - Upload error         → throw Exception
    $foto_simpan = handleUploadFotoBarang('foto_barang', $barang_lama['foto_barang']);

    // 4c. UPDATE barang
    $stmt = $pdo->prepare(
        "UPDATE barang
            SET id_kategori         = :id_kategori,
                nama_barang         = :nama_barang,
                kondisi_fisik       = :kondisi_fisik,
                harga_sewa_per_hari = :harga_sewa,
                status_barang       = :status_barang,
                foto_barang         = :foto_barang
          WHERE id_barang           = :id_barang"
    );
    $stmt->execute([
        ':id_kategori'   => $id_kategori,
        ':nama_barang'   => $nama_barang,
        ':kondisi_fisik' => $kondisi_fisik !== '' ? $kondisi_fisik : null,
        ':harga_sewa'    => $harga_sewa_per_hari,
        ':status_barang' => $status_barang,
        ':foto_barang'   => $foto_simpan,
        ':id_barang'     => $id_barang,
    ]);

    $pdo->commit();

    $_SESSION['success'] = "Data barang '{$nama_barang}' berhasil diperbarui.";
    header('Location: list.php');
    exit;

} catch (Exception $e) {
    // Menangkap PDOException maupun Exception dari handleUploadFotoBarang
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = 'Gagal memperbarui data: ' . $e->getMessage();
    header('Location: edit.php?id=' . (int) $id_barang);
    exit;
}
?>
