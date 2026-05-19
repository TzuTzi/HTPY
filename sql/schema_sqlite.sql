-- ============================================================
--  BlimBlau  –  SQLite Schema
--  Compatible with: SQLite 3.x (Linux shared hosting, scs.ubbcluj.ro)
--  This file is for reference only — tables are auto-created by db.php
-- ============================================================

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
    year       INTEGER NOT NULL CHECK (year BETWEEN 1888 AND 2099),
    genre      TEXT,
    rating     REAL    CHECK (rating BETWEEN 0 AND 10),
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

-- ── Test users ────────────────────────────────────────────────
-- Passwords: admin / admin123   and   demo / user123
INSERT OR IGNORE INTO users (username, password_hash, email, role)
VALUES ('admin',
        '$2y$10$LvpRTzWfCl0rx7UCgNdHYOAKGIiERpMxbuzDQmLQoTRabAaz.VzMa',
        'admin@blimblau.ro', 'admin');

INSERT OR IGNORE INTO users (username, password_hash, email, role)
VALUES ('demo',
        '$2y$10$vErm950PXWkdbyEbo8fTzelrx5e0uK2s8XFBLK..jHHSkjB6f8OEW',
        'demo@blimblau.ro', 'user');
