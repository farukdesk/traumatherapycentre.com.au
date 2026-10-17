<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/mailer.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('index.php');
}

$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = trim($_POST['email'] ?? '');

    // Simple throttle to avoid abuse of the mailer.
    $_SESSION['reset_attempts'] = ($_SESSION['reset_attempts'] ?? 0) + 1;
    if ($_SESSION['reset_attempts'] > 5) {
        $error = 'Too many attempts. Please try again later.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = db()->prepare('SELECT id, username, email FROM admins WHERE email = ? AND email <> ""');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin) {
            send_password_reset($admin);
        }
        // Always show success so account emails can't be enumerated.
        $sent = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password — <?= e(setting('site_title')) ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-card">
  <h1>Forgot Password</h1>
  <p class="login-sub"><?= e(setting('site_title')) ?></p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <?php if ($sent): ?>
    <div class="alert alert-success">If that email matches an admin account, a password reset link has been sent. Please check your inbox (and spam folder).</div>
  <?php else: ?>
    <form method="post" action="forgot_password.php">
      <?= csrf_field() ?>
      <label for="email">Email address</label>
      <input type="email" id="email" name="email" required autofocus autocomplete="email">
      <button type="submit" class="btn-primary">Send reset link</button>
    </form>
  <?php endif; ?>
  <a class="back-link" href="login.php">← Back to login</a>
</div>
</body>
</html>
