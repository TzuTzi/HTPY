<?php
/* ============================================================
   index.php  –  Punct de intrare principal
   - Utilizator autentificat → widgets.php  (dashboard)
   - Vizitator           → LoggedOutPage.php (welcome page)
   ============================================================ */

require_once __DIR__ . '/php/auth.php';
check_remember_cookie();

if (is_logged_in()) {
    header('Location: ' . logged_in_home());
} else {
    header('Location: LoggedOutPage.php');
}
exit;
