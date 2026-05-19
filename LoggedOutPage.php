<?php
/* ============================================================
   LoggedOutPage.php  –  Welcome / landing page
   If the user is already logged in, send them to the dashboard.
   Guests see the full welcome page (HTML content below).
   ============================================================ */

require_once __DIR__ . '/php/auth.php';
check_remember_cookie();

if (is_logged_in()) {
    header('Location: ' . logged_in_home());
    exit;
}

/* Serve the static welcome page as-is */
include __DIR__ . '/LoggedOutPage-html5.html';
