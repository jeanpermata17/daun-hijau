// ===== Interaksi halaman Daun Hijau =====
(function () {
  const kartu = Array.from(document.querySelectorAll('.kartu-tanaman'));

  // ---- Animasi muncul saat di-scroll ----
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entri) => {
      entri.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('tampil'), i * 70);
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    kartu.forEach((k) => io.observe(k));
  } else {
    kartu.forEach((k) => k.classList.add('tampil'));
  }

  // ---- Filter berdasarkan kebutuhan cahaya ----
  const tombolFilter = Array.from(document.querySelectorAll('[data-filter]'));
  const info = document.getElementById('jumlah-hasil');
  const kosong = document.getElementById('kosong-filter');
  let filterAktif = 'semua';

  function terapkanFilter() {
    let n = 0;
    kartu.forEach((k) => {
      const cocok = filterAktif === 'semua' || k.dataset.cahaya === filterAktif;
      k.hidden = !cocok;
      k.classList.remove('sorot');
      if (cocok) {
        n++;
        // pastikan kartu yang baru muncul ikut terlihat
        requestAnimationFrame(() => k.classList.add('tampil'));
      }
    });
    if (info) info.textContent = n + ' tanaman ditampilkan';
    if (kosong) kosong.hidden = n !== 0;
  }

  tombolFilter.forEach((t) => {
    t.addEventListener('click', () => {
      filterAktif = t.dataset.filter;
      tombolFilter.forEach((x) => x.classList.toggle('aktif', x === t));
      const hasil = document.getElementById('hasil-rekomendasi');
      if (hasil) hasil.hidden = true;
      terapkanFilter();
    });
  });
  terapkanFilter();

  // ---- Tombol "Pilihkan untukku" ----
  const tombolAcak = document.getElementById('btn-pilih');
  const kotak = document.getElementById('hasil-rekomendasi');
  if (tombolAcak && kotak) {
    tombolAcak.addEventListener('click', () => {
      const kandidat = kartu.filter((k) => !k.hidden && Number(k.dataset.stok) > 0);
      kotak.hidden = false;
      if (kandidat.length === 0) {
        kotak.textContent = 'Belum ada tanaman yang cocok dan tersedia. Coba ganti filter ya.';
        return;
      }
      const pilih = kandidat[Math.floor(Math.random() * kandidat.length)];
      kartu.forEach((k) => k.classList.remove('sorot'));
      pilih.classList.add('sorot');
      pilih.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const cahaya = pilih.dataset.cahaya === 'terang' ? 'tempat terang' : 'tempat teduh';
      kotak.textContent = '🌟 Coba ' + pilih.dataset.nama + '! Cocok di ' + cahaya +
        ' dan cukup disiram setiap ' + pilih.dataset.siram + ' hari.';
    });
  }

  // ---- Pengingat jadwal siram (disimpan di browser) ----
  const form = document.getElementById('form-siram');
  const daftar = document.getElementById('daftar-siram');
  if (!form || !daftar) return;

  const KUNCI = 'daunhijau_siram';
  const pilihan = document.getElementById('siram-tanaman');
  const inputHari = document.getElementById('siram-hari');

  function muat() {
    try { return JSON.parse(localStorage.getItem(KUNCI)) || []; } catch (e) { return []; }
  }
  function simpan(data) {
    try { localStorage.setItem(KUNCI, JSON.stringify(data)); } catch (e) { /* abaikan */ }
  }
  function hariIni() {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), d.getDate()).getTime();
  }
  const HARI = 86400000;

  function gambar() {
    const data = muat();
    daftar.textContent = '';
    if (data.length === 0) {
      const li = document.createElement('li');
      li.textContent = 'Belum ada pengingat. Tambahkan tanaman di atas 🌱';
      daftar.appendChild(li);
      return;
    }
    data.forEach((item, idx) => {
      const sisa = item.interval - Math.floor((hariIni() - item.terakhir) / HARI);
      const li = document.createElement('li');
      if (sisa <= 0) li.classList.add('telat');

      const kiri = document.createElement('div');
      const nama = document.createElement('strong');
      nama.textContent = item.nama;
      const status = document.createElement('div');
      status.className = 'status-siram';
      status.textContent = sisa <= 0
        ? '💧 Waktunya disiram hari ini!'
        : 'Siram lagi ' + sisa + ' hari lagi (tiap ' + item.interval + ' hari)';
      kiri.appendChild(nama);
      kiri.appendChild(status);

      const kanan = document.createElement('div');
      kanan.className = 'aksi-tabel';
      const btnSiram = document.createElement('button');
      btnSiram.type = 'button';
      btnSiram.className = 'tombol kecil';
      btnSiram.textContent = 'Sudah disiram';
      btnSiram.addEventListener('click', () => {
        const d = muat();
        d[idx].terakhir = hariIni();
        simpan(d);
        gambar();
      });
      const btnHapus = document.createElement('button');
      btnHapus.type = 'button';
      btnHapus.className = 'tombol kecil garis';
      btnHapus.textContent = 'Hapus';
      btnHapus.addEventListener('click', () => {
        const d = muat();
        d.splice(idx, 1);
        simpan(d);
        gambar();
      });
      kanan.appendChild(btnSiram);
      kanan.appendChild(btnHapus);

      li.appendChild(kiri);
      li.appendChild(kanan);
      daftar.appendChild(li);
    });
  }

  pilihan.addEventListener('change', () => {
    const opt = pilihan.options[pilihan.selectedIndex];
    if (opt && opt.dataset.siram) inputHari.value = opt.dataset.siram;
  });
  pilihan.dispatchEvent(new Event('change'));

  form.addEventListener('submit', (ev) => {
    ev.preventDefault();
    const opt = pilihan.options[pilihan.selectedIndex];
    const interval = Math.max(1, parseInt(inputHari.value, 10) || 1);
    const data = muat();
    data.push({ nama: opt.textContent.trim(), interval: interval, terakhir: hariIni() });
    simpan(data);
    gambar();
  });

  gambar();
})();
