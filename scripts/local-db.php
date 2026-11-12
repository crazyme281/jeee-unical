<?php

require dirname(__DIR__).'/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();

if (($_ENV['DB_CONNECTION'] ?? 'mysql') !== 'mysql') {
    throw new RuntimeException('Local database setup only creates a XAMPP database.');
}

$name = $_ENV['DB_DATABASE'];
$user = $_ENV['DB_USERNAME'];
$host = $_ENV['DB_HOST'];
if (!in_array($host, ['localhost', '127.0.0.1'], true) || ($_ENV['APP_ENV'] ?? '') !== 'local') {
    throw new RuntimeException('This script only sets up a database on this computer.');
}
if (!preg_match('/^[a-zA-Z0-9_]+$/', $name) || !preg_match('/^[a-zA-Z0-9_]+$/', $user) || $user === 'root' || in_array(strtolower($name), ['mysql', 'sys', 'information_schema', 'performance_schema'], true)) {
    throw new RuntimeException('Use a separate database and user with letters, numbers, or underscores.');
}

$pdo = new PDO(
    'mysql:host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'],
    getenv('LOCAL_DB_ADMIN_USER') ?: 'root',
    getenv('LOCAL_DB_ADMIN_PASSWORD') ?: '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$pdo->exec('CREATE DATABASE IF NOT EXISTS `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
foreach (['localhost', '127.0.0.1'] as $host) {
    $account = $pdo->quote($user).'@'.$pdo->quote($host);
    $pdo->exec('CREATE USER IF NOT EXISTS '.$account.' IDENTIFIED BY '.$pdo->quote($_ENV['DB_PASSWORD']));
    $pdo->exec('GRANT ALL PRIVILEGES ON `'.$name.'`.* TO '.$account);
}
try {
    new PDO(
        'mysql:host='.$_ENV['DB_HOST'].';port='.$_ENV['DB_PORT'].';dbname='.$name,
        $user,
        $_ENV['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    throw new RuntimeException('The local database user already exists with a different password. Use its password in .env or choose a new DB_USERNAME. No existing password was changed.', 0, $e);
}
echo "Local database is ready.\n";
