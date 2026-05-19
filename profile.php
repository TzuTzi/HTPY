<?php
/* ============================================================
   profile.php  –  Authenticated user profile
   Features:
     - View/edit account data (pre-filled from DB)
     - Avatar upload / delete
     - User film list – PDO PostgreSQL
     - Add new film
     - Recent activity log
   ============================================================ */

require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/db.php';

require_login();

$userId   = $_SESSION['user_id'];
$username = $_SESSION['username'];
$db       = get_db();

$messages = [];
$errors   = [];

/* ============================================================
   VULNERABILITY 3 — CSRF (intentionally vulnerable)
   The delete_film form has NO CSRF token, so any external page
   can silently submit it on behalf of a logged-in user.

   Exploit (save as attacker.html and open while logged in):
   <form action="http://localhost/HTPY/profile.php" method="post" id="f">
       <input type="hidden" name="action" value="delete_film">
       <input type="hidden" name="film_id" value="1">
   </form>
   <script>document.getElementById('f').submit();</script>

   ── SECURE version: generate a token and validate it ──
   To patch: uncomment the two blocks below (generation + validation)
   and add <?= $csrfField ?> inside every sensitive form.
   ============================================================ */

// SECURE — CSRF token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfField = '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';

// SECURE — CSRF token validation on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        die('CSRF token mismatch — request blocked.');
    }
}

/* ── VULNERABLE version (remove the blocks above and uncomment below to demo CSRF) ──
if (empty($_SESSION['csrf_token'])) {}
$csrfField = '';
── END VULNERABLE ── */

/* ── POST handling ──────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* 1. Update profile */
    if (isset($_POST['action']) && $_POST['action'] === 'update_profile') {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address.';
        } else {
            $stmt = $db->prepare('UPDATE users SET email = ? WHERE id = ?');
            $stmt->execute([$email, $userId]);
            log_activity($userId, $username, 'update_profile', '');
            $messages[] = 'Profile updated.';
        }
    }

    
    if (isset($_POST['action']) && $_POST['action'] === 'upload_avatar') {
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

        $file = $_FILES['avatar'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload error.';
        } elseif ($file['size'] > MAX_FILE_SIZE) {
            $errors[] = 'File exceeds 2 MB limit.';
        // SECURE — MIME type check
        } elseif (!in_array(mime_content_type($file['tmp_name']), ALLOWED_TYPES)) {
            $errors[] = 'Unsupported file type. Use jpg, png, gif or webp.';
        /* ── VULNERABLE version (remove the elseif above and uncomment to demo file upload) ──
        } elseif (false) {
        ── END VULNERABLE ── */
        } else {
            $row = $db->query("SELECT avatar_path FROM users WHERE id = $userId")->fetch();
            if ($row['avatar_path'] && file_exists(UPLOAD_DIR . $row['avatar_path'])) {
                unlink(UPLOAD_DIR . $row['avatar_path']);
            }
            // VULNERABLE: uses original filename extension (attacker controls it)
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $filename);

            $stmt = $db->prepare('UPDATE users SET avatar_path = ? WHERE id = ?');
            $stmt->execute([$filename, $userId]);
            log_activity($userId, $username, 'upload_avatar', $filename);
            $messages[] = 'Profile picture updated.';
        }
    }

    /* 3. Delete avatar */
    if (isset($_POST['action']) && $_POST['action'] === 'delete_avatar') {
        $row = $db->query("SELECT avatar_path FROM users WHERE id = $userId")->fetch();
        if ($row['avatar_path']) {
            $path = UPLOAD_DIR . $row['avatar_path'];
            if (file_exists($path)) unlink($path);
            $stmt = $db->prepare('UPDATE users SET avatar_path = NULL WHERE id = ?');
            $stmt->execute([$userId]);
            log_activity($userId, $username, 'delete_avatar', '');
            $messages[] = 'Profile picture removed.';
        }
    }

    /* 4. Add film */
    if (isset($_POST['action']) && $_POST['action'] === 'add_film') {
        $title  = trim($_POST['title']  ?? '');
        $year   = (int)($_POST['year']  ?? 0);
        $genre  = trim($_POST['genre']  ?? '');
        $rating = (float)($_POST['rating'] ?? 0);
        $mood   = trim($_POST['mood']   ?? '');

        if ($title === '' || $year < 1888 || $year > 2030) {
            $errors[] = 'Title and a valid year are required.';
        } else {
            $stmt = $db->prepare(
                'INSERT INTO films (title, year, genre, rating, mood, user_id)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$title, $year, $genre, $rating, $mood, $userId]);
            log_activity($userId, $username, 'add_film', $title);
            $messages[] = 'Film "' . $title . '" added.';
        }
    }

    /* 5. Delete film */
    if (isset($_POST['action']) && $_POST['action'] === 'delete_film') {
        $filmId = (int)($_POST['film_id'] ?? 0);
        $stmt   = $db->prepare('DELETE FROM films WHERE id = ? AND user_id = ?');
        $stmt->execute([$filmId, $userId]);
        log_activity($userId, $username, 'delete_film', "id=$filmId");
        $messages[] = 'Film removed.';
    }
}

