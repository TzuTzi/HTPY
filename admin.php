<?php
/* ============================================================
   admin.php  –  Panou administrator
   Acces: doar utilizatori cu role = 'admin'
   Funcționalități:
     - Listă utilizatori – MySQLi
     - Toate filmele din baza de date – PDO MySQL
     - Jurnal complet de activitate – PDO SQLite
     - Ștergere utilizator (cascadează films)
   ============================================================ */

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/db.php';

require_admin();

$db = get_db();
$messages = [];

/* ── Ștergere utilizator ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'delete_user') {
        $delId = (int)$_POST['user_id'];
        if ($delId !== (int)$_SESSION['user_id']) {
            $stmt = $db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$delId]);
            log_activity($_SESSION['user_id'], $_SESSION['username'], 'admin_delete_user', "id=$delId");
            $messages[] = "User #$delId deleted.";
        } else {
            $messages[] = 'You cannot delete your own account from the admin panel.';
        }
    }

    if ($_POST['action'] === 'change_role') {
        $targetId = (int)$_POST['user_id'];
        $newRole  = $_POST['new_role'] === 'admin' ? 'admin' : 'user';
        $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$newRole, $targetId]);
        log_activity($_SESSION['user_id'], $_SESSION['username'], 'admin_change_role', "id=$targetId role=$newRole");
        $messages[] = "Role for user #$targetId changed to $newRole.";
    }
}

/* ── Toți utilizatorii ── */
$users = $db->query('SELECT id, username, email, role, created_at FROM users ORDER BY id')->fetchAll();

/* ── Toate filmele ── */
$films = $db->query(
    'SELECT f.*, u.username FROM films f JOIN users u ON u.id = f.user_id ORDER BY f.created_at DESC'
)->fetchAll();

/* ── Full activity log – read from SQLite (additional database) ── */
$fullLog = get_sqlite()->query(
    'SELECT username, action, detail, ip, created_at FROM activity_log ORDER BY id DESC LIMIT 50'
)->fetchAll();

$pageTitle = 'Admin';
require_once __DIR__ . '/php/header.php';
?>

<p class="admin-area-banner">Administrative area — manage users, films, and the activity log. Use <strong>Dashboard</strong> in the menu for the member site.</p>

<?php foreach ($messages as $m): ?>
    <div style="background:#0d2a14;border:1px solid #238636;border-radius:6px;
                padding:0.6rem 1rem;margin-bottom:0.75rem;color:#3fb950;font-size:0.875rem;">
        <?= htmlspecialchars($m) ?>
    </div>
<?php endforeach; ?>

<!-- ── Utilizatori ───────────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Users (<?= count($users) ?>)</h2></td>
            </tr>
            <tr><td>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th><th>Username</th><th>Email</th>
                                <th>Role</th><th>Registered</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= htmlspecialchars($u['email']) ?></td>
                                    <td>
                                        <span class="genre-badge"><?= $u['role'] ?></span>
                                    </td>
                                    <td style="font-size:0.8rem;color:var(--color-text-muted);">
                                        <?= date('d.m.Y', strtotime($u['created_at'])) ?>
                                    </td>
                                    <td>
                                        <!-- Schimbă rol -->
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="action" value="change_role">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            <select name="new_role" style="font-size:0.75rem;padding:2px 4px;">
                                                <option value="user"  <?= $u['role']==='user'  ? 'selected':'' ?>>user</option>
                                                <option value="admin" <?= $u['role']==='admin' ? 'selected':'' ?>>admin</option>
                                            </select>
                                            <button type="submit" class="btn-secondary"
                                                    style="font-size:0.75rem;padding:0.15rem 0.5rem;">
                                                Set
                                            </button>
                                        </form>
                                        <!-- Delete -->
                                        <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                                            <form method="post" style="display:inline;margin-left:4px;">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="btn-danger"
                                                        style="font-size:0.75rem;padding:0.15rem 0.5rem;"
                                                        onclick="return confirm('Delete this user?')">
                                                    Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Toate filmele ─────────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>All Films (<?= count($films) ?>)</h2></td>
            </tr>
            <tr><td>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th><th>Year</th><th>Genre</th>
                                <th>Rating</th><th>Mood</th><th>Added by</th><th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($films as $f): ?>
                                <tr>
                                    <td><?= htmlspecialchars($f['title']) ?></td>
                                    <td><?= $f['year'] ?></td>
                                    <td><span class="genre-badge"><?= htmlspecialchars($f['genre']) ?></span></td>
                                    <td><?= $f['rating'] ?></td>
                                    <td><span class="mood-tag mood-<?= htmlspecialchars($f['mood']) ?>"><?= htmlspecialchars($f['mood']) ?></span></td>
                                    <td><?= htmlspecialchars($f['username']) ?></td>
                                    <td style="font-size:0.8rem;color:var(--color-text-muted);">
                                        <?= date('d.m.Y', strtotime($f['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Jurnal activitate ─────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Activity Log (last 50)</h2></td>
            </tr>
            <tr><td>
                <div style="overflow-x:auto;">
                    <table class="data-table">
                        <thead>
                            <tr><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fullLog as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['username']) ?></td>
                                    <td><?= htmlspecialchars($log['action']) ?></td>
                                    <td><?= htmlspecialchars($log['detail']) ?></td>
                                    <td style="font-size:0.8rem;color:var(--color-text-muted);"><?= htmlspecialchars($log['ip']) ?></td>
                                    <td style="font-size:0.8rem;color:var(--color-text-muted);"><?= $log['created_at'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </td></tr>
        </table>
    </td></tr>
</table>

<?php require_once __DIR__ . '/php/footer.php'; ?>
