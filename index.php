<?php

use app\App;

session_start();

define("__HOME__", $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST']);
//define("UGLY_URL", true);

spl_autoload_extensions('.php');
spl_autoload_register();

$app = new App();
$app->run();