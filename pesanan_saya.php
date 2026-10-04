<?php
require 'config.php';
wajib_login('pembeli');

$st = $pdo->prepare('SELECT * FROM pesanan WHERE user_id = ? ORDER BY id DESC');
$st->execute([user()['id']]);
$daftar = $st->fetchAll();

$judul = 'Pesanan Saya';
include '_header.php';
?>
<main class="wadah" style="padding-bottom:3rem">
  <h1 style="margin-top:1.5rem">Pesanan Saya</h1>
  <div class="bungkus-tabel">
    <table>
      <thead><tr><th>#</th><th>Tanaman</th><th>Jumlah</th><th>Total</th><th>Tanggal</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($daftar as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><?= e($p['nama_tanaman']) ?></td>
          <td><?= (int)$p['jumlah'] ?></td>
          <td><?= rupiah($p['total']) ?></td>
          <td><?= e(date('d M Y H:i', strtotime($p['created_at']))) ?></td>
          <td><span class="lencana mudah"><?= e(ucfirst($p['status'])) ?></span></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (!$daftar): ?><p class="kosong">Kamu belum punya pesanan. <a href="index.php#katalog">Lihat katalog</a></p><?php endif; ?>
  </div>
</main>
<?php include '_footer.php'; ?>
