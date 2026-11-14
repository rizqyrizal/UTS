<?php
require_once __DIR__ . '/app/bootstrap.php';
require_auth();

$user = user();
$projects = read_json(PROJECTS_FILE);
$members = array_map(static function (array $item): array {
    unset($item['password']);
    return $item;
}, read_json(USERS_FILE));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'create') {
        $projects[] = [
            'id' => bin2hex(random_bytes(5)),
            'name' => trim((string) ($_POST['name'] ?? 'Untitled Project')),
            'category' => trim((string) ($_POST['category'] ?? 'General')),
            'status' => (string) ($_POST['status'] ?? 'Berjalan'),
            'deadline' => (string) ($_POST['deadline'] ?? date('Y-m-d')),
            'owner' => trim((string) ($_POST['owner'] ?? $user['name'])),
        ];
        flash('success', 'Project baru berhasil ditambahkan.');
    }

    if ($action === 'update') {
        $id = (string) ($_POST['id'] ?? '');
        foreach ($projects as &$project) {
            if ($project['id'] === $id) {
                $project['name'] = trim((string) ($_POST['name'] ?? $project['name']));
                $project['category'] = trim((string) ($_POST['category'] ?? $project['category']));
                $project['status'] = (string) ($_POST['status'] ?? $project['status']);
                $project['deadline'] = (string) ($_POST['deadline'] ?? $project['deadline']);
                $project['owner'] = trim((string) ($_POST['owner'] ?? $project['owner']));
            }
        }
        unset($project);
        flash('success', 'Perubahan project berhasil disimpan.');
    }

    if ($action === 'delete') {
        $id = (string) ($_POST['id'] ?? '');
        $projects = array_values(array_filter($projects, static fn (array $p): bool => $p['id'] !== $id));
        flash('success', 'Project berhasil dihapus.');
    }

    write_json(PROJECTS_FILE, $projects);
    redirect('dashboard.php');
}

$statusStyles = ['Berjalan' => 'status-running', 'Selesai' => 'status-done', 'Review' => 'status-review', 'Pause' => 'status-pause'];
$counts = ['total' => count($projects), 'running' => 0, 'done' => 0, 'review' => 0];
foreach ($projects as $project) {
    $counts['running'] += $project['status'] === 'Berjalan' ? 1 : 0;
    $counts['done'] += $project['status'] === 'Selesai' ? 1 : 0;
    $counts['review'] += $project['status'] === 'Review' ? 1 : 0;
}

