<?php
/**
 * Created by PhpStorm.
 * User: stech
 * Date: 07.11.2015
 * Time: 19:58
 */

namespace app\controller\admin;


use app\model\service\request\IRequest;

class AdminDashboardController extends AdminBaseController {

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = "Dashboard";
        $this->view = 'Dashboard';
    }


}