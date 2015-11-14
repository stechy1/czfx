<?php

namespace app\controller\api;


use app\controller\BaseController;
use app\model\service\request\IRequest;

class ApiBaseController extends BaseController {

    /**
     * ApiBaseController constructor.
     */
    public function __construct () {
        $this->pathToView .= "api/";
    }

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->redirect('api');
    }

    /**
     * @param IRequest $request
     */
    public function defaultPostAction (IRequest $request) {
        $this->redirect('error');
    }

}