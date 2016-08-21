<?php

namespace app\model\service;


use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

class Container {

    private static $created = false;

    private $map;
    private $clasess = array();
    private $rootFolder;

    /**
     * Container constructor.
     */
    private function __construct () {
        $this->map = new \stdClass();
    }

    /**
     * Vytvoří nový container
     *
     * @return Container
     * @throws Exception Pokud container již existuje
     */
    public static function getContainer () {
        if (self::$created)
            throw new Exception("Container už existuje");

        self::$created = true;
        $container = new Container();
        $container->mapValue('container', $container);

        return $container;
    }

    /**
     * Zaregistruje složku
     *
     * @param $folder
     */
    public function registerFolder ($folder) {
        if (empty($this->rootFolder))
            $this->rootFolder = $folder;

        $this->loadMap($folder);
    }

    private function loadMap($folder, $loadAnyway = false) {
        $cacheFile = "app\\cache\\map.php";
        $this->clasess = array();
        if (file_exists($cacheFile) && !$loadAnyway) {
            /** @noinspection PhpIncludeInspection */
            $this->clasess = require $cacheFile;
        } else {
            $string = "<?php " . PHP_EOL . "return array(" . PHP_EOL;
            $root = str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT']);
            foreach (new FileFilterIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folder))) as $fileInfo) {
                $pathName = $fileInfo->getPathname();

                $filePath = str_replace($root, '', $pathName);

                $file = strtolower($fileInfo->getBasename('.php'));
                $filePath = str_replace('.php', '', $filePath);
                $this->clasess[$file] = $filePath;
                $string .= "\t'$file' => '$filePath'," . PHP_EOL;
            }
            $string .= ");";
            file_put_contents($cacheFile, $string);
        }
    }

    /**
     * Injektuje objekt požadovanými třídami
     *
     * @param $obj object Injektovaný objekt
     * @param $reflection ReflectionClass Reflexní třída pro injektovaný objekt
     * @return object
     */
    private function injectClass ($obj, $reflection) {
        if ($doc = $reflection->getDocComment()) {
            $lines = explode("\n", $doc);
            foreach ($lines as $line) {
                if (count($parts = explode("@Inject", $line)) > 1) {
                    $parts = explode(" ", $parts[1]);
                    if (count($parts) > 1) {
                        $key = $parts[1];
                        $key = str_replace("\n", "", $key);
                        $key = str_replace("\r", "", $key);
                        $key = strtolower($key);

                        if (array_key_exists($key, $this->clasess) && !isset($this->map->$key))
                            $this->getInstanceOf($key);

                        if (isset($this->map->$key)) {
                            $property = $reflection->getProperty($key);
                            $property->setAccessible(true);
                            switch ($this->map->$key->type) {
                                case "value":
                                    $property->setValue($obj, $this->map->$key->value);
                                    break;
                                case "class":
                                    $property->setValue($obj, $this->getInstanceOf($this->map->$key->value, $this->map->$key->arguments));
                                    break;
                                case "classSingleton":
                                    if ($this->map->$key->instance === null) {
                                        $property->setValue($obj, $this->map->$key->instance = $this->getInstanceOf($this->map->$key->value, $this->map->$key->arguments));
                                    } else {
                                        $property->setValue($obj, $this->map->$key->instance);
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }

        return $obj;
    }

    /**
     * Přidá do mapy hodnoty
     * @param $key string Klíč
     * @param $obj object Hodnota
     */
    private function addToMap ($key, $obj) {
        if ($this->map === null) {
            $this->map = (object)array();
        }
        $this->map->$key = $obj;
    }

    /**
     * Přidá referenci třídy do mapy načtených tříd
     *
     * @param $key string Klíč, pod kterým se třída bude nacházet
     * @param $value object Reference na třídy
     */
    public function mapValue ($key, $value) {
        $this->addToMap($key, (object)array("value" => $value, "type" => "value"));
    }

    /**
     * Přidá třídu do mapy načtených tříd
     *
     * @param $key string Klíč, pod kterým se třída bude nacházet
     * @param $value string Plný název třídy
     * @param null $arguments Případné argumenty
     */
    public function mapClass ($key, $value, $arguments = null) {
        $this->addToMap($key, (object)array("value" => $value, "type" => "class", "arguments" => $arguments));
    }

    /**
     * Přidá třídu typu singleton do mapy načtených tříd
     *
     * @param $key string Klíč, pod kterým se třída bude nacházet
     * @param $value string Plný název třídy
     * @param null $arguments Případné argumenty
     */
    public function mapClassAsSingleton ($key, $value, $arguments = null) {
        $this->addToMap($key, (object)array("value" => $value, "type" => "classSingleton", "instance" => null, "arguments" => $arguments));
    }

    /**
     * Vrátí instanci třídy
     *
     * @param $className string Název třídy, kterou chcete získat
     * @param null $arguments Případné argumenty
     * @return object|null Null, pokud instanci není možné vytvořit, jinak referenci na objekt
     */
    public function getInstanceOf ($className, $arguments = null) {
        $className = strtolower($className);

        if (!array_key_exists($className, $this->clasess)) {
            $this->loadMap($this->rootFolder, true);
            if (!array_key_exists($className, $this->clasess))
                return null;
        }

        if (isset($this->map->$className))
            return $this->map->$className->value;

        $reflection = new ReflectionClass($this->clasess[$className]);

        if ($arguments === null || count($arguments) == 0) {
            $obj = new $this->clasess[$className];
        } else {
            if (!is_array($arguments)) {
                $arguments = array($arguments);
            }
            $obj = $reflection->newInstanceArgs($arguments);
        }

        $parentReflection = $reflection->getParentClass();

        while ($parentReflection != null) {
            $obj = $this->injectClass($obj, $parentReflection);
            $parentReflection = $parentReflection->getParentClass();
        }

        $obj = $this->injectClass($obj, $reflection);

        if (!isset($this->map->$className))
            $this->mapValue($className, $obj);

        return $obj;
    }
}