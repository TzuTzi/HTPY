<?php
/* ============================================================
   auth.php  –  Authentication, session, roles
   Uses SQLite via db.php (PDO).
   ============================================================ */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ── Check "Remember me" cookie ── */
function check_remember_cookie(): void {
    if (isset($_SESSION['user_id'])) return;
    if (!isset($_COOKIE[COOKIE_NAME])) return;

    $token = $_COOKIE[COOKIE_NAME];
    $stmt  = get_db()->prepare(
        'SELECT rt.user_id, u.username, u.role
         FROM remember_tokens rt
         JOIN users u ON u.id = rt.user_id
         WHERE rt.token = ? AND rt.expires_at > datetime("now")
         LIMIT 1'
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch();

    if ($row) {
        $_SESSION['user_id']  = $row['user_id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];
    } else {
        setcookie(COOKIE_NAME, '', time() - 3600, '/');
    }
}

function is_logged_in(): bool {
    check_remember_cookie();
    return isset($_SESSION['user_id']);
}

function is_admin(): bool {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

function require_login(string $redirect = 'login.php'): void {
    if (!is_logged_in()) { header('Location: ' . $redirect); exit; }
}

function require_admin(string $redirect = 'login.php'): void {
    if (!is_admin()) { header('Location: ' . $redirect); exit; }
}

function logged_in_home(): string {
    return is_admin() ? 'admin.php' : 'widgets.php';
}

/* ── Process login ── */
function process_login(array $post): ?string {
    $username  = trim($post['username'] ?? '');
    $password  = $post['password'] ?? '';
    $remember  = !empty($post['remember']);
    $captchaIn = trim($post['captcha'] ?? '');

    if (strtolower($captchaIn) !== strtolower($_SESSION['captcha_answer'] ?? '')) {
        regenerate_captcha();
        return 'Incorrect CAPTCHA code.';
    }
    regenerate_captcha();

    if ($username === '' || $password === '') return 'Please fill in all fields.';

    /* ============================================================
       VULNERABILITY 1 — SQL INJECTION (intentionally vulnerable)
       Exploit: username =  ' OR '1'='1' --   password = anything
                SQL becomes:
                SELECT ... WHERE username = '' OR '1'='1' -- LIMIT 1
                '1'='1' is always true → first user row returned → login bypass
       ── SECURE version: uncomment the prepared-statement block below to patch ──
       ============================================================ */
    $db = get_db();

    /* ── VULNERABLE version (uncomment to demo SQL injection) ──
    try {
        $stmt = $db->query(
            "SELECT id, role FROM users WHERE username = '$username' LIMIT 1"
        );
        $row = $stmt ? $stmt->fetch() : null;
    } catch (PDOException $e) {
        return 'SQL Error: ' . $e->getMessage();
    }
    if (!$row) {
        return 'Incorrect username or password.';
    }
    ── END VULNERABLE ── */

    // SECURE — prepared statement + password_verify
    $stmt = $db->prepare('SELECT id, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        return 'Incorrect username or password.';
    }

    session_regenerate_id(true);
    $_SESSION['user_id']  = $row['id'];
    $_SESSION['username'] = $username;
    $_SESSION['role']     = $row['role'];

    if ($remember) {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + REMEMBER_DAYS * 86400);
        $stmt2 = $db->prepare(
            'INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)'
        );
        $stmt2->execute([$row['id'], $token, $expires]);
        setcookie(COOKIE_NAME, $token, time() + REMEMBER_DAYS * 86400, '/', '', false, true);
    }

    log_activity($row['id'], $username, 'login', 'success');
    header('Location: ' . logged_in_home());
    exit;
}

/* ── Logout ── */
function do_logout(): void {
    check_remember_cookie();
    $userId   = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? '';

    if (isset($_COOKIE[COOKIE_NAME])) {
        $token = $_COOKIE[COOKIE_NAME];
        $stmt  = get_db()->prepare('DELETE FROM remember_tokens WHERE token = ?');
        $stmt->execute([$token]);
        setcookie(COOKIE_NAME, '', time() - 3600, '/');
    }

    if ($userId) log_activity($userId, $username, 'logout', '');
    session_destroy();
    header('Location: index.php');
    exit;
}

/* ── Register ── */
function process_register(array $post): ?string {
    $username = trim($post['username'] ?? '');
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';
    $confirm  = $post['password_confirm'] ?? '';

    if ($username === '' || $email === '' || $password === '') return 'Please fill in all required fields.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Invalid email address.';
    if (strlen($password) < 8) return 'Password must be at least 8 characters.';
    if ($password !== $confirm) return 'Passwords do not match.';

    $hash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $db   = get_db();
        $stmt = $db->prepare(
            'INSERT INTO users (username, password_hash, email) VALUES (?, ?, ?)'
        );
        $stmt->execute([$username, $hash, $email]);
        $newId = (int) $db->lastInsertId();
    } catch (PDOException $e) {
        return 'Username or email already taken.';
    }

    log_activity($newId, $username, 'register', '');
    session_regenerate_id(true);
    $_SESSION['user_id']  = $newId;
    $_SESSION['username'] = $username;
    $_SESSION['role']     = 'user';

    header('Location: widgets.php');
    exit;
}

/* ── Generate CAPTCHA ── */
function regenerate_captcha(): void {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code  = '';
    for ($i = 0; $i < 5; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    $_SESSION['captcha_answer'] = $code;
}
