<?php
/* ============================================================
   db_sqlite.php  –  Conexiune PDO SQLite (baza adițională)
   Folosit pentru: jurnal de activitate (activity log).
   Fișierul SQLite se creează automat dacă nu există.
   ============================================================ */

require_once __DIR__ . '/config.php';

function get_sqlite(): PDO {
    static $sqlite = null;
    if ($sqlite !== null) return $sqlite;

    // Creează directorul db/ dacă nu există
    $dir = dirname(SQLITE_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    try {
        $sqlite = new PDO('sqlite:' . SQLITE_PATH, null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Creează tabelul de log dacă nu există
        $sqlite->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id         INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id    INTEGER,
            username   TEXT,
            action     TEXT NOT NULL,
            detail     TEXT,
            ip         TEXT,
            created_at DATETIME DEFAULT (datetime('now'))
        )");
    } catch (PDOException $e) {
        error_log('SQLite connect error: ' . $e->getMessage());
        die('Eroare conexiune SQLite.');
    }

    return $sqlite;
}
