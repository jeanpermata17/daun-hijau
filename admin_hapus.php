<?php
require 'config.php';
wajib_login('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_index.php');
    exit;
}
cek_csrf();

$id = (int)($_POST['id'] ?? 0);
$st = $pdo->prepare('SELECT nama FROM tanaman WHERE id = ?');
$st->execute([$id]);
$t = $st->fetch();

if ($t) {
    $pdo->prepare('DELETE FROM tanaman WHERE id = ?')->execute([$id]);
    flash('Tanaman "' . $t['nama'] . '" telah dihapus.');
} else {
    flash('Tanaman tidak ditemukan.');
}
header('Location: admin_index.php');
exit;
