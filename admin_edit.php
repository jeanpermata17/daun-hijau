<?php
require 'config.php';
wajib_login('admin');

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM tanaman WHERE id = ?');
$st->execute([$id]);
$t = $st->fetch();
if (!$t) {
    flash('Tanaman tidak ditemukan.');
    header('Location: admin_index.php');
    exit;
}
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    [$data, $err] = input_tanaman();
    $t = array_merge($t, $data);
    if (!$err) {
        $data['id'] = $id;
        $pdo->prepare(
            'UPDATE tanaman SET nama=:nama, nama_latin=:nama_latin, emoji=:emoji, kebutuhan_cahaya=:kebutuhan_cahaya,
             tingkat_perawatan=:tingkat_perawatan, harga=:harga, stok=:stok, jadwal_siram=:jadwal_siram, deskripsi=:deskripsi
             WHERE id=:id'
        )->execute($data);
        flash('Tanaman "' . $data['nama'] . '" berhasil diperbarui.');
        header('Location: admin_index.php');
        exit;
    }
}

$judul = 'Edit Tanaman';
$aksi_label = 'Simpan Perubahan';
include '_header.php';
include '_form_tanaman.php';
include '_footer.php';
