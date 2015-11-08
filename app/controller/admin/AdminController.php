<?php

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->redirect('admin-dashboard');
    }
}