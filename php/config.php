<?php
/* ============================================================
   config.php  –  Centralized configuration
   Database: SQLite (compatible with scs.ubbcluj.ro)
   ============================================================ */

// ── SQLite — single file, all tables ────────────────────────
define('SQLITE_PATH', __DIR__ . '/../db/blimblau.sqlite');

// ── Upload ──────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL',  '/HTPY/uploads/');
define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ── Session / Cookie ─────────────────────────────────────────
define('REMEMBER_DAYS', 30);
define('COOKIE_NAME',   'blimblau_remember');
