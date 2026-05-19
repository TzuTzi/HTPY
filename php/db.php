<?php
/* ============================================================
   db.php  –  Database connection (SQLite only)
   get_db()       → PDO SQLite  (main data: users, films, tokens)
   get_sqlite()   → same connection alias (activity log)
   log_activity() → writes to activity_log table
   ============================================================ */

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dir = dirname(SQLITE_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    try {
        $pdo = new PDO('sqlite:' . SQLITE_PATH, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $pdo->exec('PRAGMA journal_mode=WAL');
        $pdo->exec('PRAGMA foreign_keys=ON');

        // Auto-create all tables on first run
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                username      TEXT    NOT NULL UNIQUE,
                password_hash TEXT    NOT NULL,
                email         TEXT    NOT NULL UNIQUE,
                role          TEXT    NOT NULL DEFAULT 'user' CHECK (role IN ('user','admin')),
                avatar_path   TEXT    DEFAULT NULL,
                created_at    DATETIME DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS films (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                title      TEXT    NOT NULL,
                year       INTEGER NOT NULL,
                genre      TEXT,
                rating     REAL,
                mood       TEXT,
                user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                created_at DATETIME DEFAULT (datetime('now'))
            );

            CREATE TABLE IF NOT EXISTS remember_tokens (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id    INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
                token      TEXT    NOT NULL UNIQUE,
                expires_at DATETIME NOT NULL
            );

            CREATE TABLE IF NOT EXISTS activity_log (
                id         INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id    INTEGER DEFAULT NULL,
                username   TEXT    DEFAULT NULL,
                action     TEXT    NOT NULL,
                detail     TEXT,
                ip         TEXT,
                created_at DATETIME DEFAULT (datetime('now'))
            );
        ");

        // Insert default users if they don't exist yet
        // Passwords: admin / admin123   and   demo / user123
        $pdo->exec("
            INSERT OR IGNORE INTO users (username, password_hash, email, role)
            VALUES ('admin',
                    '\$2y\$10\$LvpRTzWfCl0rx7UCgNdHYOAKGIiERpMxbuzDQmLQoTRabAaz.VzMa',
                    'admin@blimblau.ro', 'admin');

            INSERT OR IGNORE INTO users (username, password_hash, email, role)
            VALUES ('demo',
                    '\$2y\$10\$vErm950PXWkdbyEbo8fTzelrx5e0uK2s8XFBLK..jHHSkjB6f8OEW',
                    'demo@blimblau.ro', 'user');
        ");

    } catch (PDOException $e) {
        error_log('SQLite connect error: ' . $e->getMessage());
        die('Database connection error.');
    }

    return $pdo;
}

// Alias — activity log uses the same DB
function get_sqlite(): PDO {
    return get_db();
}

/* ── log_activity — writes to activity_log ─────────────────── */
function log_activity(int $userId, string $username, string $action, string $detail = ''): void {
    $db   = get_db();
    $stmt = $db->prepare(
        'INSERT INTO activity_log (user_id, username, action, detail, ip)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $username, $action, $detail, $_SERVER['REMOTE_ADDR'] ?? '']);
}
