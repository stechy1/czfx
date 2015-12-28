<?php

namespace app\controller\admin;


use app\controller\BaseController;
use app\model\callback\CallBackMessage;
use app\model\service\exception\MyException;

class AdminBaseController extends BaseController {

    /**
     * AdminBaseController constructor.
     */
    public function __construct () {
        $this->pathToView .= "admin/";
    }

    public function onStartup () {
        parent::onStartup();

        try {
            $this->validateUser(USER_ROLE_ADMIN, true);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('login');
        }
    }
}