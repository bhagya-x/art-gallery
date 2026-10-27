<?php
// PHPMailer / SMTP email settings with support for environment variables
define('MAIL_FROM_EMAIL', getenv('MAIL_FROM_EMAIL') ?: 'no-reply@artgallery.lk');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: APP_NAME);

define('MAIL_HOST', getenv('MAIL_HOST') ?: 'smtp.gmail.com');
define('MAIL_PORT', (int)(getenv('MAIL_PORT') ?: 587));
define('MAIL_USERNAME', getenv('MAIL_USERNAME') ?: 'bhagya200312@gmail.com');
define('MAIL_PASSWORD', getenv('MAIL_PASSWORD') ?: 'ggbx mdsu momh jlvi');
define('MAIL_ENCRYPTION', getenv('MAIL_ENCRYPTION') ?: 'tls');

define('OTP_EXPIRY_MINUTES', (int)(getenv('OTP_EXPIRY_MINUTES') ?: 5));
define('OTP_MAX_ATTEMPTS', (int)(getenv('OTP_MAX_ATTEMPTS') ?: 5));

define('MAIL_LOG_FALLBACK', true);
