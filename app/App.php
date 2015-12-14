<?php

namespace app;



use app\controller\RouterController;
use app\model\database\IDatabase;
use app\model\service\Container;
use Twig_Environment;
use Twig_Loader_Filesystem;


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
        $router = $this->container->getInstanceOf("routercontroller");
        $reqFactory = $this->container->getInstanceOf("requestfactory");

        $router->defaultAction($reqFactory->createHttpRequest());

        $router->renderView();
    }
}