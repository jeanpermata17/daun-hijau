<?php
// Jalankan SEKALI setelah database.sql diimpor: http://localhost/daun-hijau/setup.php
// Membuat akun demo dengan password ter-hash. HAPUS file ini setelah selesai.
require 'config.php';

$akun = [
    ['Admin Daun Hijau', 'admin@daunhijau.test', 'admin123', 'admin'],
    ['Pembeli Contoh', 'pembeli@daunhijau.test', 'pembeli123', 'pembeli'],
];

$hasil = [];
$cek = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$ins = $pdo->prepare('INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)');
foreach ($akun as [$nama, $email, $pass, $role]) {
    $cek->execute([$email]);
    if ($cek->fetch()) {
        $hasil[] = "Sudah ada: $email";
    } else {
        $ins->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT), $role]);
        $hasil[] = "Dibuat: $email (password: $pass, peran: $role)";
    }
}
?>
<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Setup</title>
<link rel="stylesheet" href="style.css"></head>
<body><main class="wadah" style="padding:3rem 0">
<div class="kartu-form">
<h1>Setup selesai 🌱</h1>
<ul><?php foreach ($hasil as $h) echo '<li>' . e($h) . '</li>'; ?></ul>
<p><strong>Penting:</strong> hapus file <code>setup.php</code> dari server sekarang.</p>
<p><a class="tombol" href="login.php">Ke halaman login</a></p>
</div></main></body></html>
