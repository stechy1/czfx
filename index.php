<?php

use app\App;
use app\model\service\Container;

session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

require "vendor/autoload.php";
//require "lib/recaptchalib.php";

Logger::configure("app/config/log4php.xml");
$logger = Logger::getLogger("main");
$logger->info("Hello log4php");

/** @var Container $container */
$container = require("app/bootstrap.php");
/** @var App $app */
$app = $container->getInstanceOf('app');
$app->run();