<?php

namespace app\model\service;

/**
 * Class Třída představující načítač tříd
 * @package app\model
 */
class ClassLoader {

    private $loadedClasses = array();

    /**
     * ClassLoader constructor.
     */
    public function __construct () {
        echo 'Vytvarim class loader <br>';
    }

    /**
     * Načte příslušnou třídu
     *
     * @param $class string Název třídy
     */
    public function load($class) {
        echo 'Pokousim se nacist tridu: ' . $class . '<br>';
        if (file_exists($class . '.php')) {
            require $class . '.php';
        }

        /*$theClass = new $class;
        $reflector = new ReflectionClass($theClass);
        $rp = $reflector->getProperty("userManager");
        $rp->setAccessible(true);
        $rp->setValue($theClass, "UserManager");

        echo 'Injectoval jsem tridu app tridou userManger';
        $arrDocClass = explode(PHP_EOL, $reflector->getDocComment());
        $prop =  $reflector->getProperties();

        foreach($prop as $p) {
            $docComment = $p->getDocComment();
            if (strpos($docComment, '@inject') !== false) {

                $tmp = substr($docComment, strpos($docComment, '@var') + 5);
                $injectClassString = substr($tmp, 0, strpos($tmp, ' '));
                $injectClass = new $injectClassString();
                echo 'Nacetl jsem tridu: ' . $injectClassString;
            }
        }*/
    }

    /**
     * Provede injekci potřebných tříd
     *
     * @param $class mixed Třída, která má být injektována
     */
    public function inject($class) {

    }

    public function register () {
        spl_autoload_register(array($this, "load"));
    }

    public function unregister() {
        spl_autoload_unregister(array($this, "load"));
    }
}