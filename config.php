<?php
// Mulai sesi agar kita bisa mengingat status login pengguna
session_start();


// ==========================================
// 1. PENGATURAN DATABASE
// ==========================================
$DB_HOST = 'localhost';
$DB_NAME = 'toko_tanaman';
$DB_USER = 'root';
$DB_PASS = '';

// Mencoba melakukan koneksi ke database
try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    
    // Atur agar PDO menampilkan error (Exception) jika terjadi masalah di database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Atur agar data hasil query berbentuk array (nama_kolom => nilai)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $ex) {
    // Jika koneksi gagal, hentikan program dan tampilkan pesan error
    die('Koneksi database gagal. Pastikan database.sql sudah diimpor dengan benar.');
}


// ==========================================
// 2. FUNGSI-FUNGSI BANTUAN UMUM
// ==========================================

/**
 * Mencegah serangan XSS dengan mengubah karakter spesial (seperti < atau >) menjadi teks biasa
 */
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

/**
 * Mengubah angka menjadi format mata uang Rupiah
 */
function rupiah($angka) {
    return 'Rp ' . number_format((int)$angka, 0, ',', '.');
}

/**
 * Mengambil data pengguna yang sedang login saat ini
 */
function user() {
    if (isset($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    return null;
}

/**
 * Mengecek apakah pengguna sudah login, jika belum arahkan ke halaman login
 */
function wajib_login($role = null) {
    // Jika tidak ada user yang login
    if (!user()) {
        $_SESSION['kembali'] = $_SERVER['REQUEST_URI'] ?? 'index.php';
        header('Location: login.php');
        exit;
    }
    
    // Jika halaman butuh peran tertentu (misal: 'admin') tapi user bukan admin
    if ($role != null && user()['role'] !== $role) {
        http_response_code(403);
        die('Akses ditolak. Halaman ini hanya untuk ' . e($role) . '. <a href="index.php">Kembali</a>');
    }
}

/**
 * Membuat token keamanan form (CSRF) agar aman dari serangan
 */
function csrf() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

/**
 * Mengecek apakah token form yang dikirim valid (cocok dengan token di sesi)
 */
function cek_csrf() {
    $token_di_sesi = $_SESSION['csrf'] ?? '';
    $token_dari_form = $_POST['csrf'] ?? '';

    if (!hash_equals($token_di_sesi, $token_dari_form)) {
        http_response_code(400);
        die('Token keamanan tidak valid. Silakan kembali dan coba lagi.');
    }
}

/**
 * Menyimpan dan menampilkan pesan singkat (flash message) seperti pesan sukses
 */
function flash($pesan = null) {
    // Jika ada pesan yang dikirimkan, simpan ke sesi
    if ($pesan !== null) { 
        $_SESSION['flash'] = $pesan; 
        return null; 
    }
    
    // Jika tidak ada pesan yang dikirim, ambil pesan lama lalu hapus dari sesi
    $pesan_lama = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $pesan_lama;
}


// ==========================================
// 3. FUNGSI-FUNGSI KHUSUS TANAMAN
// ==========================================

/**
 * Mengambil dan mengecek (validasi) data form tanaman (untuk fitur Tambah / Edit)
 */
function input_tanaman() {
    $emoji = trim($_POST['emoji'] ?? '');
    
    $data = [
        'nama'              => trim($_POST['nama'] ?? ''),
        'nama_latin'        => trim($_POST['nama_latin'] ?? ''),
        'emoji'             => $emoji !== '' ? mb_substr($emoji, 0, 4) : '🌿',
        'kebutuhan_cahaya'  => $_POST['kebutuhan_cahaya'] ?? 'teduh',
        'tingkat_perawatan' => $_POST['tingkat_perawatan'] ?? 'mudah',
        'harga'             => (int)($_POST['harga'] ?? 0),
        'stok'              => (int)($_POST['stok'] ?? 0),
        'jadwal_siram'      => max(1, (int)($_POST['jadwal_siram'] ?? 3)),
        'deskripsi'         => trim($_POST['deskripsi'] ?? ''),
    ];
    
    $error = [];
    
    if ($data['nama'] === '') {
        $error[] = 'Nama tanaman wajib diisi.';
    }
    if (!in_array($data['kebutuhan_cahaya'], ['teduh', 'terang'], true)) {
        $error[] = 'Kebutuhan cahaya tidak valid.';
    }
    if (!in_array($data['tingkat_perawatan'], ['mudah', 'sedang'], true)) {
        $error[] = 'Tingkat perawatan tidak valid.';
    }
    if ($data['harga'] < 0) {
        $error[] = 'Harga tidak boleh negatif.';
    }
    if ($data['stok'] < 0) {
        $error[] = 'Stok tidak boleh negatif.';
    }
    
    return [$data, $error];
}

/**
 * Memberikan nilai bawaan (kosong) saat form tambah tanaman pertama kali dibuka
 */
function tanaman_kosong() {
    return [
        'nama'              => '', 
        'nama_latin'        => '', 
        'emoji'             => '🌿',
        'kebutuhan_cahaya'  => 'teduh', 
        'tingkat_perawatan' => 'mudah',
        'harga'             => 0, 
        'stok'              => 0, 
        'jadwal_siram'      => 7, 
        'deskripsi'         => '',
    ];
}
