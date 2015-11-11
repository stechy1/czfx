<?php

use app\App;

session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

require("lib/recaptchalib.php");
require("lib/log4php/Logger.php");

/*spl_autoload_extensions('.php');
spl_autoload_register();*/

$logger = Logger::getLogger("main");
$logger->info("Zpráva z loggeru");

$app = new App();
$app->run();