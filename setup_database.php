<?php
/**
 * CLI database setup for Laragon/XAMPP.
 *
 * Usage:
 *   php setup_database.php --yes
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script can only be run from the command line.\n");
}

require_once __DIR__ . '/config/database.php';

$confirmed = in_array('--yes', $argv, true);

if (!$confirmed) {
    echo "This will recreate database `" . DB_NAME . "` and reset all sample data.\n";
    echo "Run again with: php setup_database.php --yes\n";
    exit(0);
}

$sqlFiles = [
    __DIR__ . '/database.sql',
    __DIR__ . '/seed.sql',
    __DIR__ . '/docker/mysql/init/03-product-images.sql',
];

function runSqlFile(PDO $pdo, string $file): void
{
    if (!is_file($file)) {
        throw new RuntimeException("SQL file not found: {$file}");
    }

    $sql = file_get_contents($file);
    if ($sql === false) {
        throw new RuntimeException("Cannot read SQL file: {$file}");
    }

    $statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql)));

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

try {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('SET NAMES ' . DB_CHARSET);

    foreach ($sqlFiles as $file) {
        echo 'Importing ' . str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $file) . "...\n";
        runSqlFile($pdo, $file);
    }

    echo "Done. Database `" . DB_NAME . "` is ready.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Database setup failed: " . $e->getMessage() . "\n");
    exit(1);
}
