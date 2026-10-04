<?php
require 'config.php';
$daftar = $pdo->query('SELECT * FROM tanaman ORDER BY nama ASC')->fetchAll();
$judul = 'Toko Tanaman Hias';
include '_header.php';
?>

<!-- ===== HERO ===== -->
<section class="hero" id="beranda">
  <div class="wadah hero-isi">
    <div>
      <h1>Rawat tanaman jadi <em>mudah</em>, bahkan di kamar kos.</h1>
      <p>Daun Hijau membantu pemula memilih tanaman hias yang sesuai dengan cahaya di rumahmu, lengkap dengan panduan perawatan yang simpel.</p>
      <div class="hero-aksi">
        <a class="tombol" href="#katalog">Lihat Tanaman</a>
        <a class="tombol garis" href="#tips">Tips Perawatan</a>
      </div>
    </div>
    <div class="hero-gambar" aria-hidden="true">
      <svg class="hero-plant" viewBox="0 0 300 360" xmlns="http://www.w3.org/2000/svg">
        <path class="pot" d="M88 272 h124 l-16 74 h-92 z" fill="#c7794a"/>
        <rect class="pot" x="80" y="258" width="140" height="22" rx="10" fill="#b0663a"/>
        <path class="batang" d="M150 262 C150 210 140 170 150 90"/>
        <path class="batang b2" d="M150 225 C172 200 200 190 224 150"/>
        <path class="batang b3" d="M150 195 C128 170 100 165 76 128"/>
        <ellipse class="daun d1" cx="150" cy="72" rx="20" ry="38"/>
        <ellipse class="daun tua d2" cx="232" cy="136" rx="18" ry="34" transform="rotate(40 232 136)"/>
        <ellipse class="daun tua d3" cx="68" cy="114" rx="18" ry="34" transform="rotate(-40 68 114)"/>
        <ellipse class="daun d4" cx="190" cy="196" rx="14" ry="26" transform="rotate(60 190 196)"/>
        <ellipse class="daun d5" cx="112" cy="180" rx="14" ry="26" transform="rotate(-60 112 180)"/>
      </svg>
    </div>
  </div>
  <svg class="gelombang" viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
    <path d="M0 40 C240 100 480 0 720 40 C960 80 1200 10 1440 50 L1440 80 L0 80 Z" fill="#f6f3ea"/>
  </svg>
</section>

<!-- ===== KATALOG ===== -->
<section class="bagian" id="katalog">
  <div class="wadah">
    <div class="judul-bagian">
      <h2>Katalog Tanaman</h2>
      <p>Pilih berdasarkan cahaya di tempat kamu menaruhnya.</p>
    </div>

    <div class="filter-bar">
      <button type="button" class="chip aktif" data-filter="semua">Semua</button>
      <button type="button" class="chip" data-filter="teduh">🌥️ Teduh</button>
      <button type="button" class="chip" data-filter="terang">☀️ Terang</button>
      <button type="button" class="chip acak" id="btn-pilih">🎲 Pilihkan untukku</button>
    </div>
    <div class="kotak-rekomendasi" id="hasil-rekomendasi" hidden></div>
    <p class="info-hasil" id="jumlah-hasil"></p>

    <div class="grid-tanaman">
      <?php foreach ($daftar as $t): ?>
        <article class="kartu-tanaman"
                 data-cahaya="<?= e($t['kebutuhan_cahaya']) ?>"
                 data-nama="<?= e($t['nama']) ?>"
                 data-siram="<?= (int)$t['jadwal_siram'] ?>"
                 data-stok="<?= (int)$t['stok'] ?>">
          <div class="emoji-besar"><?= e($t['emoji']) ?></div>
          <h3><?= e($t['nama']) ?></h3>
          <?php if ($t['nama_latin']): ?><p class="latin"><?= e($t['nama_latin']) ?></p><?php endif; ?>
          <div>
            <span class="lencana <?= e($t['kebutuhan_cahaya']) ?>"><?= $t['kebutuhan_cahaya'] === 'terang' ? '☀️ Terang' : '🌥️ Teduh' ?></span>
            <span class="lencana <?= e($t['tingkat_perawatan']) ?>">Perawatan <?= e($t['tingkat_perawatan']) ?></span>
          </div>
          <p class="deskripsi"><?= e($t['deskripsi']) ?></p>
          <p class="deskripsi">💧 Siram tiap <?= (int)$t['jadwal_siram'] ?> hari</p>
          <div class="harga"><?= rupiah($t['harga']) ?></div>
          <div class="kartu-aksi">
            <?php if ($t['stok'] > 0): ?>
              <a class="tombol kecil" href="pesan.php?id=<?= (int)$t['id'] ?>">Pesan</a>
              <span class="lencana mudah">Stok <?= (int)$t['stok'] ?></span>
            <?php else: ?>
              <span class="lencana habis">Stok habis</span>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="kosong" id="kosong-filter" hidden>Belum ada tanaman untuk filter ini.</p>
    <?php if (!$daftar): ?><p class="kosong">Katalog masih kosong.</p><?php endif; ?>
  </div>
