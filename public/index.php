<?php

declare(strict_types=1);

use Lea\Router\Router;
use Lea\ServiceLoader;

include_once(__DIR__ . '/../src/ServiceLoader.php');
include_once(__DIR__ . '/../vendor/autoload.php');
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch(Throwable $e) {
    http_response_code(500);
    die("Błąd konfiguracji - skontaktuj się z administratorem");
}
ServiceLoader::load();
if ($_ENV['DEBUG']) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}

$router = new Router();
