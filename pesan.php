<?php
require 'config.php';
wajib_login('pembeli');

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM tanaman WHERE id = ?');
$st->execute([$id]);
$t = $st->fetch();
if (!$t) {
    flash('Tanaman tidak ditemukan.');
    header('Location: index.php#katalog');
    exit;
}

$err = [];
$jumlah = 1;
$alamat = '';
$catatan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    $jumlah  = (int)($_POST['jumlah'] ?? 0);
    $alamat  = trim($_POST['alamat'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');

    if ($jumlah < 1) $err[] = 'Jumlah minimal 1.';
    if ($alamat === '') $err[] = 'Alamat pengiriman wajib diisi.';

    if (!$err) {
        try {
            $pdo->beginTransaction();
            $kunci = $pdo->prepare('SELECT * FROM tanaman WHERE id = ? FOR UPDATE');
            $kunci->execute([$id]);
            $terkini = $kunci->fetch();
            if (!$terkini || $terkini['stok'] < $jumlah) {
                $pdo->rollBack();
                $err[] = 'Stok tidak mencukupi. Tersisa ' . (int)($terkini['stok'] ?? 0) . '.';
            } else {
                $total = $terkini['harga'] * $jumlah;
                $pdo->prepare(
                    'INSERT INTO pesanan (user_id, tanaman_id, nama_tanaman, jumlah, total, alamat, catatan)
                     VALUES (?,?,?,?,?,?,?)'
                )->execute([user()['id'], $id, $terkini['nama'], $jumlah, $total, $alamat, $catatan ?: null]);
                $pdo->prepare('UPDATE tanaman SET stok = stok - ? WHERE id = ?')->execute([$jumlah, $id]);
                $pdo->commit();
                flash('Pesanan berhasil dibuat. Terima kasih!');
                header('Location: pesanan_saya.php');
                exit;
            }
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $err[] = 'Terjadi kesalahan saat memproses pesanan.';
        }
    }
}

$judul = 'Pesan ' . $t['nama'];
include '_header.php';
?>
<main class="wadah">
  <div class="kartu-form">
    <h1><?= e($t['emoji']) ?> Pesan <?= e($t['nama']) ?></h1>
    <p><?= rupiah($t['harga']) ?> per tanaman · stok <?= (int)$t['stok'] ?></p>
    <?php foreach ($err as $er): ?><div class="notif galat"><?= e($er) ?></div><?php endforeach; ?>
    <?php if ($t['stok'] < 1): ?>
      <div class="notif galat">Maaf, stok tanaman ini sedang habis.</div>
      <a class="tombol garis" href="index.php#katalog">Kembali ke katalog</a>
    <?php else: ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
      <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
      <label for="jumlah">Jumlah</label>
      <input type="number" id="jumlah" name="jumlah" min="1" max="<?= (int)$t['stok'] ?>" value="<?= (int)$jumlah ?>" required>
      <label for="alamat">Alamat pengiriman</label>
      <textarea id="alamat" name="alamat" rows="3" required><?= e($alamat) ?></textarea>
      <label for="catatan">Catatan (opsional)</label>
      <input type="text" id="catatan" name="catatan" maxlength="255" value="<?= e($catatan) ?>">
      <p style="margin-top:1.3rem;display:flex;gap:.6rem;flex-wrap:wrap">
        <button class="tombol" type="submit">Buat Pesanan</button>
        <a class="tombol garis" href="index.php#katalog">Batal</a>
      </p>
    </form>
    <?php endif; ?>
  </div>
</main>
<?php include '_footer.php'; ?>