/* ── Fetch user data (pre-fill form) ── */
$userRow = $db->query("SELECT * FROM users WHERE id = $userId")->fetch();

/* ── User films ── */
$filmStmt = $db->prepare('SELECT * FROM films WHERE user_id = ? ORDER BY created_at DESC');
$filmStmt->execute([$userId]);
$userFilms = $filmStmt->fetchAll();

/* ── Recent activity – read from SQLite (additional database) ── */
$logStmt = get_sqlite()->prepare(
    'SELECT action, detail, created_at FROM activity_log
     WHERE user_id = ? ORDER BY id DESC LIMIT 10'
);
$logStmt->execute([$userId]);
$activityLog = $logStmt->fetchAll();

$pageTitle = 'My Profile';
require_once __DIR__ . '/php/header.php';
?>

<?php foreach ($messages as $m): ?>
    <div style="background:#0d2a14;border:1px solid #238636;border-radius:6px;
                padding:0.6rem 1rem;margin-bottom:0.75rem;color:#3fb950;font-size:0.875rem;">
        <?= htmlspecialchars($m) ?>
    </div>
<?php endforeach; ?>
<?php foreach ($errors as $e): ?>
    <div style="background:#3d1a1a;border:1px solid #f85149;border-radius:6px;
                padding:0.6rem 1rem;margin-bottom:0.75rem;color:#f85149;font-size:0.875rem;">
        <?= htmlspecialchars($e) ?>
    </div>
<?php endforeach; ?>

