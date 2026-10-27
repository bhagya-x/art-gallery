<?php
// සිංහල සටහන: login OTP create/send/verify කරන helper functions.
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/mailer.php';
require_once __DIR__ . '/../config/mail.php';

function mask_email(string $email): string
{
    // සිංහල සටහන: user email එක security සඳහා කොටසක් පමණක් පෙන්වයි.
    [$name, $domain] = array_pad(explode('@', $email, 2), 2, '');
    $visible = substr($name, 0, 2);
    return $visible . str_repeat('*', max(2, strlen($name) - 2)) . '@' . $domain;
}

function create_login_otp(array $user): string
{
    // සිංහල සටහන: 6 digit OTP code එකක් නිර්මාණය කර session එකේ hash එක පමණක් තබයි.
    $otp = (string) random_int(100000, 999999);
    $safeUser = $user;
    unset($safeUser['password']);

    $_SESSION['pending_login'] = $safeUser;
    $_SESSION['login_otp_hash'] = password_hash($otp, PASSWORD_DEFAULT);
    $_SESSION['login_otp_expires'] = time() + (OTP_EXPIRY_MINUTES * 60);
    $_SESSION['login_otp_attempts'] = 0;

    return $otp;
}

function send_login_otp(array $user, string $otp): bool
{
    // සිංහල සටහන: login OTP එක PHPMailer හරහා user email එකට යවයි.
    $name = $user['name'] ?? 'Customer';
    $subject = 'Your Arts Gallery Login OTP Code';
    $plain = "Hello {$name},\n\nYour Arts Gallery login OTP code is: {$otp}\n\nThis code will expire in " . OTP_EXPIRY_MINUTES . " minutes. If you did not try to login, please ignore this email.";

    $html = '
    <div style="font-family:Arial,sans-serif;background:#f6f4f1;padding:28px">
        <div style="max-width:520px;margin:auto;background:#ffffff;border-radius:22px;padding:28px;border:1px solid #e7e0d8">
            <p style="margin:0 0 8px;color:#777;font-size:13px;letter-spacing:.08em;text-transform:uppercase">Arts Gallery Security</p>
            <h2 style="margin:0 0 14px;color:#111">Login OTP Verification</h2>
            <p style="color:#555;line-height:1.6">Hello ' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ', use the code below to complete your login.</p>
            <div style="font-size:34px;font-weight:800;letter-spacing:8px;text-align:center;background:#111;color:#fff;border-radius:18px;padding:18px;margin:22px 0">' . htmlspecialchars($otp, ENT_QUOTES, 'UTF-8') . '</div>
            <p style="color:#555;line-height:1.6">This code expires in <strong>' . OTP_EXPIRY_MINUTES . ' minutes</strong>. Do not share this code with anyone.</p>
            <p style="color:#999;font-size:12px;margin-top:24px">If you did not try to login, please ignore this email.</p>
        </div>
    </div>';

    return send_app_mail($user['email'], $subject, $html, $plain);
}

function clear_login_otp(): void
{
    // සිංහල සටහන: OTP verification අවසන් වූ විට temporary session data ඉවත් කරයි.
    unset($_SESSION['pending_login'], $_SESSION['login_otp_hash'], $_SESSION['login_otp_expires'], $_SESSION['login_otp_attempts']);
}
