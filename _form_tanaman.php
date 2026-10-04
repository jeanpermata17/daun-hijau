<?php
// Form bersama untuk tambah & edit. Variabel: $t (data), $err (array), $aksi_label
?>
<main class="wadah">
  <div class="kartu-form lebar">
    <h1><?= e($judul) ?></h1>
    <?php foreach (($err ?? []) as $er): ?><div class="notif galat"><?= e($er) ?></div><?php endforeach; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">

      <div class="dua-kolom">
        <div>
          <label for="nama">Nama tanaman *</label>
          <input type="text" id="nama" name="nama" required value="<?= e($t['nama']) ?>">
        </div>
        <div>
          <label for="nama_latin">Nama latin</label>
          <input type="text" id="nama_latin" name="nama_latin" value="<?= e($t['nama_latin']) ?>">
        </div>
      </div>

      <div class="dua-kolom">
        <div>
          <label for="emoji">Emoji ikon</label>
          <input type="text" id="emoji" name="emoji" maxlength="4" value="<?= e($t['emoji']) ?>">
        </div>
        <div>
          <label for="jadwal_siram">Siram tiap (hari)</label>
          <input type="number" id="jadwal_siram" name="jadwal_siram" min="1" max="60" value="<?= (int)$t['jadwal_siram'] ?>">
        </div>
      </div>

      <div class="dua-kolom">
        <div>
          <label for="kebutuhan_cahaya">Kebutuhan cahaya</label>
          <select id="kebutuhan_cahaya" name="kebutuhan_cahaya">
            <option value="teduh"  <?= $t['kebutuhan_cahaya'] === 'teduh'  ? 'selected' : '' ?>>Teduh</option>
            <option value="terang" <?= $t['kebutuhan_cahaya'] === 'terang' ? 'selected' : '' ?>>Terang</option>
          </select>
        </div>
        <div>
          <label for="tingkat_perawatan">Tingkat perawatan</label>
          <select id="tingkat_perawatan" name="tingkat_perawatan">
            <option value="mudah"  <?= $t['tingkat_perawatan'] === 'mudah'  ? 'selected' : '' ?>>Mudah</option>
            <option value="sedang" <?= $t['tingkat_perawatan'] === 'sedang' ? 'selected' : '' ?>>Sedang</option>
          </select>
        </div>
      </div>

      <div class="dua-kolom">
        <div>
          <label for="harga">Harga (Rp)</label>
          <input type="number" id="harga" name="harga" min="0" step="500" value="<?= (int)$t['harga'] ?>">
        </div>
        <div>
          <label for="stok">Stok</label>
          <input type="number" id="stok" name="stok" min="0" value="<?= (int)$t['stok'] ?>">
        </div>
      </div>

      <label for="deskripsi">Deskripsi</label>
      <textarea id="deskripsi" name="deskripsi" rows="4"><?= e($t['deskripsi']) ?></textarea>

      <p style="margin-top:1.4rem;display:flex;gap:.6rem;flex-wrap:wrap">
        <button class="tombol" type="submit"><?= e($aksi_label ?? 'Simpan') ?></button>
        <a class="tombol garis" href="admin_index.php">Batal</a>
      </p>
    </form>
  </div>
</main>
