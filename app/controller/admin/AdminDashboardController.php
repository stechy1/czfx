<?php

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminDashboardController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->redirect("admin");
    }


}