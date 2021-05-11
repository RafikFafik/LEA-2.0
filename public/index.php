<?php

use Lea\ServiceLoader;

include_once(__DIR__ . '/../src/ServiceLoader.php');
include_once(__DIR__ . '/../vendor/autoload.php');
header("Content-type: application/json");
ServiceLoader::load();
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$router = new Lea\Router\Router();
