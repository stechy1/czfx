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
     * @return boolean True, pokud se jedná o ajaxový požadavek, jinak false
     */
    function isAjax();

    /**
     * @return string Vrátí název kontrolleru
     */
    function getController();

    /**
     * @return string Vrátí název akce, která se má provést
     */
    function getAction();

    /**
     * Vrátí hodnotu uloženou v postu na daném klíči. Pokud hodnota neexistuje, vrátí výchozí hodnotu
     * @param null $key Klíč hledané hodnoty
     * @param null $default Výchozí hodnota, pokud není v postu
     * @return mixed Hodnotu z postu nebo výchozí hodnotu
     */
    function getPost($key = null, $default = null);

    /**
     * @param $key
     * @return mixed Vrátí nahraný soubor
     */
    function getFile($key);

    /**
     * @return array Vrátí pole nahraných souborů
     */
    function getFiles();

    /**
     * @return array Pole naparsovaných parametrů z adresy
     */
    function getParams();

    /**
     * @return boolean True, pokud request obsahuje parametry, jinak false
     */
    function hasParams();

    /**
     * @return boolean True, pokud požadavek obsahuje data v POSTu, jinak false
     */
    function hasPost();

    /**
     * @return boolean True, pokud požadavek obsahuje nějaké uživatelem nahrané soubory, jinak false
     */
    function hasFiles();
}