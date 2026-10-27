<?php
// Load local .env file if present
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (getenv($key) === false) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }
}

// MySQL database connection via PDO with environment variable support for cloud hosting (e.g. Vercel)
$host = getenv('DB_HOST') ?: '';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: '';
$username = getenv('DB_USER') ?: (getenv('DB_USERNAME') ?: '');
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
$charset = 'utf8mb4';

// For local development fallback if no env vars are defined
if (empty($host) && !getenv('VERCEL')) {
    $host = '127.0.0.1';
    $dbname = 'art_gallery_premium';
    $username = 'root';
    $password = '';
}

$pdo = null;
$dbError = null;

if (!empty($host) && !empty($dbname)) {
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        // Cloud MySQL SSL support (e.g. Aiven, TiDB, PlanetScale)
        if (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1' || str_contains($host, 'aivencloud') || str_contains($host, 'tidbcloud')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }

        $pdo = new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        $dbError = $e->getMessage();
    }
} else {
    $dbError = 'Database environment variables are not configured in Vercel.';
}

function db_connected(): bool
{
    global $pdo;
    return $pdo !== null;
}
