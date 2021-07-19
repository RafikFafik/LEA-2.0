<?php

declare(strict_types=1);

use Lea\CronJobs\CronJobs;
use Lea\Response\Response;
use Lea\Router\Router;

include_once(__DIR__ . '/../vendor/autoload.php');
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
} catch(Throwable $e) {
    http_response_code(500);
    die("Błąd konfiguracji - skontaktuj się z administratorem");
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? "http://insomnia.local";
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Headers: Accept, Accept-Encoding, Accept-Language, Authorization, Content-Type, Cookie");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS, PUT");

if ($_ENV['DEBUG']) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    ini_set('display_errors', 'Off');
    error_reporting(E_ALL);
    ini_set("log_errors", 'On');
    ini_set("error_log", __DIR__ . "/../log/error.log");
    error_log( "Hello, errors!", 500,  __DIR__ . "/../log/error.log");
} else {
    ini_set('display_errors', 'Off');
}

/* Workaround */
if($_SERVER['REQUEST_METHOD'] === "OPTIONS")
    Response::noContent();

$cron_jobs = new CronJobs();
$cron_jobs->addJobs();

$router = new Router();
