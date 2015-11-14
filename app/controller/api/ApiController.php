<?php

namespace app\controller\api;


use app\model\service\request\IRequest;

class ApiController extends ApiBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = "Api";
        $this->view = 'api';
    }


}