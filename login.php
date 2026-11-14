<?php
require_once __DIR__ . '/app/bootstrap.php';
require_guest();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $account = find_user($email);
    if (!$account || !password_verify($password, $account['password'])) {
        $errors[] = 'Email atau password yang dimasukkan salah.';
    } else {
        session_regenerate_id(true);
        unset($account['password']);
        $_SESSION['user'] = $account;
        flash('success', 'Selamat datang kembali, ' . $account['name'] . '.');
        redirect('dashboard.php');
    }
}
render_header('Masuk');
?>
<div class="auth-shell"><div class="auth-visual"><a class="auth-logo" href="index.php"><span class="brand-mark">N</span><span>NUSA<span class="brand-dot">.</span></span></a><div class="visual-copy"><div class="eyebrow light"><span class="eyebrow-line"></span> NUSA STUDIO / PORTAL</div><h1>Good work<br>starts with<br><em>good people.</em></h1><p>Ruang kerja sederhana untuk mengelola setiap cerita brand yang kami bangun bersama.</p></div><div class="visual-foot">© 2024 Nusa Studio <span>Bandung · Jakarta · Bali</span></div></div><div class="auth-panel"><div class="auth-form-wrap"><div class="mobile-logo"><span class="brand-mark">N</span><span>NUSA<span class="brand-dot">.</span></span></div><div class="eyebrow"><span class="eyebrow-line"></span> MEMBER AREA</div><h2>Selamat datang<br><em>kembali.</em></h2><p class="form-intro">Masuk untuk melihat progress dan mengelola project kamu.</p><?php if ($errors): ?><div class="alert alert-danger py-2 small"><?php foreach ($errors as $error): ?><div><i class="bi bi-exclamation-circle me-2"></i><?= e($error) ?></div><?php endforeach; ?></div><?php endif; ?><?php if ($message = flash('success')): ?><div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-2"></i><?= e($message) ?></div><?php endif; ?><form method="post" novalidate><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><div class="form-floating mb-3"><input class="form-control" type="email" id="email" name="email" placeholder="nama@email.com" value="<?= e((string) ($_POST['email'] ?? '')) ?>" required><label for="email"><i class="bi bi-envelope me-2"></i>Email address</label></div><div class="form-floating mb-2"><input class="form-control" type="password" id="password" name="password" placeholder="Password" required><label for="password"><i class="bi bi-lock me-2"></i>Password</label></div><div class="d-flex justify-content-between align-items-center mb-4 small"><label class="remember"><input type="checkbox" name="remember"> Ingat saya</label><a href="mailto:hello@nusastudio.id?subject=Reset%20password" class="muted-link">Lupa password?</a></div><button class="btn btn-accent w-100" type="submit">Masuk ke dashboard <i class="bi bi-arrow-up-right ms-2"></i></button></form><p class="auth-switch">Belum punya akun? <a href="register.php">Daftar sekarang</a></p><div class="demo-note"><i class="bi bi-info-circle me-2"></i><span><strong>Akun demo</strong><br>admin@nusastudio.id · nusa123</span></div></div></div></div>
<?php render_footer(); ?>
