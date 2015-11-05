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
     * @param $host
     * @param $uzivatel
     * @param $heslo
     * @param $databaze
     */
    function connect($host, $uzivatel, $heslo, $databaze);

    /**
     * Spustí query a vrátí z něj první řádek
     * @param $dotaz
     * @param array $parametry
     * @return mixed
     */
    function queryOne($dotaz, $parametry = array());

    /**
     * Spustí query a vrátí všechny jeho řádky jako pole asociativních polí
     * @param $dotaz
     * @param array $parametry
     * @return mixed
     */
    function queryAll($dotaz, $parametry = array());

    /**
     * Spustí query a vrátí z něj první sloupec prvního řádku
     * @param $dotaz
     * @param array $parametry
     * @return int
     */
    function queryItself($dotaz, $parametry = array());

    /**
     * Spustí query a vrátí počet ovlivněných řádků
     * @param $dotaz
     * @param array $parametry
     * @return mixed
     */
    function query($dotaz, $parametry = array());

    /**
     * Vloží do tabulky nový řádek jako data z asociativního pole
     * @param $tabulka
     * @param array $parametry
     * @return mixed
     */
    function insert($tabulka, $parametry = array());

    /**
     * Změní řádek v tabulce tak, aby obsahoval data z asociativního pole
     * @param $tabulka
     * @param array $hodnoty
     * @param $podminka
     * @param array $parametry
     * @return mixed
     */
    function update($tabulka, $hodnoty = array(), $podminka, $parametry = array());

    /**
     * Započne novou transakci
     * @return boolean True, pokud se podařilo založit novou transakci, jinak false
     */
    function beginTransaction();

    /**
     * Provede všechny změny
     */
    function commit();

    /**
     * Vrátí zpět všechny změny co byly provedeny od posledního zavolání beginTransaction
     */
    function rollback();

    /**
     * Vrací ID posledně vloženého záznamu
     * @return mixed
     */
    function getLastId();
}