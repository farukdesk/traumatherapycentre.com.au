<?php
/**
 * Email helpers — password reset email with a branded HTML template.
 */

require_once __DIR__ . '/functions.php';

/** Best-effort "From" address for outgoing site mail. */
function mail_from_address(): string
{
    $host = strtolower($_SERVER['HTTP_HOST'] ?? 'traumatherapycentre.com.au');
    $host = preg_replace('/^(www|new)\./', '', preg_replace('/:\d+$/', '', $host));
    if ($host === '' || !preg_match('/^[a-z0-9.-]+$/', $host)) {
        $host = 'traumatherapycentre.com.au';
    }
    return 'noreply@' . $host;
}

/** Send an HTML email. Returns true on success. */
function send_html_email(string $to, string $subject, string $html): bool
{
    $from = mail_from_address();
    $siteTitle = setting('site_title', 'Trauma Therapy Centre');
    $encodedFrom = '=?UTF-8?B?' . base64_encode($siteTitle) . '?=';

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From: ' . $encodedFrom . ' <' . $from . '>' . "\r\n";
    $headers .= 'Reply-To: ' . $from . "\r\n";
    $headers .= 'X-Mailer: PHP/' . phpversion();

    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, $headers);
}

/** Branded HTML template for the password reset email. */
function password_reset_email_html(string $username, string $resetUrl, int $validMinutes): string
{
    $siteTitle = e(setting('site_title', 'Trauma Therapy Centre'));
    $logoUrl   = e(setting('logo_url'));
    $username  = e($username);
    $resetUrl  = e($resetUrl);
    $year      = date('Y');

    $logoHtml = $logoUrl !== ''
        ? '<img src="' . $logoUrl . '" alt="' . $siteTitle . '" width="56" style="display:block;margin:0 auto 12px;border:0;max-height:56px;">'
        : '';

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Password Reset — {$siteTitle}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f1ec;font-family:Georgia,'Times New Roman',serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1ec;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background-color:#fffdf8;border:1px solid #e3ddd2;border-radius:16px;overflow:hidden;">
          <tr>
            <td style="background-color:#3f4a3c;padding:28px 36px;text-align:center;">
              {$logoHtml}
              <h1 style="margin:0;font-size:20px;font-weight:normal;color:#fffdf8;letter-spacing:.5px;">{$siteTitle}</h1>
            </td>
          </tr>
          <tr>
            <td style="padding:36px;">
              <h2 style="margin:0 0 16px;font-size:18px;color:#3f4a3c;">Password reset request</h2>
              <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#4a4540;">Hi <strong>{$username}</strong>,</p>
              <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#4a4540;">
                We received a request to reset the password for your admin account.
                Click the button below to choose a new password. This link is valid for
                <strong>{$validMinutes} minutes</strong> and can only be used once.
              </p>
              <table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px auto;">
                <tr>
                  <td style="background-color:#3f4a3c;border-radius:10px;">
                    <a href="{$resetUrl}" style="display:inline-block;padding:14px 32px;font-size:15px;color:#fffdf8;text-decoration:none;font-family:Georgia,serif;">Reset my password</a>
                  </td>
                </tr>
              </table>
              <p style="margin:0 0 8px;font-size:13px;line-height:1.6;color:#8a8278;">
                If the button doesn't work, copy and paste this link into your browser:
              </p>
              <p style="margin:0 0 20px;font-size:13px;line-height:1.6;word-break:break-all;">
                <a href="{$resetUrl}" style="color:#5b4a63;">{$resetUrl}</a>
              </p>
              <p style="margin:0;font-size:13px;line-height:1.6;color:#8a8278;">
                If you didn't request this, you can safely ignore this email —
                your password will stay the same.
              </p>
            </td>
          </tr>
          <tr>
            <td style="padding:20px 36px;background-color:#f4f1ec;border-top:1px solid #e3ddd2;text-align:center;">
              <p style="margin:0;font-size:12px;color:#8a8278;">&copy; {$year} {$siteTitle}. This is an automated message — please do not reply.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;
}

/**
 * Generate a reset token for an admin, store its hash and email the link.
 * Returns true if the email was handed to the mailer.
 */
function send_password_reset(array $admin): bool
{
    $validMinutes = 60;
    $token = bin2hex(random_bytes(32));

    $stmt = db()->prepare(
        'UPDATE admins SET reset_token_hash = ?, reset_token_expires = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?'
    );
    $stmt->execute([hash('sha256', $token), $validMinutes, (int) $admin['id']]);

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'traumatherapycentre.com.au';
    $path = rtrim(dirname($_SERVER['PHP_SELF'] ?? '/admin/forgot_password.php'), '/\\');
    $resetUrl = $scheme . '://' . $host . $path . '/reset_password.php?token=' . $token;

    $siteTitle = setting('site_title', 'Trauma Therapy Centre');
    $html = password_reset_email_html($admin['username'], $resetUrl, $validMinutes);

    return send_html_email($admin['email'], 'Reset your password — ' . $siteTitle, $html);
}
