<?php
// 1. Fungsi Card 1: Total Pesanan (Hari Ini)
function getTotalPesananHariIni($pdo) {
    // Menghitung transaksi yang masuk khusus hari ini
    $sql = "SELECT COUNT(id_transaksi) as total 
            FROM transaksi 
            WHERE DATE(waktu_pemesanan) = CURRENT_DATE";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['total'] : 0;
}

// 2. Fungsi Card 2: Barang Sedang Disewa
function getBarangSedangDisewa($pdo) {
    $sql = "SELECT COUNT(dt.id_barang) as total 
            FROM detail_transaksi dt 
            JOIN transaksi t ON dt.id_transaksi = t.id_transaksi 
            WHERE t.status_transaksi = 'disewa'";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['total'] : 0;
}

// 3. Fungsi Card 3: Pendapatan Hari Ini
function getPendapatanHariIni($pdo) {
    $sql = "SELECT SUM(total_tagihan + COALESCE(nominal_denda, 0)) as total 
            FROM transaksi 
            WHERE status_transaksi = 'selesai' 
            AND DATE(waktu_aktual_kembali) = CURRENT_DATE";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($row && $row['total']) ? (float)$row['total'] : 0;
}

// 4. Fungsi Card 4: Pelanggan Baru (Hari Ini)
function getPelangganBaruHariIni($pdo) {
    $sql = "SELECT COUNT(DISTINCT id_penyewa) as total 
            FROM transaksi 
            WHERE DATE(waktu_pemesanan) = CURRENT_DATE";
    $stmt = $pdo->query($sql);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['total'] : 0;
}

// 5. Fungsi Tabel: Rincian Penyewaan Hari Ini
function getRincianPenyewaanHariIni($pdo) {
    $sql = "SELECT 
                t.id_transaksi,
                b.nama_barang,
                p.nama_penyewa as nama_pelanggan,
                t.waktu_mulai_sewa as waktu_mulai,
                t.waktu_rencana_kembali as waktu_selesai,
                CASE 
                    WHEN t.status_transaksi = 'selesai' THEN 'Selesai'
                    ELSE 'Disewa'
                END as status
            FROM transaksi t
            JOIN penyewa p ON t.id_penyewa = p.id_penyewa
            JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
            JOIN barang b ON dt.id_barang = b.id_barang
            WHERE DATE(t.waktu_pemesanan) = CURRENT_DATE 
               OR t.status_transaksi = 'disewa'
            ORDER BY t.waktu_rencana_kembali ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Jika file ini diakses langsung (misal untuk uji endpoint via Postman / REST API)
if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    header('Content-Type: application/json; charset=utf-8');
    require_once __DIR__ . '/../includes/koneksi.php';

    try {
        $response = [
            'status' => 'success',
            'message' => 'Data dashboard berhasil diambil',
            'data' => [
                'total_pesanan_hari_ini' => getTotalPesananHariIni($pdo),
                'barang_sedang_disewa' => getBarangSedangDisewa($pdo),
                'pendapatan_hari_ini' => getPendapatanHariIni($pdo),
                'pelanggan_baru_hari_ini' => getPelangganBaruHariIni($pdo),
                'rincian_penyewaan_hari_ini' => getRincianPenyewaanHariIni($pdo)
            ]
        ];
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ], JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Server error: ' . $e->getMessage()
        ], JSON_PRETTY_PRINT);
    }
    exit;
}
?>
