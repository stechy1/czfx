<?php


use app\model\service\ClassLoader;
use app\model\service\Container;

require "config/config.php";
require "model/service/ClassLoader.php";

$loader = new ClassLoader(str_replace("app", "", __DIR__));
$loader->register();

$container = Container::getContainer();
$container->registerFolder(__DIR__);

return $container;