<?php
require_once __DIR__ . '/app/bootstrap.php';
require_guest();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');
    if (strlen($name) < 2) $errors[] = 'Nama minimal 2 karakter.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Masukkan email yang valid.';
    if (find_user($email)) $errors[] = 'Email tersebut sudah terdaftar.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $confirmation) $errors[] = 'Konfirmasi password tidak cocok.';
    if (!$errors) {
        $users = read_json(USERS_FILE);
        $users[] = ['id' => bin2hex(random_bytes(5)), 'name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT), 'role' => 'Member', 'created_at' => date('Y-m-d H:i:s')];
        write_json(USERS_FILE, $users);
        flash('success', 'Akun berhasil dibuat. Silakan masuk.');
        redirect('login.php');
    }
}
render_header('Daftar');
?>
<div class="auth-shell"><div class="auth-visual register-visual"><a class="auth-logo" href="index.php"><span class="brand-mark">N</span><span>NUSA<span class="brand-dot">.</span></span></a><div class="visual-copy"><div class="eyebrow light"><span class="eyebrow-line"></span> JOIN THE STUDIO</div><h1>Bring your<br><em>ideas</em><br>to life.</h1><p>Bergabung dengan ruang kolaborasi Nusa dan jadikan ide baik terasa lebih dekat.</p></div><div class="visual-foot">© 2024 Nusa Studio <span>Made with intention.</span></div></div><div class="auth-panel"><div class="auth-form-wrap"><div class="mobile-logo"><span class="brand-mark">N</span><span>NUSA<span class="brand-dot">.</span></span></div><div class="eyebrow"><span class="eyebrow-line"></span> CREATE ACCOUNT</div><h2>Mulai dari<br><em>sini.</em></h2><p class="form-intro">Buat akun untuk mengakses dashboard project Nusa.</p><?php if ($errors): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $error): ?><div><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?><form method="post" novalidate><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><div class="form-floating mb-3"><input class="form-control" type="text" id="name" name="name" placeholder="Nama lengkap" value="<?= e((string) ($_POST['name'] ?? '')) ?>" required><label for="name"><i class="bi bi-person me-2"></i>Nama lengkap</label></div><div class="form-floating mb-3"><input class="form-control" type="email" id="email" name="email" placeholder="nama@email.com" value="<?= e((string) ($_POST['email'] ?? '')) ?>" required><label for="email"><i class="bi bi-envelope me-2"></i>Email address</label></div><div class="form-floating mb-3"><input class="form-control" type="password" id="password" name="password" placeholder="Password" required><label for="password"><i class="bi bi-lock me-2"></i>Password</label></div><div class="form-floating mb-4"><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="Konfirmasi password" required><label for="password_confirmation"><i class="bi bi-shield-check me-2"></i>Konfirmasi password</label></div><button class="btn btn-accent w-100" type="submit">Buat akun <i class="bi bi-arrow-up-right ms-2"></i></button></form><p class="auth-switch">Sudah punya akun? <a href="login.php">Masuk di sini</a></p></div></div></div>
<?php render_footer(); ?>
