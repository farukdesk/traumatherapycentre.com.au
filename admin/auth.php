<?php
/**
 * Admin auth guard — include at the top of every protected admin page.
 */

require_once __DIR__ . '/../includes/functions.php';

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    $stmt = db()->prepare('SELECT id, username FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}
