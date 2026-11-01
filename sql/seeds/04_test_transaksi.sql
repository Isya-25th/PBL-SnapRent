    -- 1. Tambah Penyewa Baru Hari Ini (Untuk menguji getPelangganBaruHariIni)
    INSERT INTO penyewa (nama_penyewa, no_telepon)
    VALUES ('Pelanggan Uji Postman', '089998887771');

    -- 2. Tambah Transaksi "Disewa" Hari Ini (Untuk getTotalPesananHariIni, getBarangSedangDisewa, & getRincianPenyewaanHariIni)
    INSERT INTO transaksi (
        id_penyewa,
        id_admin,
        waktu_pemesanan,
        waktu_mulai_sewa,
        waktu_rencana_kembali,
        jenis_pembayaran,
        status_pembayaran,
        status_transaksi,
        total_tagihan,
        nominal_denda
    ) VALUES (
        (SELECT id_penyewa FROM penyewa WHERE no_telepon = '089998887771'),
        1,
        CURRENT_TIMESTAMP,                      -- waktu pemesanan = HARI INI
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP + INTERVAL '1 day',
        'transfer',
        'lunas',
        'disewa',                               -- status = 'disewa'
        150000.00,
        0.00
    );

    -- 3. Tambah Detail Barang yang Disewa (Untuk getBarangSedangDisewa & getRincianPenyewaanHariIni)
    INSERT INTO detail_transaksi (id_transaksi, id_barang, harga_satuan, subtotal)
    VALUES (
        (SELECT MAX(id_transaksi) FROM transaksi),
        1,                                      -- id_barang 1 (Reflektor 5 in 1)
        150000.00,
        150000.00
    );

    -- 4. Tambah Transaksi "Selesai" Hari Ini (Untuk menguji getPendapatanHariIni)
    INSERT INTO transaksi (
        id_penyewa,
        id_admin,
        waktu_pemesanan,
        waktu_mulai_sewa,
        waktu_rencana_kembali,
        waktu_aktual_kembali,
        jenis_pembayaran,
        status_pembayaran,
        status_transaksi,
        total_tagihan,
        nominal_denda
    ) VALUES (
        (SELECT id_penyewa FROM penyewa WHERE no_telepon = '089998887771'),
        1,
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP - INTERVAL '2 days',
        CURRENT_TIMESTAMP - INTERVAL '1 day',
        CURRENT_TIMESTAMP,                      -- waktu aktual kembali = HARI INI
        'tunai',
        'lunas',
        'selesai',                              -- status = 'selesai'
        200000.00,
        50000.00                                -- denda 50.000 (total pendapatan = 250.000)
    );