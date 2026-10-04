<?php
require 'config.php';

if (user()) {
    header('Location: ' . (user()['role'] === 'admin' ? 'admin_index.php' : 'index.php'));
    exit;
}

$error = '';
$peran = 'pembeli';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $peran = ($_POST['peran'] ?? '') === 'admin' ? 'admin' : 'pembeli';

    $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $u = $st->fetch();

    if ($u && password_verify($pass, $u['password']) && $u['role'] === $peran) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'email' => $u['email'], 'role' => $u['role']];
        $tujuan = $u['role'] === 'admin' ? 'admin_index.php' : ($_SESSION['kembali'] ?? 'index.php');
        unset($_SESSION['kembali']);
        header('Location: ' . $tujuan);
        exit;
    }
    $error = 'Email, password, atau peran tidak cocok.';
}

$judul = 'Masuk';
include '_header.php';
?>
<main class="wadah">
  <div class="kartu-form">
    <h1>Selamat datang 🌿</h1>
    <p>Masuk sebagai penjual (admin) atau pembeli.</p>
    <?php if ($error): ?><div class="notif galat"><?= e($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="on">
      <input type="hidden" name="csrf" value="<?= e(csrf()) ?>">
      <div class="tab-peran">
        <input type="radio" name="peran" id="p-pembeli" value="pembeli" <?= $peran === 'pembeli' ? 'checked' : '' ?>>
        <label for="p-pembeli">Pembeli</label>
        <input type="radio" name="peran" id="p-admin" value="admin" <?= $peran === 'admin' ? 'checked' : '' ?>>
        <label for="p-admin">Admin / Penjual</label>
      </div>
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>
      <p style="margin-top:1.2rem"><button class="tombol" type="submit">Masuk</button></p>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar sebagai pembeli</a></p>
  </div>
</main>
<?php include '_footer.php'; ?>
