<?php

namespace app\model\factory;


use app\model\service\request\Request;
use app\model\util\StringUtils;

class RequestFactory {

    /**
     * Naparsuje URL adresu podle lomítek a vrátí pole parametrů
     *
     * @param $url string URL pro naparsování
     * @return array Pole(1. proměnná je vždy kontroler, zbytek jsou proměnný)
     */
    private function parseURL($url)
    {
        // Naparsuje jednotlivé části URL adresy do asociativního pole
        $parsedURL = parse_url($url);
        // Odstranění počátečního lomítka
        $parsedURL["path"] = ltrim($parsedURL["path"], "/");
        // Odstranění bílých znaků kolem adresy
        $parsedURL["path"] = trim($parsedURL["path"]);
        // Rozbití řetězce podle lomítek
        $partedWay = explode("/", $parsedURL["path"]);
        return $partedWay;
    }

    /**
     * Vytvoří nový request
     *
     * @return Request
     */
    public function createHttpRequest() {
        if (defined("UGLY_URL") && UGLY_URL == "true") {
            $requestUrl = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '/';
            $requestUrl = preg_replace("/.=/", "", $requestUrl); //str_replace("c=", "", $requestUrl);
        } else
            $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        $parsedURL = $this->parseURL($requestUrl);

        $controller = (!empty($parsedURL[0]) ? StringUtils::hyphensToCamel(array_shift($parsedURL)) : 'default');
        $action = (!empty($parsedURL[0]) ? StringUtils::hyphensToCamel($parsedURL[0]) : 'default');
        $ajax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        $params = $parsedURL;

        if (!empty($_POST)) $action .= 'Post';
        if ($ajax) $action .= 'Ajax';
        $action .= 'Action';

        return new Request($controller, $action, $params, $ajax, $_POST, $_FILES);
    }
}