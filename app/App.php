<?php

namespace app;


use app\controller\RouterController;
use app\model\database\Database;
use app\model\factory\RequestFactory;
use app\model\service\Container;

define("__BASEDIR__", dirname(__FILE__));

/**
 * Třída představující vstupní bod aplikace
 * @package app
 */
class App {

    /**
     * @var Container
     */
    private $container;
    /**
     * @var Database
     */
    private $database;

    /**
     * App constructor.
     */
    public function __construct () {
        $this->container = Container::getContainer();
        $this->container->mapValue('container', $this->container);
        $this->database = $this->container->getInstanceOf('database');

        $this->database->connect("127.0.0.1", "root", "", "czfx");
    }

    public function run() {
        /**
         * @var $router RouterController
         */
        $router = $this->container->getInstanceOf("routercontroller");
        $reqFactory = new RequestFactory();

        $router->defaultAction($reqFactory->createHttpRequest());
        $router->renderView();
    }
}