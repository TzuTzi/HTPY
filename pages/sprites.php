<?php
require_once __DIR__ . '/../php/auth.php';
check_remember_cookie();
if (!is_logged_in()) {
    header('Location: ../login.php');
    exit;
}
include __DIR__ . '/sprites.html';
