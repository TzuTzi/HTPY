<?php
/* ============================================================
   login.php  –  Login page
   - Form with username, password, CAPTCHA, Remember me
   ============================================================ */

require_once __DIR__ . '/php/auth.php';

if (is_logged_in()) {
    header('Location: ' . logged_in_home());
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = process_login($_POST);
}

if (empty($_SESSION['captcha_answer'])) {
    regenerate_captcha();
}

$pageTitle = 'Login';
require_once __DIR__ . '/php/header.php';
?>

<table class="section-panel">
    <tr>
        <td>
            <table class="section-inner" style="max-width:420px;margin:0 auto;">
                <tr class="section-head-row">
                    <td><h2>Sign In</h2></td>
                </tr>
                <tr>
                    <td>

                        <?php if ($error): ?>
                            <div style="background:#3d1a1a;border:1px solid #f85149;border-radius:6px;
                                        padding:0.6rem 1rem;margin-bottom:1rem;color:#f85149;font-size:0.875rem;">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="login.php">

                            <table class="form-table">
                                <tr>
                                    <td class="form-label"><label for="username">Username</label></td>
                                    <td class="form-field">
                                        <input type="text" id="username" name="username"
                                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                               required autocomplete="username">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="form-label"><label for="password">Password</label></td>
                                    <td class="form-field">
                                        <input type="password" id="password" name="password"
                                               required autocomplete="current-password">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="form-label">CAPTCHA</td>
                                    <td class="form-field">
                                        <img src="captcha.php?<?= time() ?>" alt="CAPTCHA code"
                                             style="border-radius:4px;display:block;margin-bottom:6px;">
                                        <small style="color:var(--color-text-muted);font-size:0.75rem;">
                                            Can't read the code?
                                            <a href="login.php" style="color:var(--color-blue);">Reload page</a>
                                        </small>
                                        <input type="text" name="captcha" placeholder="Enter the code above"
                                               required autocomplete="off" style="margin-top:6px;letter-spacing:0.1em;">
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td class="form-field">
                                        <label style="display:flex;align-items:center;gap:8px;color:var(--color-text-muted);font-size:0.875rem;cursor:pointer;">
                                            <input type="checkbox" name="remember" value="1"
                                                   <?= !empty($_POST['remember']) ? 'checked' : '' ?>>
                                            Remember me for <?= REMEMBER_DAYS ?> days
                                        </label>
                                    </td>
                                </tr>
                            </table>

                            <div class="form-actions" style="margin-top:1.2rem;">
                                <button type="submit" class="btn-primary">Sign In</button>
                            </div>

                        </form>

                        <p style="margin-top:1rem;color:var(--color-text-muted);font-size:0.875rem;">
                            Don't have an account?
                            <a href="register.php" style="color:var(--color-blue);">Register</a>
                        </p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php require_once __DIR__ . '/php/footer.php'; ?>
