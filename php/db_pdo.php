<?php
/* ============================================================
   db_pdo.php  –  Conexiune PDO (MySQL)
   Returnează un obiect PDO reutilizabil (singleton simplu).
   Folosit pentru: operații pe tabelul films (CRUD).
   ============================================================ */

require_once __DIR__ . '/config.php';

function get_pdo(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        error_log('PDO connect error: ' . $e->getMessage());
        die('Eroare conexiune bază de date.');
    }

    return $pdo;
}
