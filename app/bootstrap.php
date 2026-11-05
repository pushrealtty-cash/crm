<?php
declare(strict_types=1);
$configFile = __DIR__ . '/config.php';
if (!file_exists($configFile)) { http_response_code(500); exit('Missing app/config.php. Follow the installation guide.'); }
$config = require $configFile;
date_default_timezone_set($config['timezone'] ?? 'Asia/Kolkata');
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_name('estatecrm_session');
session_set_cookie_params(['httponly'=>true,'secure'=>$isHttps,'samesite'=>'Lax']);
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
try {
  $dsn = 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'].';charset='.($config['db']['charset'] ?? 'utf8mb4');
  $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
} catch (Throwable $e) { http_response_code(500); exit('Database connection failed. Check app/config.php.'); }
require_once __DIR__.'/helpers.php';
