<?php

namespace app\model\service;


require("FileFilterIterator.php");

/**
 * Class Třída představující načítač tříd
 * @package app\model
 */
class ClassLoader {

    private $rootFolder;

    /**
     * ClassLoader constructor
     *
     * @param $rootFolder string Kořenová složka aplikace
     */
    public function __construct ($rootFolder) {
        $this->rootFolder = $rootFolder;
    }

    /**
     * Načte příslušnou třídu
     *
     * @param $class string Název třídy
     */
    public function load($class) {
        if (file_exists($this->rootFolder . $class . '.php')) {
            require $this->rootFolder . $class . '.php';
        }
    }

    /**
     * Zaregistruje class loader
     */
    public function register () {
        spl_autoload_register(array($this, "load"));
    }

    /**
     * Odhlásí class loader
     */
    public function unregister() {
        spl_autoload_unregister(array($this, "load"));
    }
}