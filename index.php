<?php

use app\App;
use app\model\service\Container;

session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

$loader = require "vendor/autoload.php";

//require "app/global_functions.php";

Logger::configure("app/config/log4php.xml");

/** @var Container $container */
$container = require("app/bootstrap.php");
/** @var App $app */
$app = $container->getInstanceOf('app');
$app->run();
