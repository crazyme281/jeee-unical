<?php
// Exit 0 when the configured database (mysql or pgsql) accepts a connection, 1 otherwise.
// Used by start.sh before running migrations. Reads the same env vars as Laravel.
$driver = getenv('DB_CONNECTION') ?: 'mysql';
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306');
$name = getenv('DB_DATABASE') ?: '';
$opts = [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
try {
    if ($driver === 'pgsql') {
        $ssl = getenv('DB_SSLMODE') ?: 'prefer';
        new PDO("pgsql:host=$host;port=$port;dbname=$name;sslmode=$ssl", getenv('DB_USERNAME') ?: '', getenv('DB_PASSWORD') ?: '', $opts);
    } else {
        if (getenv('MYSQL_ATTR_SSL_CA')) {
            $opts[PDO::MYSQL_ATTR_SSL_CA] = getenv('MYSQL_ATTR_SSL_CA');
        }
        new PDO("mysql:host=$host;port=$port;dbname=$name", getenv('DB_USERNAME') ?: '', getenv('DB_PASSWORD') ?: '', $opts);
    }
    exit(0);
} catch (Exception $e) {
    fwrite(STDERR, '[db-check] ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
