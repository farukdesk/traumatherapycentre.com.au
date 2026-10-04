<?php
require_once __DIR__ . '/auth.php';
require_admin();

$pageTitle = 'Change Password';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = db()->prepare('SELECT password_hash FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $hash = $stmt->fetchColumn();

    if (!$hash || !password_verify($current, $hash)) {
        flash_set('error', 'Current password is incorrect.');
    } elseif (strlen($new) < 8) {
        flash_set('error', 'New password must be at least 8 characters.');
    } elseif ($new !== $confirm) {
        flash_set('error', 'New passwords do not match.');
    } else {
        $stmt = db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        flash_set('success', 'Password updated.');
    }
    redirect('password.php');
}

require __DIR__ . '/includes/admin_header.php';
?>
<h1>Change Password</h1>
<form method="post" action="password.php" class="edit-form narrow">
  <?= csrf_field() ?>
  <div class="field">
    <label for="current_password">Current password</label>
    <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
  </div>
  <div class="field">
    <label for="new_password">New password (min 8 characters)</label>
    <input type="password" id="new_password" name="new_password" required minlength="8" autocomplete="new-password">
  </div>
  <div class="field">
    <label for="confirm_password">Confirm new password</label>
    <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
  </div>
  <button type="submit" class="btn-primary">Update password</button>
</form>
<?php require __DIR__ . '/includes/admin_footer.php'; ?>
