<?php
/**
 * Shared helper functions.
 */

require_once __DIR__ . '/../config/database.php';

/** Polyfill for PHP < 8.0. */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return $needle === '' || strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}

/** HTML-escape a value for safe output. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Get all site settings as key => value. */
function get_settings(): array
{
    static $settings = null;
    if ($settings === null) {
        $settings = [];
        foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $settings;
}

/** Get one setting with optional default. */
function setting(string $key, string $default = ''): string
{
    $settings = get_settings();
    return $settings[$key] ?? $default;
}

/** Fetch all active rows from a content table ordered by sort_order. */
function get_rows(string $table, bool $activeOnly = true): array
{
    $allowed = [
        'therapies', 'gold_standards', 'fee_steps', 'cancellation_tiers',
        'qualifications', 'availability', 'marquee_items', 'hero_stats',
    ];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Unknown table: ' . $table);
    }
    $sql = "SELECT * FROM `$table`" . ($activeOnly ? ' WHERE is_active = 1' : '') . ' ORDER BY sort_order, id';
    return db()->query($sql)->fetchAll();
}

/** CSRF: get (or create) the token for this session. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** CSRF: hidden input field. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** CSRF: validate a submitted token, abort on mismatch. */
function csrf_verify(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

/** Redirect and exit. */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/** Flash messages. */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render limited, safe HTML from stored content:
 * allows only a small whitelist of inline tags.
 */
function rich(?string $value): string
{
    $escaped = e($value);
    // Re-enable a small whitelist of tags saved by the admin.
    $escaped = preg_replace('/&lt;(\/?)(em|strong|b|i|br|u)\s*\/?&gt;/i', '<$1$2>', $escaped);
    return nl2br($escaped);
}

/** Same whitelist as rich() but without converting newlines. */
function rich_inline(?string $value): string
{
    $escaped = e($value);
    return preg_replace('/&lt;(\/?)(em|strong|b|i|br|u)\s*\/?&gt;/i', '<$1$2>', $escaped);
}

/**
 * Render multi-line body text: blank lines separate paragraphs,
 * lines starting with "- " become list items.
 */
function render_body(?string $text): string
{
    $html = '';
    $listItems = [];
    $flushList = function () use (&$html, &$listItems) {
        if ($listItems) {
            $html .= '<ul><li>' . implode('</li><li>', $listItems) . '</li></ul>';
            $listItems = [];
        }
    };
    foreach (preg_split('/\R\R+/', trim((string) $text)) as $block) {
        $lines = preg_split('/\R/', trim($block));
        $paragraph = [];
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '- ')) {
                $listItems[] = rich_inline(substr(trim($line), 2));
            } else {
                $flushList();
                $paragraph[] = rich_inline($line);
            }
        }
        if ($paragraph) {
            $html .= '<p>' . implode('<br>', $paragraph) . '</p>';
        }
        $flushList();
    }
    return $html;
}

/** Render newline-separated items as <li> elements (inline rich markup allowed). */
function render_list_items(?string $items): string
{
    $html = '';
    foreach (preg_split('/\R/', trim((string) $items)) as $line) {
        $line = trim($line);
        if ($line !== '') {
            $html .= '<li>' . rich_inline($line) . '</li>';
        }
    }
    return $html;
}
