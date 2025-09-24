<?php

function loadEnv(string $path): void {
    if (!is_file($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;              
        [$k, $v] = array_map('trim', explode('=', $line, 2) + ['', '']);
        $v = trim($v, " \t\n\r\0\x0B\"'");                       
        $_ENV[$k] = $v; $_SERVER[$k] = $v; putenv("$k=$v");
    }
}

loadEnv(__DIR__ . '/../../../.env'); 

$host    = $_ENV['DB_HOST']    ?? '127.0.0.1';
$db      = $_ENV['DB_NAME']    ?? 'babybloom';
$user    = $_ENV['DB_USER']    ?? 'root';
$pass    = $_ENV['DB_PASS']    ?? '';
$charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

$con = mysqli_connect($host, $user, $pass, $db);
if (!$con) {
    http_response_code(500);
    exit('Database connection failed.');
}
mysqli_set_charset($con, $charset);

return $con;
