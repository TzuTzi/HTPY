<?php
/* ============================================================
   logout.php  –  Deconectare
   Distruge sesiunea, șterge cookie-ul și redirecționează.
   ============================================================ */

require_once __DIR__ . '/php/auth.php';
do_logout();   // face redirect intern la login.php
