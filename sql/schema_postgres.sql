-- ============================================================
--  BlimBlau  –  Schema PostgreSQL
--  Rulează o singură dată pentru a crea baza de date și tabelul.
--  Execută ca superuser sau ca utilizatorul cvir3716.
-- ============================================================

-- Creează baza de date (dacă nu există, rulează separat):
-- CREATE DATABASE blimblau_log OWNER cvir3716;

\c blimblau_log

CREATE TABLE IF NOT EXISTS activity_log (
    id         SERIAL PRIMARY KEY,
    user_id    INTEGER,
    username   VARCHAR(100),
    action     VARCHAR(100) NOT NULL,
    detail     TEXT,
    ip         VARCHAR(50),
    created_at TIMESTAMP DEFAULT NOW()
);
