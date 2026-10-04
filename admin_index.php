<?php
require 'config.php';
wajib_login('admin');

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $st = $pdo->prepare('SELECT * FROM tanaman WHERE nama LIKE :q OR nama_latin LIKE :q ORDER BY id DESC');
    $st->execute([':q' => "%$q%"]);
    $daftar = $st->fetchAll();
} else {
    $daftar = $pdo->query('SELECT * FROM tanaman ORDER BY id DESC')->fetchAll();
}

$jml_tanaman = (int)$pdo->query('SELECT COUNT(*) FROM tanaman')->fetchColumn();
$jml_stok    = (int)$pdo->query('SELECT COALESCE(SUM(stok),0) FROM tanaman')->fetchColumn();
$jml_pesanan = (int)$pdo->query('SELECT COUNT(*) FROM pesanan')->fetchColumn();
$jml_baru    = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE status='baru'")->fetchColumn();

$judul = 'Kelola Tanaman';
include '_header.php';
?>
<main class="wadah" style="padding-bottom:3rem">
  <h1 style="margin-top:1.5rem">Dashboard Penjual</h1>

  <div class="statistik">
    <div><strong><?= $jml_tanaman ?></strong>Jenis tanaman</div>
    <div><strong><?= $jml_stok ?></strong>Total stok</div>
    <div><strong><?= $jml_pesanan ?></strong>Total pesanan</div>
    <div><strong><?= $jml_baru ?></strong>Pesanan baru</div>
  </div>

  <div class="bar-atas">
    <form method="get">
      <input type="search" name="q" placeholder="Cari tanaman..." value="<?= e($q) ?>">
      <button class="tombol kecil" type="submit">Cari</button>
      <?php if ($q !== ''): ?><a class="tombol kecil garis" href="admin_index.php">Reset</a><?php endif; ?>
    </form>
    <a class="tombol" href="admin_tambah.php">+ Tambah Tanaman</a>
  </div>

  <div class="bungkus-tabel">
    <table>
      <thead>
        <tr><th>#</th><th>Tanaman</th><th>Cahaya</th><th>Perawatan</th><th>Harga</th><th>Stok</th><th>Siram</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php foreach ($daftar as $i => $t): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= e($t['emoji']) ?> <strong><?= e($t['nama']) ?></strong><br><small class="latin"><?= e($t['nama_latin']) ?></small></td>
          <td><span class="lencana <?= e($t['kebutuhan_cahaya']) ?>"><?= e(ucfirst($t['kebutuhan_cahaya'])) ?></span></td>
          <td><span class="lencana <?= e($t['tingkat_perawatan']) ?>"><?= e(ucfirst($t['tingkat_perawatan'])) ?></span></td>
          <td><?= rupiah($t['harga']) ?></td>
          <td><?= (int)$t['stok'] ?></td>
          <td><?= (int)$t['jadwal_siram'] ?> hr</td>
          <td>
            <div class="aksi-tabel">
              <a class="tombol kecil garis" href="admin_edit.php?id=<?= (int)$t['id'] ?>">Edit</a>
              <form method="post" action="admin_hapus.php" onsubmit="return confirm('Hapus <?= e(addslashes($t['nama'])) ?>? Tindakan ini tidak bisa dibatalkan.')">
                <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
                <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="tombol kecil bahaya" type="submit">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (!$daftar): ?><p class="kosong">Tidak ada data tanaman.</p><?php endif; ?>
  </div>
</main>
<?php include '_footer.php'; ?>
