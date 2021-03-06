<?php

namespace app;


use app\controller\RouterController;
use app\model\database\IDatabase;
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
        /**
         * @var IDatabase $database
         */
        $database = $this->container->getInstanceOf('database');
        try {
            $database->connect(DATABASE_HOST, DATABASE_LOGIN, DATABASE_PASS, DATABASE_SCHEME);
        } catch (\PDOException $ex) {
            echo "Nepodarilo se pripojit k databazi. Ukoncuji relaci.";
            exit(1);
        }

        /**
         * @var $router RouterController
         */
        $router = $this->container->getInstanceOf("RouterController");
        $reqFactory = $this->container->getInstanceOf("RequestFactory");

        $router->defaultAction($reqFactory->createHttpRequest());
        $router->renderView();
    }
}