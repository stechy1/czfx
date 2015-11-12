<?php

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminSettingsController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
    }


}