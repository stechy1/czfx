<?php


use app\model\service\Container;

require "config/config.php";

/**
 * $container Container
 */
$container = Container::getContainer();
$container->registerFolder(__DIR__);

return $container;