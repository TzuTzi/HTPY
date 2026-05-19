<?php
/* ============================================================
   db_mysqli.php  –  Conexiune MySQLi
   Returnează un obiect $mysqli reutilizabil (singleton simplu).
   Folosit pentru: autentificare, utilizatori, tokeni.
   ============================================================ */

require_once __DIR__ . '/config.php';

function get_mysqli(): mysqli {
    static $conn = null;
    if ($conn !== null) return $conn;

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        // Nu expune detalii în producție
        error_log('MySQLi connect error: ' . $conn->connect_error);
        die('Eroare conexiune bază de date.');
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}
