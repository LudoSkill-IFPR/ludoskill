<?php

//Configuração do ambiente
define('DEV_ENVIRONMENT', getenv('APP_ENV') === 'development');

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

if (DEV_ENVIRONMENT == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
}

//Configuração do Sistema
define('APP_NAME', 'LudoSkill');
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$scriptDir = preg_replace('#/public$#', '', $scriptDir);
$scriptDir = in_array($scriptDir, ['.', '/'], true) ? '' : $scriptDir;
define('URL_BASE', $scriptDir === '/' ? '' : rtrim($scriptDir, '/'));

$urlConfigurada = getenv('APP_URL');
if ($urlConfigurada !== false && trim($urlConfigurada) !== '') {
    define('APP_URL', rtrim($urlConfigurada, '/'));
} else {
    $httpsAtivo = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $protocolo = $httpsAtivo ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('APP_URL', $protocolo . '://' . $host . URL_BASE);
}

define('UPLOAD_PATH', __DIR__ . '/../../public/assets/uploads');

//Configurações do Banco de dados
define('DB_HOST', getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost');
define('DB_NAME', getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'db_ludoskill');
define('DB_USER', getenv('DB_USER') !== false ? getenv('DB_USER') : 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '090908iza');
