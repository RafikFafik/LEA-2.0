<?php

declare(strict_types=1);

use Lea\Router\Router;
use Lea\ServiceLoader;


include_once(__DIR__ . '/../src/ServiceLoader.php');
// include './../src/Core/Database/DatabaseManager.php';
// include './../src/Module/OfferModule/Entity/Offer.php';
include_once(__DIR__ . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
ServiceLoader::load();
if ($_ENV['DEBUG']) {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    ini_set('display_errors', 'On');
}

$router = new Router();
