<?php
// සිංහල සටහන: PHPMailer/SMTP email settings මෙතනින් පාලනය කරයි.
// Gmail භාවිතා කරනවා නම් App Password එකක් සාදා MAIL_USERNAME සහ MAIL_PASSWORD දාන්න.

define('MAIL_FROM_EMAIL', 'no-reply@artgallery.lk');
define('MAIL_FROM_NAME', APP_NAME);

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'bhagya200312@gmail.com'); // උදා: yourgmail@gmail.com
define('MAIL_PASSWORD', 'ggbx mdsu momh jlvi'); // උදා: Gmail App Password
define('MAIL_ENCRYPTION', 'tls'); // tls හෝ ssl

define('OTP_EXPIRY_MINUTES', 5);
define('OTP_MAX_ATTEMPTS', 5);

// සිංහල සටහන: SMTP/PHPMailer setup නැති local environment එකේදී email.log එකට OTP එක ලියන fallback එක.
define('MAIL_LOG_FALLBACK', true);
