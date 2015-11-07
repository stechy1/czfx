<?php

use app\App;

session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);

require("app/config/config.php");
require("lib/recaptchalib.php");

spl_autoload_extensions('.php');
spl_autoload_register();

$app = new App();
$app->run();