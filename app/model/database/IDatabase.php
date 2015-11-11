<?php

namespace app\model\database;

/**
 * Interface IDatabase
 * Rozhraní definující metody pro všechny databáze
 * @package app\model\database
 */
interface IDatabase {

    /**
     * Připojí se k databázi pomocí daných údajů
     *
     * @param $host string Hostitelský stroj, kde běží databáze
     * @param $uzivatel string Jméno uživatele, pokd kterým se má aplikace připojit
     * @param $heslo string Heslo k databázi
     * @param $databaze string Název databáze, která se má použít
     */
    function connect($host, $uzivatel, $heslo, $databaze);

    /**
     * Spustí dotaz a vrátí z něj první řádek
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return array|null Asociativní pole obsahující data odpovídající záznamu, nebo null
     */
    function queryOne($query, $parameters = array());

    /**
     * Spustí dotaz a vrátí všechny jeho řádky jako pole asociativních polí
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return array|null Asociativní pole obsahující data odpovídající záznamu, nebo null
     */
    function queryAll($query, $parameters = array());

    /**
     * Spustí dotaz a vrátí z něj první sloupec prvního řádku
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return int Počet ovlivněných řádek
     */
    function queryItself($query, $parameters = array());

    /**
     * Spustí dotaz a vrátí počet ovlivněných řádků
     *
     * @param $query string Dotaz
     * @param array $parameters Parametry dotazu
     * @return int Počet ovlivněných řádek
     */
    function query($query, $parameters = array());

    /**
     * Vloží do tabulky nový řádek
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param array $values Hodnoty, které se mají vložit
     * @return int Počet ovlivněných řádek
     */
    function insert($table, $values = array());

    /**
     * Upraví záznam ve vybrané tabulce
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param array $values Hodnoty, které se mají změnit
     * @param $condition string Podmínka
     * @param array $parameters Parametry podmínky
     * @return int Počet ovlivněných řádek
     */
    function update($table, $values = array(), $condition, $parameters = array());

    /**
     * Smaže záznam(y) z vybrané tabulky podle podmínky
     *
     * @param $table string Tabulka, s kterou se bude manipulovat
     * @param null $condition Podmínka
     * @param array $parameters Parametry
     * @return int Počet ovlivněných řádek
     */
    function delete($table, $condition, $parameters = array());

    /**
     * Započne novou transakci
     *
     * @return boolean True, pokud se podařilo založit novou transakci, jinak false
     */
    function beginTransaction();

    /**
     * Provede všechny změny
     *
     * @return boolean True, pokud se provedly všechny změny úspěšně, jinak false
     */
    function commit();

    /**
     * Vrátí zpět všechny změny co byly provedeny od posledního zavolání beginTransaction
     *
     * @return boolean True, pokud byly vráceny všechny změny úspěšně, jinak false
     */
    function rollback();

    /**
     * Vrací ID posledně vloženého záznamu
     *
     * @return mixed
     */
    function getLastId();
}