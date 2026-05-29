<?php

//Configuração do ambiente
define('DEV_ENVIRONMENT', true);

if(session_status() === PHP_SESSION_NONE){
    session_start();
}



if (DEV_ENVIRONMENT == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

//Configuração do Sistema
define('APP_NAME', 'LudoSkill');
define('URL_BASE', 'http://localhost:8080');

define('UPLOAD_PATH', __DIR__ . '/../../public/assets/uploads');

//Configurações do Banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_projeto_integrador');

define('DB_USER', 'root');
define('DB_PASS', '090908iza');
// define('DB_PASS', getenv('DB_PASS'));

