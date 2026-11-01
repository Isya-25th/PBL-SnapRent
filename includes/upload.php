<?php
function handleUploadFotoBarang($fileInputName, $existingFile = null) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingFile;
    }

    $file = $_FILES[$fileInputName];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Gagal mengunggah file (kode error: " . $file['error'] . ")");
    }

    // Batas maksimal 2MB
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        throw new Exception("Ukuran gambar maksimal adalah 2MB!");
    }

    // Validasi tipe mime dan ekstensi
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        throw new Exception("Format gambar harus JPG, PNG, atau WEBP!");
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFilename = 'cam_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    
    $uploadDir = __DIR__ . '/../uploads/barang/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destPath = $uploadDir . $newFilename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception("Gagal menyimpan file gambar ke direktori upload.");
    }

    // Hapus foto lama jika ada dan berbeda
    if ($existingFile && file_exists($uploadDir . $existingFile) && is_file($uploadDir . $existingFile)) {
        @unlink($uploadDir . $existingFile);
    }

    return $newFilename;
}