render_header('Dashboard', true);
?>
<div class="dashboard-shell">
  <aside class="dash-sidebar">
    <nav class="sidebar-nav">
      <span class="sidebar-label">Menu</span>
      <a class="sidebar-link active" href="dashboard.php"><i class="bi bi-grid-1x2"></i> Dashboard</a>
      <a class="sidebar-link" href="#projects"><i class="bi bi-briefcase"></i> Projects</a>
      <a class="sidebar-link" href="#team"><i class="bi bi-people"></i> Team</a>
      <span class="sidebar-label mt-4">Lainnya</span>
      <a class="sidebar-link" href="profil.html" target="_blank" rel="noopener"><i class="bi bi-globe2"></i> Website profil</a>
      <a class="sidebar-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>
    </nav>
    <div class="sidebar-user">
      <span class="avatar"><?= e(mb_substr($user['name'], 0, 1)) ?></span>
      <div><strong><?= e($user['name']) ?></strong><span><?= e($user['role']) ?></span></div>
    </div>
  </aside>

  <main class="dash-main">
    <header class="dash-header">
      <div>
        <div class="eyebrow"><span class="eyebrow-line"></span> STUDIO DASHBOARD</div>
        <h1>Halo, <?= e(explode(' ', $user['name'])[0]) ?> <em>!</em></h1>
        <p>Berikut ringkasan project yang sedang dikerjakan Nusa Studio.</p>
      </div>
      <button class="btn btn-accent" data-bs-toggle="modal" data-bs-target="#createProject"><i class="bi bi-plus-lg me-2"></i>Project baru</button>
    </header>

    <?php if ($message = flash('success')): ?><div class="alert alert-success py-2 small"><i class="bi bi-check-circle me-2"></i><?= e($message) ?></div><?php endif; ?>

    <section class="stat-cards">
      <article class="stat-card"><span>Total project</span><strong><?= $counts['total'] ?></strong><i class="bi bi-briefcase"></i></article>
      <article class="stat-card accent-coral"><span>Berjalan</span><strong><?= $counts['running'] ?></strong><i class="bi bi-lightning-charge"></i></article>
      <article class="stat-card accent-yellow"><span>Review</span><strong><?= $counts['review'] ?></strong><i class="bi bi-eye"></i></article>
      <article class="stat-card accent-ink"><span>Selesai</span><strong><?= $counts['done'] ?></strong><i class="bi bi-check2-circle"></i></article>
    </section>

    <section id="projects" class="panel">
      <div class="panel-head">
        <div><h2>Daftar Project</h2><p>Kelola status dan pemilik setiap project.</p></div>
        <div class="panel-search"><i class="bi bi-search"></i><input type="search" id="projectSearch" placeholder="Cari project..."></div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle project-table" id="projectTable">
          <thead><tr><th>Project</th><th>Kategori</th><th>Status</th><th>Pemilik</th><th>Deadline</th><th class="text-end">Aksi</th></tr></thead>
          <tbody>
          <?php if (!$projects): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada project.</td></tr>
          <?php endif; ?>
          <?php foreach ($projects as $project): $style = $statusStyles[$project['status']] ?? 'status-pause'; ?>
            <tr>
              <td><div class="project-name"><span class="project-initial"><?= e(mb_strtoupper(mb_substr($project['name'], 0, 2))) ?></span><strong><?= e($project['name']) ?></strong></div></td>
              <td><?= e($project['category']) ?></td>
              <td><span class="status-pill <?= e($style) ?>"><?= e($project['status']) ?></span></td>
              <td><?= e($project['owner']) ?></td>
              <td><?= e(date('d M Y', strtotime($project['deadline']))) ?></td>
              <td class="text-end">
                <button class="btn btn-sm btn-light edit-project" title="Edit"
                  data-bs-toggle="modal" data-bs-target="#editProject"
                  data-id="<?= e($project['id']) ?>" data-name="<?= e($project['name']) ?>"
                  data-category="<?= e($project['category']) ?>" data-status="<?= e($project['status']) ?>"
                  data-deadline="<?= e($project['deadline']) ?>" data-owner="<?= e($project['owner']) ?>"><i class="bi bi-pencil"></i></button>
                <form method="post" class="d-inline" onsubmit="return confirm('Hapus project ini?')">
                  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= e($project['id']) ?>">
                  <button class="btn btn-sm btn-light text-danger" title="Hapus"><i class="bi bi-trash3"></i></button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section id="team" class="panel">
      <div class="panel-head"><div><h2>Anggota Tim</h2><p>Pengguna yang memiliki akses ke dashboard.</p></div><span class="role-pill"><?= count($members) ?> akun aktif</span></div>
      <div class="team-grid">
        <?php foreach ($members as $member): ?>
          <article class="team-card"><span class="avatar"><?= e(mb_substr($member['name'], 0, 1)) ?></span><div><strong><?= e($member['name']) ?></strong><span><?= e($member['email']) ?></span></div><span class="role-pill"><?= e($member['role']) ?></span></article>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</div>

<div class="modal fade" id="createProject" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title">Tambah project</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create"><?php $formPrefix = 'create'; require __DIR__ . '/app/project-form.php'; ?></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-accent" type="submit">Simpan project</button></div></form></div></div></div>

<div class="modal fade" id="editProject" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><form method="post"><div class="modal-header"><h5 class="modal-title">Edit project</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="update"><input type="hidden" name="id" id="edit-id"><?php $formPrefix = 'edit'; require __DIR__ . '/app/project-form.php'; ?></div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-accent" type="submit">Simpan perubahan</button></div></form></div></div></div>
<?php render_footer(); ?>
