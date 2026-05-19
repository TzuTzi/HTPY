-- ============================================================
--  BlimBlau  –  MySQL Schema
--  Run: mysql -u root -p blimblau < sql/schema.sql
--  Or paste into phpMyAdmin SQL tab after creating the DB.
-- ============================================================

CREATE DATABASE IF NOT EXISTS blimblau
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE blimblau;

-- ── Table 1: users ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id            INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email         VARCHAR(100) NOT NULL UNIQUE,
    role          ENUM('user','admin') NOT NULL DEFAULT 'user',
    avatar_path   VARCHAR(255) DEFAULT NULL,
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table 2: films  (N:1 → users) ───────────────────────────
CREATE TABLE IF NOT EXISTS films (
    id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(200) NOT NULL,
    year       INT          NOT NULL CHECK (year BETWEEN 1888 AND 2099),
    genre      VARCHAR(60),
    rating     DECIMAL(3,1) CHECK (rating BETWEEN 0 AND 10),
    mood       VARCHAR(50),
    user_id    INT          NOT NULL,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table 3: remember_tokens ────────────────────────────────
CREATE TABLE IF NOT EXISTS remember_tokens (
    id         INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    token      VARCHAR(64)  NOT NULL UNIQUE,
    expires_at DATETIME     NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Table 4: activity_log ───────────────────────────────────
CREATE TABLE IF NOT EXISTS activity_log (
    id         INT           NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id    INT           DEFAULT NULL,
    username   VARCHAR(100)  DEFAULT NULL,
    action     VARCHAR(100)  NOT NULL,
    detail     TEXT,
    ip         VARCHAR(50),
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Test data ────────────────────────────────────────────────
-- Passwords: admin / admin123   and   demo / user123
-- (hashes from PHP password_hash(..., PASSWORD_BCRYPT) — do not edit cost/prefix manually)
INSERT IGNORE INTO users (username, password_hash, email, role)
VALUES ('admin',
        '$2y$10$LvpRTzWfCl0rx7UCgNdHYOAKGIiERpMxbuzDQmLQoTRabAaz.VzMa',
        'admin@blimblau.ro', 'admin');

INSERT IGNORE INTO users (username, password_hash, email, role)
VALUES ('demo',
        '$2y$10$vErm950PXWkdbyEbo8fTzelrx5e0uK2s8XFBLK..jHHSkjB6f8OEW',
        'demo@blimblau.ro', 'user');
