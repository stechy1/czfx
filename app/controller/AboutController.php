<?php

namespace app\controller;


use app\model\service\request\IRequest;

class AboutController extends BaseController {

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        $this->header['title'] = 'O projektu';
        $this->view = 'about';
    }
}