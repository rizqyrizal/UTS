<?php
declare(strict_types=1);

const APP_NAME = 'Nusa Studio';
const STORAGE_PATH = __DIR__ . '/../storage';
const USERS_FILE = STORAGE_PATH . '/users.json';
const PROJECTS_FILE = STORAGE_PATH . '/projects.json';

date_default_timezone_set('Asia/Jakarta');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('nusa_studio_session');
    session_start();
}

function ensure_storage(): void
{
    if (!is_dir(STORAGE_PATH)) {
        mkdir(STORAGE_PATH, 0755, true);
    }

    if (!file_exists(USERS_FILE)) {
        write_json(USERS_FILE, [[
            'id' => bin2hex(random_bytes(5)),
            'name' => 'Admin Nusa',
            'email' => 'admin@nusastudio.id',
            'password' => password_hash('nusa123', PASSWORD_DEFAULT),
            'role' => 'Administrator',
            'created_at' => date('Y-m-d H:i:s'),
        ]]);
    }

    if (!file_exists(PROJECTS_FILE)) {
        write_json(PROJECTS_FILE, [
            ['id' => bin2hex(random_bytes(5)), 'name' => 'Rona Skincare', 'category' => 'Brand Identity', 'status' => 'Berjalan', 'deadline' => '2024-12-20', 'owner' => 'Nusa Team'],
            ['id' => bin2hex(random_bytes(5)), 'name' => 'Bara Coffee', 'category' => 'Campaign', 'status' => 'Selesai', 'deadline' => '2024-11-30', 'owner' => 'Nusa Team'],
            ['id' => bin2hex(random_bytes(5)), 'name' => 'Loka Living', 'category' => 'Digital Experience', 'status' => 'Review', 'deadline' => '2024-12-28', 'owner' => 'Nusa Team'],
        ]);
    }
}

function read_json(string $file): array
{
    if (!file_exists($file)) {
        return [];
    }
    $data = json_decode((string) file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

function write_json(string $file, array $data): void
{
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        exit('Token keamanan tidak valid. Silakan kembali dan coba lagi.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (!user()) {
        redirect('login.php');
    }
}

function require_guest(): void
{
    if (user()) {
        redirect('dashboard.php');
    }
}

function find_user(string $email): ?array
{
    foreach (read_json(USERS_FILE) as $item) {
        if (strtolower($item['email']) === strtolower(trim($email))) {
            return $item;
        }
    }
    return null;
}

function render_header(string $title, bool $dashboard = false): void
{
    $currentUser = user();
    $pageTitle = e($title . ' — ' . APP_NAME);
    echo "<!doctype html><html lang='id'><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>{$pageTitle}</title><link rel='stylesheet' href='assets/css/bootstrap.min.css'><link rel='stylesheet' href='assets/css/bootstrap-icons.min.css'><link rel='stylesheet' href='assets/css/app.css'></head><body class='" . ($dashboard ? 'dashboard-body' : 'auth-body') . "'>";
    if ($dashboard) {
        echo "<nav class='navbar navbar-expand-lg dash-nav'><div class='container-fluid px-4'><a class='navbar-brand d-flex align-items-center gap-2' href='dashboard.php'><span class='brand-mark'>N</span><span>NUSA<span class='brand-dot'>.</span></span></a><div class='d-flex align-items-center gap-3'><span class='user-greeting d-none d-md-inline'>Halo, " . e($currentUser['name']) . "</span><a class='btn btn-outline-dark btn-sm' href='logout.php'><i class='bi bi-box-arrow-right me-1'></i>Keluar</a></div></div></nav>";
    }
}

function render_footer(): void
{
    echo "<script src='assets/js/bootstrap.bundle.min.js'></script><script src='assets/js/app.js'></script></body></html>";
}

ensure_storage();
