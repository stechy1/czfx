<?php

namespace app\controller;


use app\model\service\request\IRequest;

class ErrorController extends BaseController {

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        header("HTTP/1.0 404 Not Found");

        $this->header['title'] = 'Stránka nenalezena';
        $this->view = 'error';
    }
}