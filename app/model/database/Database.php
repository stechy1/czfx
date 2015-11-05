<?php

namespace app\model\database;


use PDO;

class Database implements IDatabase {
    /**
     * Databázové spojení
     * @var PDO
     */
    private $connection;

    /**
     * Výchozí nastavení ovladače
     * @var array
     */
    private static $settings = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    /**
     * Připojí se k databázi pomocí daných údajů
     * @param $host
     * @param $uzivatel
     * @param $heslo
     * @param $databaze
     */
    public function connect($host, $uzivatel, $heslo, $databaze)
    {
        if (isset($this->connection))
            return;

        $this->connection = @new PDO(
            "mysql:host=$host;dbname=$databaze",
            $uzivatel,
            $heslo,
            self::$settings
        );

    }

    /**
     * Spustí query a vrátí z něj první řádek
     * @param $dotaz
     * @param array $parameters
     * @return mixed
     */
    public function queryOne($dotaz, $parameters = array())
    {
        $navrat = $this->connection->prepare($dotaz);
        $navrat->execute($parameters);
        return $navrat->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Spustí query a vrátí všechny jeho řádky jako pole asociativních polí
     * @param $dotaz
     * @param array $parameters
     * @return mixed
     */
    public function queryAll($dotaz, $parameters = array())
    {
        $navrat = $this->connection->prepare($dotaz);
        $navrat->execute($parameters);
        return $navrat->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Spustí query a vrátí z něj první sloupec prvního řádku
     * @param $dotaz
     * @param array $parameters
     * @return int
     */
    public function queryItself($dotaz, $parameters = array())
    {
        $vysledek = $this->queryOne($dotaz, $parameters);
        if ($vysledek) {
            $vysledek = array_values($vysledek);
            return $vysledek[0];
        }
        return 0;
    }

    /**
     * Spustí query a vrátí počet ovlivněných řádků
     * @param $dotaz
     * @param array $parameters
     * @return mixed
     */
    public function query($dotaz, $parameters = array())
    {
        $navrat = $this->connection->prepare($dotaz);
        $navrat->execute($parameters);
        return $navrat->rowCount();
    }

    /**
     * Vloží do tabulky nový řádek jako data z asociativního pole
     * @param $table
     * @param array $parameters
     * @return mixed
     */
    public function insert($table, $parameters = array())
    {
        return $this->query("INSERT INTO `$table` (`" .
            implode('`, `', array_keys($parameters)) .
            "`) VALUES (" . str_repeat('?,', sizeOf($parameters) - 1) . "?)",
            array_values($parameters));
    }

    /**
     * Změní řádek v tabulce tak, aby obsahoval data z asociativního pole
     * @param $tabulka
     * @param array $hodnoty
     * @param $podminka
     * @param array $parameters
     * @return mixed
     */
    public function update($tabulka, $hodnoty = array(), $podminka, $parameters = array())
    {
        return $this->query("UPDATE `$tabulka` SET `" .
            implode('` = ?, `', array_keys($hodnoty)) .
            "` = ? " . $podminka,
            array_merge(array_values($hodnoty), $parameters));
    }

    /**
     * Započne novou transakci
     * @return boolean True, pokud se podařilo založit novou transakci, jinak false
     */
    function beginTransaction () {
        return $this->connection->beginTransaction();
    }

    /**
     * Provede všechny změny
     * @return boolean True, pokud se provedly všechny změny úspěšně, jinak false
     */
    function commit () {
        return $this->connection->commit();
    }

    /**
     * Vrátí zpět všechny změny co byly provedeny od posledního zavolání beginTransaction
     * @return boolean True, pokud byly vráceny všechny změny úspěšně, jinak false
     */
    function rollback () {
        return $this->connection->rollBack();
    }

    /**
     * Vrací ID posledně vloženého záznamu
     * @return mixed
     */
    public function getLastId()
    {
        return $this->connection->lastInsertId();
    }
}