<?php
$t = microtime(true);
try {
    $pdo = new PDO("mysql:host=".getenv('DB_HOST').";port=".getenv('DB_PORT').";dbname=".getenv('DB_NAME').";charset=utf8mb4", getenv('DB_USER'), getenv('DB_PASS'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 5]);
    echo 'connect_ms=' . round((microtime(true)-$t)*1000) . '<br>';
    echo 'users=' . $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() . '<br>';
    echo 'total_ms=' . round((microtime(true)-$t)*1000) . '<br>';
} catch (Exception $e) {
    echo 'ERR after_ms=' . round((microtime(true)-$t)*1000) . ' ' . htmlspecialchars($e->getMessage()) . '<br>';
}
echo 'host=' . gethostname();