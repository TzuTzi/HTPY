<?php
/* ============================================================
   db_postgres.php  –  Conexiune PDO PostgreSQL
   Baza de date adițională – stochează jurnalul de activitate.
   Editează constantele de mai jos cu datele tale PgSQL.
   ============================================================ */

require_once __DIR__ . '/config.php';

// ── Configurare PostgreSQL ───────────────────────────────────
define('PG_HOST', 'localhost');
define('PG_PORT', '5432');
define('PG_USER', 'postgres');
define('PG_PASS', 'postgres');
define('PG_NAME', 'blimblau_log');

function get_postgres(): PDO {
    static $pg = null;
    if ($pg !== null) return $pg;

    $dsn = sprintf(
        'pgsql:host=%s;port=%s;dbname=%s',
        PG_HOST, PG_PORT, PG_NAME
    );

    try {
        $pg = new PDO($dsn, PG_USER, PG_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        // Creează tabelul dacă nu există (prima rulare)
        $pg->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id         SERIAL PRIMARY KEY,
            user_id    INTEGER,
            username   VARCHAR(100),
            action     VARCHAR(100) NOT NULL,
            detail     TEXT,
            ip         VARCHAR(50),
            created_at TIMESTAMP DEFAULT NOW()
        )");

    } catch (PDOException $e) {
        error_log('PostgreSQL connect error: ' . $e->getMessage());
        die('Eroare conexiune PostgreSQL.');
    }

    return $pg;
}

/* ── Scrie o intrare în jurnal ── */
function log_activity(int $userId, string $username, string $action, string $detail = ''): void {
    $db   = get_postgres();
    $stmt = $db->prepare(
        'INSERT INTO activity_log (user_id, username, action, detail, ip)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $username, $action, $detail, $_SERVER['REMOTE_ADDR'] ?? '']);
}