<!-- ── Profile section ────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>My Profile</h2></td>
            </tr>
            <tr><td>

                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <!-- Avatar -->
                        <td style="width:140px;vertical-align:top;border:none;padding:0 1.5rem 0 0;">
                            <?php if ($userRow['avatar_path']): ?>
                                <img src="<?= UPLOAD_URL . htmlspecialchars($userRow['avatar_path']) ?>"
                                     alt="Avatar" style="width:110px;height:110px;object-fit:cover;
                                     border-radius:50%;border:2px solid var(--color-border);">
                            <?php else: ?>
                                <div style="width:110px;height:110px;border-radius:50%;
                                            background:var(--color-surface);border:2px solid var(--color-border);
                                            display:flex;align-items:center;justify-content:center;
                                            font-size:2.5rem;color:var(--color-text-muted);">
                                    &#128100;
                                </div>
                            <?php endif; ?>

                            <form method="post" enctype="multipart/form-data" style="margin-top:0.75rem;">
                                <?= $csrfField ?>
                                <input type="hidden" name="action" value="upload_avatar">
                                <input type="file" name="avatar" accept="image/*"
                                       style="font-size:0.75rem;color:var(--color-text-muted);width:110px;">
                                <button type="submit" class="btn-secondary"
                                        style="margin-top:0.4rem;width:110px;font-size:0.75rem;">
                                    Upload
                                </button>
                            </form>

                            <?php if ($userRow['avatar_path']): ?>
                                <form method="post" style="margin-top:0.3rem;">
                                    <?= $csrfField ?>
                                    <input type="hidden" name="action" value="delete_avatar">
                                    <button type="submit" class="btn-danger"
                                            style="width:110px;font-size:0.75rem;">
                                        Remove photo
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>

                        <!-- Account form (pre-filled from DB) -->
                        <td style="vertical-align:top;border:none;padding:0;">
                            <form method="post">
                                <?= $csrfField ?>
                                <input type="hidden" name="action" value="update_profile">
                                <table class="form-table">
                                    <tr>
                                        <td class="form-label">Username</td>
                                        <td class="form-field">
                                            <input type="text"
                                                   value="<?= htmlspecialchars($userRow['username']) ?>"
                                                   readonly style="color:var(--color-text-muted);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="form-label"><label for="p-email">Email</label></td>
                                        <td class="form-field">
                                            <input type="email" id="p-email" name="email"
                                                   value="<?= htmlspecialchars($userRow['email']) ?>"
                                                   required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="form-label">Role</td>
                                        <td class="form-field">
                                            <input type="text"
                                                   value="<?= htmlspecialchars($userRow['role']) ?>"
                                                   readonly style="color:var(--color-text-muted);">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="form-label">Member since</td>
                                        <td class="form-field">
                                            <input type="text"
                                                   value="<?= date('d.m.Y', strtotime($userRow['created_at'])) ?>"
                                                   readonly style="color:var(--color-text-muted);">
                                        </td>
                                    </tr>
                                </table>
                                <div class="form-actions" style="margin-top:0.8rem;">
                                    <button type="submit" class="btn-primary">Save</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                </table>

            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Add new film ───────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Add a Film</h2></td>
            </tr>
            <tr><td>
                <form method="post">
                    <?= $csrfField ?>
                    <input type="hidden" name="action" value="add_film">
                    <table class="form-table">
                        <tr>
                            <td class="form-label"><label for="f-title">Title *</label></td>
                            <td class="form-field">
                                <input type="text" id="f-title" name="title" required>
                            </td>
                            <td class="form-label"><label for="f-year">Year *</label></td>
                            <td class="form-field">
                                <input type="number" id="f-year" name="year"
                                       min="1888" max="2030" value="<?= date('Y') ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="form-label"><label for="f-genre">Genre</label></td>
                            <td class="form-field">
                                <select id="f-genre" name="genre">
                                    <option value="">– select –</option>
                                    <?php foreach (['Action','Comedy','Drama','Horror','Sci-Fi','Thriller','Romance','Animation'] as $g): ?>
                                        <option value="<?= $g ?>"><?= $g ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="form-label"><label for="f-rating">Rating</label></td>
                            <td class="form-field">
                                <input type="number" id="f-rating" name="rating"
                                       min="0" max="10" step="0.1" value="7.0" style="width:80px;">
                                <small style="color:var(--color-text-muted);">/ 10</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="form-label"><label for="f-mood">Mood</label></td>
                            <td class="form-field" colspan="3">
                                <select id="f-mood" name="mood">
                                    <option value="">– select –</option>
                                    <?php foreach (['happy','tense','adventurous','melancholic'] as $m): ?>
                                        <option value="<?= $m ?>"><?= ucfirst($m) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="form-actions" style="margin-top:0.8rem;">
                        <button type="submit" class="btn-primary">Add Film</button>
                    </div>
                </form>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── My Films ───────────────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>My Films (<?= count($userFilms) ?>)</h2></td>
            </tr>
            <tr><td>
                <?php if (empty($userFilms)): ?>
                    <p style="color:var(--color-text-muted);">You haven't added any films yet.</p>
                <?php else: ?>
                    <div style="overflow-x:auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th><th>Year</th><th>Genre</th>
                                    <th>Rating</th><th>Mood</th><th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($userFilms as $f): ?>
                                    <tr>
                                        <?php
                                        /* ============================================================
                                           VULNERABILITY 2 — XSS (intentionally vulnerable)
                                           Exploit: add a film with title:
                                                    <script>alert('XSS')</script>
                                                    or: <img src=x onerror="alert(document.cookie)">
                                           The script runs in every visitor's browser that views this table.
                                           ============================================================ */
                                        ?>
                                        <?php
                                        /* ── VULNERABLE version (uncomment to demo XSS) ──
                                        <td><?= $f['title'] ?></td>
                                        <td><?= $f['year'] ?></td>
                                        <td><span class="genre-badge"><?= $f['genre'] ?></span></td>
                                        <td><?= $f['rating'] ?></td>
                                        <td><span class="mood-tag mood-<?= $f['mood'] ?>"><?= $f['mood'] ?></span></td>
                                        ── END VULNERABLE ── */
                                        ?>
                                        <td><?= htmlspecialchars($f['title']) ?></td>
                                        <td><?= $f['year'] ?></td>
                                        <td><span class="genre-badge"><?= htmlspecialchars($f['genre']) ?></span></td>
                                        <td><?= $f['rating'] ?></td>
                                        <td><span class="mood-tag mood-<?= htmlspecialchars($f['mood']) ?>"><?= htmlspecialchars($f['mood']) ?></span></td>

                                        <td>
                                            <form method="post" style="display:inline;">
                                                <?= $csrfField ?>
                                                <input type="hidden" name="action" value="delete_film">
                                                <input type="hidden" name="film_id" value="<?= $f['id'] ?>">
                                                <button type="submit" class="btn-danger"
                                                        style="font-size:0.75rem;padding:0.2rem 0.6rem;"
                                                        onclick="return confirm('Remove this film?')">
                                                    Remove
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </td></tr>
        </table>
    </td></tr>
</table>

<!-- ── Recent activity ───────────────────────────────── -->
<table class="section-panel">
    <tr><td>
        <table class="section-inner">
            <tr class="section-head-row">
                <td><h2>Recent Activity</h2></td>
            </tr>
            <tr><td>
                <?php if (empty($activityLog)): ?>
                    <p style="color:var(--color-text-muted);">No activity recorded yet.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr><th>Action</th><th>Details</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activityLog as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['action']) ?></td>
                                    <td><?= htmlspecialchars($log['detail']) ?></td>
                                    <td style="color:var(--color-text-muted);font-size:0.8rem;">
                                        <?= $log['created_at'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </td></tr>
        </table>
    </td></tr>
</table>

<?php require_once __DIR__ . '/php/footer.php'; ?>
