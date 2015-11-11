<?php

namespace app\model\service\request;


interface IRequest {

    const
        GET = 'GET',
        POST = 'POST',
        HEAD = 'HEAD',
        PUT = 'PUT',
        DELETE = 'DELETE';

    /**
     * Zjistí, zda-li je požadavek typu AJAX
     *
     * @return boolean True, pokud se jedná o ajaxový požadavek, jinak false
     */
    function isAjax();

    /**
     * Získá název kontroleru
     *
     * @return string Vrátí název kontrolleru
     */
    function getController();

    /**
     * Získá název akce, která se má provést v kontroleru
     *
     * @return string Vrátí název akce, která se má provést
     */
    function getAction();

    /**
     * Vrátí hodnotu uloženou v postu na daném klíči. Pokud hodnota neexistuje, vrátí výchozí hodnotu
     *
     * @param null $key Klíč hledané hodnoty
     * @param null $default Výchozí hodnota, pokud není v postu
     * @return mixed Hodnotu z postu nebo výchozí hodnotu
     */
    function getPost($key = null, $default = null);

    /**
     * Vrátí nahraný soubor
     *
     * @param $key string Klíč, pod kterým se má soubor nacházet
     *
     * @return array|null
     */
    function getFile($key);

    /**
     * Vrátí pole nahraných souborů
     *
     * @return array
     */
    function getFiles();

    /**
     * Vrátí pole parametrů
     *
     * @return array Pole naparsovaných parametrů z adresy
     */
    function getParams();

    /**
     * Zkontroluje, zda-li požadavek obsahuje nějaké parametry
     *
     * @return boolean True, pokud request obsahuje parametry, jinak false
     */
    function hasParams();

    /**
     * Zkontroluje, zda-li požadavek obsahuje data v POSTu
     *
     * @return bool True, pokud požadavek obsahuje data v POSTu, jinak false
     */
    function hasPost();

    /**
     * Zkontroluje, zda-li požadavek obsahuje nahrané soubory
     *
     * @return boolean True, pokud požadavek obsahuje nějaké uživatelem nahrané soubory, jinak false
     */
    function hasFiles();
}