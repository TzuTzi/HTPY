<?php
/* ============================================================
   register.php  –  New account registration
   ============================================================ */

require_once __DIR__ . '/php/auth.php';

if (is_logged_in()) {
    header('Location: ' . logged_in_home());
    exit;
}

$error   = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = process_register($_POST);
}

$pageTitle = 'Register';
require_once __DIR__ . '/php/header.php';
?>

<table class="section-panel">
    <tr>
        <td>
            <table class="section-inner" style="max-width:480px;margin:0 auto;">
                <tr class="section-head-row">
                    <td><h2>Create Account</h2></td>
                </tr>
                <tr>
                    <td>

                        <?php if ($error): ?>
                            <div style="background:#3d1a1a;border:1px solid #f85149;border-radius:6px;
                                        padding:0.6rem 1rem;margin-bottom:1rem;color:#f85149;font-size:0.875rem;">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="register.php">

                            <table class="form-table">
                                <tr>
                                    <td class="form-label"><label for="reg-username">Username *</label></td>
                                    <td class="form-field">
                                        <input type="text" id="reg-username" name="username"
                                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                               required minlength="3" maxlength="50">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="form-label"><label for="reg-email">Email *</label></td>
                                    <td class="form-field">
                                        <input type="email" id="reg-email" name="email"
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                               required>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="form-label"><label for="reg-pass">Password *</label></td>
                                    <td class="form-field">
                                        <input type="password" id="reg-pass" name="password"
                                               required minlength="8" autocomplete="new-password">
                                        <small style="color:var(--color-text-muted);">Minimum 8 characters</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="form-label"><label for="reg-confirm">Confirm password *</label></td>
                                    <td class="form-field">
                                        <input type="password" id="reg-confirm" name="password_confirm"
                                               required autocomplete="new-password">
                                    </td>
                                </tr>
                            </table>

                            <div class="form-actions" style="margin-top:1.2rem;">
                                <button type="submit" class="btn-primary">Create Account</button>
                            </div>

                        </form>

                        <p style="margin-top:1rem;color:var(--color-text-muted);font-size:0.875rem;">
                            Already have an account?
                            <a href="login.php" style="color:var(--color-blue);">Sign in</a>
                        </p>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<?php require_once __DIR__ . '/php/footer.php'; ?>
