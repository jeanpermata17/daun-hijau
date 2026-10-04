<?php
require 'config.php';
wajib_login('admin');

$t = tanaman_kosong();
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    [$t, $err] = input_tanaman();
    if (!$err) {
        $pdo->prepare(
            'INSERT INTO tanaman (nama, nama_latin, emoji, kebutuhan_cahaya, tingkat_perawatan, harga, stok, jadwal_siram, deskripsi)
             VALUES (:nama, :nama_latin, :emoji, :kebutuhan_cahaya, :tingkat_perawatan, :harga, :stok, :jadwal_siram, :deskripsi)'
        )->execute($t);
        flash('Tanaman "' . $t['nama'] . '" berhasil ditambahkan.');
        header('Location: admin_index.php');
        exit;
    }
}

$judul = 'Tambah Tanaman';
$aksi_label = 'Simpan Tanaman';
include '_header.php';
include '_form_tanaman.php';
include '_footer.php';
