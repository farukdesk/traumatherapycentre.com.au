<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('index.php');
}

$token = trim($_REQUEST['token'] ?? '');
$error = '';
$done = false;
$admin = null;

if ($token !== '' && preg_match('/^[a-f0-9]{64}$/', $token)) {
    $stmt = db()->prepare(
        'SELECT id, username FROM admins WHERE reset_token_hash = ? AND reset_token_expires > NOW()'
    );
    $stmt->execute([hash('sha256', $token)]);
    $admin = $stmt->fetch() ?: null;
}

if (!$admin) {
    $error = 'This password reset link is invalid or has expired. Please request a new one.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = db()->prepare(
            'UPDATE admins SET password_hash = ?, reset_token_hash = NULL, reset_token_expires = NULL WHERE id = ?'
        );
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), (int) $admin['id']]);
        unset($_SESSION['reset_attempts'], $_SESSION['login_attempts']);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password — <?= e(setting('site_title')) ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-card">
  <h1>Reset Password</h1>
  <p class="login-sub"><?= e(setting('site_title')) ?></p>
  <?php if ($done): ?>
    <div class="alert alert-success">Your password has been updated. You can now sign in with your new password.</div>
    <a class="back-link" href="login.php">← Go to login</a>
  <?php elseif (!$admin): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
    <a class="back-link" href="forgot_password.php">← Request a new link</a>
  <?php else: ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" action="reset_password.php">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">
      <label for="new_password">New password (min 8 characters)</label>
      <input type="password" id="new_password" name="new_password" required minlength="8" autofocus autocomplete="new-password">
      <label for="confirm_password">Confirm new password</label>
      <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
      <button type="submit" class="btn-primary">Set new password</button>
    </form>
    <a class="back-link" href="login.php">← Back to login</a>
  <?php endif; ?>
</div>
</body>
</html>
