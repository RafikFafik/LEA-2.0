<?php

use Lea\Core\Database\DatabaseManager;
use Lea\Router\Router;
use Lea\ServiceLoader;

include_once(__DIR__ . '/../src/ServiceLoader.php');

// include './../src/Core/Database/DatabaseManager.php';
// include './../src/Module/OfferModule/Entity/Offer.php';
include_once(__DIR__ . '/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
header("Content-type: application/json");
ServiceLoader::load();

$router = new Router();

// $database = new Lea\Core\Database\DatabaseManager();
// $database->getRecordData(new Lea\Module\OfferModule\Entity\Offer(), 1, 'fld_Number');