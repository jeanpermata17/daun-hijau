<?php
// Partial header: set $judul sebelum include
$judul = $judul ?? 'Daun Hijau';
$u = user();
$pesan_flash = flash();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul) ?> · Daun Hijau</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="navbar">
  <div class="wadah nav-isi">
    <a class="logo" href="index.php">🌿 Daun Hijau</a>
    <nav class="menu">
      <a href="index.php#katalog">Katalog</a>
      <a href="index.php#tips">Tips</a>
      <a href="index.php#cara-pesan">Cara Pesan</a>
      <a href="index.php#kontak">Kontak</a>
      <?php if ($u && $u['role'] === 'admin'): ?>
        <a class="menu-admin" href="admin_index.php">Kelola Tanaman</a>
        <a class="menu-admin" href="admin_pesanan.php">Pesanan</a>
      <?php elseif ($u): ?>
        <a href="pesanan_saya.php">Pesanan Saya</a>
      <?php endif; ?>
      <?php if ($u): ?>
        <span class="sapa">Hai, <?= e($u['nama']) ?></span>
        <a class="tombol kecil garis" href="logout.php">Keluar</a>
      <?php else: ?>
        <a class="tombol kecil" href="login.php">Masuk</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<?php if ($pesan_flash): ?>
  <div class="wadah"><div class="notif"><?= e($pesan_flash) ?></div></div>
<?php endif; ?>
