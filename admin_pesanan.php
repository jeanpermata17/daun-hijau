<?php
require 'config.php';
wajib_login('admin');

$status_valid = ['baru', 'diproses', 'dikirim', 'selesai'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, $status_valid, true)) {
        $pdo->prepare('UPDATE pesanan SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('Status pesanan #' . $id . ' diperbarui.');
    }
    header('Location: admin_pesanan.php');
    exit;
}

$daftar = $pdo->query(
    'SELECT p.*, u.nama AS nama_pembeli, u.email
     FROM pesanan p JOIN users u ON u.id = p.user_id
     ORDER BY p.id DESC'
)->fetchAll();

$judul = 'Pesanan Masuk';
include '_header.php';
?>
<main class="wadah" style="padding-bottom:3rem">
  <h1 style="margin-top:1.5rem">Pesanan Masuk</h1>
  <div class="bungkus-tabel">
    <table>
      <thead><tr><th>#</th><th>Pembeli</th><th>Tanaman</th><th>Jumlah</th><th>Total</th><th>Alamat</th><th>Tanggal</th><th>Status</th></tr></thead>
      <tbody>
      <?php foreach ($daftar as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td><strong><?= e($p['nama_pembeli']) ?></strong><br><small><?= e($p['email']) ?></small></td>
          <td><?= e($p['nama_tanaman']) ?></td>
          <td><?= (int)$p['jumlah'] ?></td>
          <td><?= rupiah($p['total']) ?></td>
          <td style="max-width:220px"><?= nl2br(e($p['alamat'])) ?><?php if ($p['catatan']): ?><br><small>Catatan: <?= e($p['catatan']) ?></small><?php endif; ?></td>
          <td><?= e(date('d M Y H:i', strtotime($p['created_at']))) ?></td>
          <td>
            <form method="post" class="aksi-tabel">
              <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
              <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
              <select name="status" onchange="this.form.submit()" style="padding:.35rem .6rem;border-radius:12px;border:2px solid var(--tulang-gelap)">
                <?php foreach ($status_valid as $s): ?>
                  <option value="<?= $s ?>" <?= $p['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (!$daftar): ?><p class="kosong">Belum ada pesanan.</p><?php endif; ?>
  </div>
</main>
<?php include '_footer.php'; ?>
