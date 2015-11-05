<?php

namespace app\model\service\request;


class Request implements IRequest {

    private $controller;
    private $action;
    private $params;
    private $ajax;

    private $post;
    private $files;

    /**
     * Request constructor.
     * @param $controller string Název obslužného kontroleru
     * @param $action string Akce, která se má vykonat
     * @param $params array Pole parametrů
     * @param $ajax boolean True, pokud se jedná o ajaxový požadavek
     * @param $post array Pole parametrů v postu
     * @param $files array Pole nahraných souborů
     */
    public function __construct ($controller, $action, $params, $ajax, $post, $files) {
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params;
        $this->ajax = $ajax;
        $this->post = $post;
        $this->files = $files;
    }

    /**
     * @return boolean True, pokud se jedná o ajaxový požadavek, jinak false
     */
    function isAjax () {
        return $this->ajax;
    }

    /**
     * @return string Vrátí název kontrolleru
     */
    function getController () {
        return $this->controller;
    }

    /**
     * @return string Vrátí název akce, která se má provést
     */
    function getAction () {
        return $this->action;
    }

    /**
     * Vrátí hodnotu uloženou v postu na daném klíči. Pokud hodnota neexistuje, vrátí výchozí hodnotu
     * @param null $key Klíč hledané hodnoty
     * @param null $default Výchozí hodnota, pokud není v postu
     * @return mixed Hodnotu z postu nebo výchozí hodnotu
     */
    function getPost ($key = null, $default = null) {
        if (func_num_args() === 0) {
            return $this->post;

        } elseif (isset($this->post[$key])) {
            return $this->post[$key];

        } else {
            return $default;
        }
    }

    /**
     * Vrátí nahraný soubor
     * @param $key
     * @return mixed
     */
    function getFile ($key) {
        return isset($this->files[$key]) ? $this->files[$key] : NULL;
    }

    /**
     * Vrátí pole nahraných souborů
     * @return array
     */
    function getFiles () {
        return $this->files;
    }

    /**
     * @return array Pole naparsovaných parametrů z adresy
     */
    function getParams () {
        return $this->params;
    }

    /**
     * @return boolean True, pokud request obsahuje parametry, jinak false
     */
    function hasParams () {
        return sizeof($this->params) - 1 >= 0;
    }

    /**
     * @return bool True, pokud požadavek obsahuje data v POSTu, jinak false
     */
    function hasPost () {
        return !empty($this->post);
    }

    /**
     * @return boolean True, pokud požadavek obsahuje nějaké uživatelem nahrané soubory, jinak false
     */
    function hasFiles () {
        return !empty($this->files);
    }


}