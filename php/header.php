<?php
/* Errors visible in browser – only for HTML pages, not for image-generating
   scripts like captcha.php */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ============================================================
   header.php  –  Shared header for all PHP pages
   Usage: require_once 'php/header.php';
   Set $pageTitle before including.
   ============================================================ */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';
check_remember_cookie();

$loggedIn   = is_logged_in();
$isAdmin    = is_admin();
$navUser    = $_SESSION['username'] ?? '';
$navAvatar  = null;
$onAdminPage = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'admin.php';

if ($loggedIn) {
    $row = get_db()->prepare('SELECT avatar_path FROM users WHERE id = ?');
    $row->execute([$_SESSION['user_id']]);
    $navAvatar = $row->fetchColumn() ?: null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? 'BlimBlau') ?> – BlimBlau</title>
    <link rel="stylesheet" href="/HTPY/css/style-horizontal.css">
    <?php if (!empty($extraCss)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($extraCss) ?>">
    <?php endif; ?>
</head>
<body>

<table id="site-header" width="100%">
    <tr>
        <td>
            <table>
                <tr>
                    <td>
                        <table>
                            <tr class="nav-row">
                                <td class="nav-logo-td"><a href="index.php"><b>BlimBlau</b></a></td>
                                <td></td>
                                <td><a href="pages/featured-html5.html">Films</a></td>
                                <td></td>
                                <?php if ($isAdmin): ?>
                                <td><a href="admin.php"
                                       class="nav-admin-panel<?= $onAdminPage ? ' is-current' : '' ?>">Admin panel</a></td>
                                <td></td>
                                <?php endif; ?>
                                <td><a href="widgets.php">Dashboard</a></td>
                                <td></td>
                                <?php if ($loggedIn): ?>
                                <td><a href="pages/mood-picker.php">Mood</a></td>
                                <td></td>
                                <?php endif; ?>
                                <td><a href="pages/recommendations-html5.html">Browse</a></td>
                                <?php if ($loggedIn): ?>
                                <td></td>
                                <td>
                                    <div class="nav-dropdown-wrap">
                                        <span class="nav-dropdown-trigger" tabindex="0" aria-haspopup="true" aria-expanded="false">
                                            Extras &#9660;
                                        </span>
                                        <div class="nav-dropdown-panel">
                                            <a href="pages/sprites.php">CSS Sprites</a>
                                            <a href="pages/nested-list.php">Nested Lists</a>
                                            <a href="pages/form-html5.php">Form</a>
                                        </div>
                                    </div>
                                </td>
                                <?php endif; ?>
                                <td></td>
                                <td>
                                    <form action="search.html" method="get" class="nav-search-form">
                                        <input type="text" name="query" placeholder="Search movies...">
                                        <button type="submit" aria-label="Search" class="nav-search-btn">&#128269;</button>
                                    </form>
                                </td>
                                <td></td>
                                <?php if ($loggedIn): ?>
                                    <td>
                                        <div class="nav-dropdown-wrap">
                                            <span class="nav-dropdown-trigger" tabindex="0" aria-haspopup="true" aria-expanded="false" style="display:flex;align-items:center;gap:7px;">
                                                <?php if ($navAvatar): ?>
                                                    <img src="/HTPY/uploads/<?= htmlspecialchars($navAvatar) ?>"
                                                         alt="avatar"
                                                         style="width:26px;height:26px;border-radius:50%;
                                                                object-fit:cover;border:1px solid var(--color-border);
                                                                flex-shrink:0;">
                                                <?php else: ?>
                                                    <span style="font-size:1.1rem;flex-shrink:0;line-height:1;">&#128100;</span>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($navUser) ?> &#9660;
                                            </span>
                                            <div class="nav-dropdown-panel">
                                                <a href="profile.php">My Profile</a>
                                                <?php if ($isAdmin): ?>
                                                    <a href="admin.php">Admin</a>
                                                <?php endif; ?>
                                                <a href="logout.php">Sign Out</a>
                                            </div>
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td><a href="login.php">Login</a></td>
                                    <td></td>
                                    <td><a href="register.php">Register</a></td>
                                <?php endif; ?>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table id="main-content">
    <tr>
        <td>
            <table class="content-stack">
                <tr>
                    <td>
