<?php
// සිංහල සටහන: PHPMailer භාවිතා කර email යවන central helper file එක.
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/mail.php';

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

function write_mail_log(string $to, string $subject, string $body, string $status = 'LOGGED'): void
{
    // local testing fallback log
    $logDir = __DIR__ . '/../storage/logs/';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0775, true);
    }
    if (!is_writable($logDir)) {
        $logDir = sys_get_temp_dir() . '/';
    }

    $plainBody = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body)));
    $line = "[" . date('Y-m-d H:i:s') . "] {$status}\nTO: {$to}\nSUBJECT: {$subject}\n{$plainBody}\n\n";
    @file_put_contents($logDir . 'email.log', $line, FILE_APPEND);
}

function send_app_mail(string $to, string $subject, string $htmlBody, ?string $plainBody = null): bool
{
    // සිංහල සටහන: PHPMailer install කරලා SMTP details දාලා තියෙනවා නම් real email එක යවයි.
    $phpMailerAvailable = class_exists('PHPMailer\\PHPMailer\\PHPMailer');
    $smtpReady = trim((string)MAIL_USERNAME) !== '' && trim((string)MAIL_PASSWORD) !== '';

    if ($phpMailerAvailable && $smtpReady) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = MAIL_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = MAIL_USERNAME;
            $mail->Password = MAIL_PASSWORD;
            $mail->Port = MAIL_PORT;

            if (MAIL_ENCRYPTION === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = $plainBody ?: trim(strip_tags($htmlBody));
            $mail->send();

            write_mail_log($to, $subject, $plainBody ?: $htmlBody, 'SENT WITH PHPMAILER');
            return true;
        } catch (Throwable $exception) {
            write_mail_log($to, $subject, 'PHPMailer error: ' . $exception->getMessage() . "\n\n" . ($plainBody ?: $htmlBody), 'FAILED');
            return false;
        }
    }

    // සිංහල සටහන: Composer install හෝ SMTP settings නැතිනම් local log fallback එක භාවිතා කරයි.
    if (MAIL_LOG_FALLBACK) {
        write_mail_log($to, $subject, $plainBody ?: $htmlBody, 'LOCAL LOG FALLBACK');
        return true;
    }

    return false;
}