</section>

<!-- ===== TIPS PERAWATAN ===== -->
<section class="bagian gelap" id="tips">
  <div class="wadah">
    <div class="judul-bagian">
      <h2>Tips Perawatan untuk Pemula</h2>
      <p>Empat kebiasaan sederhana supaya tanamanmu awet.</p>
    </div>
    <div class="grid-tips">
      <div class="kartu-tips"><div class="ikon">💧</div><h3>Siram secukupnya</h3><p>Cek tanah dengan jari. Jika 2–3 cm teratas sudah kering, baru siram. Kelebihan air lebih berbahaya daripada kekurangan.</p></div>
      <div class="kartu-tips"><div class="ikon">☀️</div><h3>Kenali cahayanya</h3><p>Tanaman teduh cukup dekat lampu atau jendela redup. Tanaman terang butuh cahaya terang beberapa jam per hari.</p></div>
      <div class="kartu-tips"><div class="ikon">🪴</div><h3>Pot berlubang</h3><p>Pastikan ada lubang drainase supaya air tidak menggenang dan akar tidak busuk.</p></div>
      <div class="kartu-tips"><div class="ikon">🍂</div><h3>Rutin rapikan</h3><p>Potong daun kuning atau kering dan putar pot sesekali agar tumbuh merata.</p></div>
    </div>
  </div>
</section>

<!-- ===== PENGINGAT SIRAM (BONUS) ===== -->
<section class="bagian gelap" style="padding-top:0;border-radius:0">
  <div class="wadah">
    <div class="judul-bagian">
      <h2>Pengingat Jadwal Siram</h2>
      <p>Catat tanamanmu, nanti kami tandai kapan waktunya disiram. Data tersimpan di browser kamu.</p>
    </div>
    <div class="pengingat">
      <form id="form-siram" class="form-baris">
        <label style="flex:2;min-width:180px">Tanaman
          <select id="siram-tanaman">
            <?php foreach ($daftar as $t): ?>
              <option data-siram="<?= (int)$t['jadwal_siram'] ?>"><?= e($t['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label style="flex:1;min-width:110px">Siram tiap (hari)
          <input type="number" id="siram-hari" min="1" max="60" value="7">
        </label>
        <button class="tombol" type="submit">Tambah</button>
      </form>
      <ul class="daftar-siram" id="daftar-siram"></ul>
    </div>
  </div>
</section>

<!-- ===== CARA PESAN ===== -->
<section class="bagian" id="cara-pesan">
  <div class="wadah">
    <div class="judul-bagian"><h2>Cara Pesan</h2><p>Mudah, hanya empat langkah.</p></div>
    <ol class="langkah">
      <li><h3>Daftar / Masuk</h3><p>Buat akun pembeli atau masuk jika sudah punya.</p></li>
      <li><h3>Pilih tanaman</h3><p>Gunakan filter cahaya atau tombol "Pilihkan untukku".</p></li>
      <li><h3>Isi pesanan</h3><p>Tentukan jumlah dan alamat pengiriman.</p></li>
      <li><h3>Tunggu paket</h3><p>Pantau status pesananmu di halaman "Pesanan Saya".</p></li>
    </ol>
    <?php if (!user()): ?>
      <p style="text-align:center;margin-top:1.6rem"><a class="tombol" href="register.php">Daftar Sekarang</a></p>
    <?php endif; ?>
  </div>
</section>

<!-- ===== KONTAK ===== -->
<section class="bagian gelap" id="kontak">
  <div class="wadah">
    <div class="judul-bagian"><h2>Kontak Kami</h2><p>Ada pertanyaan soal tanaman? Hubungi kami.</p></div>
    <div class="kontak-grid">
      <div><div style="font-size:2rem">📱</div><strong>WhatsApp</strong><p>0812-0000-0000</p></div>
      <div><div style="font-size:2rem">✉️</div><strong>Email</strong><p>halo@daunhijau.test</p></div>
      <div><div style="font-size:2rem">📍</div><strong>Alamat</strong><p>Pekanbaru, Riau</p></div>
      <div><div style="font-size:2rem">🕘</div><strong>Jam Buka</strong><p>Setiap hari, 09.00–17.00</p></div>
    </div>
  </div>
</section>

<?php include '_footer.php'; ?>
