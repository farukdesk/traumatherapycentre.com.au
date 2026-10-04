<?php
require_once __DIR__ . '/auth.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Simple brute-force throttle
    $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
    if ($_SESSION['login_attempts'] > 10) {
        $error = 'Too many attempts. Please try again later.';
    } else {
        $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            unset($_SESSION['login_attempts']);
            redirect('index.php');
        }
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?= e(setting('site_title')) ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-page">
<div class="login-card">
  <h1>Admin Login</h1>
  <p class="login-sub"><?= e(setting('site_title')) ?></p>
  <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="login.php">
    <?= csrf_field() ?>
    <label for="username">Username</label>
    <input type="text" id="username" name="username" required autofocus autocomplete="username">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required autocomplete="current-password">
    <button type="submit" class="btn-primary">Sign in</button>
  </form>
  <a class="back-link" href="../index.php">← Back to website</a>
</div>
</body>
</html>
