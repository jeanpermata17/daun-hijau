<?php
require 'config.php';

if (user()) { header('Location: index.php'); exit; }

$errors = [];
$nama = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (strlen($pass) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($pass !== $pass2) $errors[] = 'Konfirmasi password tidak sama.';

    if (!$errors) {
        $cek = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        } else {
            $pdo->prepare('INSERT INTO users (nama, email, password, role) VALUES (?,?,?,?)')
                ->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT), 'pembeli']);
            flash('Pendaftaran berhasil. Silakan masuk.');
            header('Location: login.php');
            exit;
        }
    }
}

$judul = 'Daftar';
include '_header.php';
?>
<main class="wadah">
  <div class="kartu-form">
    <h1>Daftar Pembeli 🌱</h1>
    <?php foreach ($errors as $er): ?><div class="notif galat"><?= e($er) ?></div><?php endforeach; ?>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
      <label for="nama">Nama lengkap</label>
      <input type="text" id="nama" name="nama" required value="<?= e($nama) ?>">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= e($email) ?>">
      <label for="password">Password (min. 6 karakter)</label>
      <input type="password" id="password" name="password" required>
      <label for="password2">Ulangi password</label>
      <input type="password" id="password2" name="password2" required>
      <p style="margin-top:1.2rem"><button class="tombol" type="submit">Daftar</button></p>
    </form>
    <p>Sudah punya akun? <a href="login.php">Masuk</a></p>
  </div>
</main>
<?php include '_footer.php'; ?>
