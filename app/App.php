<?php

namespace app;


use app\controller\RouterController;
use app\model\database\Database;
use app\model\factory\RequestFactory;
use app\model\service\Container;


/**
 * Třída představující vstupní bod aplikace
 * @Inject Container
 * @package app
 */
class App {

    /**
     * @var Container
     */
    private $container;

    public function run() {
        $database = $this->container->getInstanceOf('database');
        $database->connect(DATABASE_HOST, DATABASE_LOGIN, DATABASE_PASS, DATABASE_SCHEME);

        /**
         * @var $router RouterController
         */
        $router = $this->container->getInstanceOf("routercontroller");
        $reqFactory = new RequestFactory();

        $router->defaultAction($reqFactory->createHttpRequest());
        $router->renderView();
    }
}