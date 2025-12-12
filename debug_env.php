<?php
require_once __DIR__.'/vendor/autoload.php';

// Load the environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Output the DB_CONNECTION value
echo "DB_CONNECTION from environment: " . getenv('DB_CONNECTION') . "\n";

// Also try to read from $_ENV
echo "DB_CONNECTION from \$_ENV: " . ($_ENV['DB_CONNECTION'] ?? 'NOT SET') . "\n";

// Also try to read from $_SERVER
echo "DB_CONNECTION from \$_SERVER: " . ($_SERVER['DB_CONNECTION'] ?? 'NOT SET') . "\n";