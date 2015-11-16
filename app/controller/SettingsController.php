<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use app\model\service\exception\MyException;

/**
 * Class SettingsController
 * @Inject UserManager
 * @Inject UserFactory
 * @package app\controller
 */
class SettingsController extends BaseController {

    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var UserManager
     */
    private $usermanager;


    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        try {
            $this->data['user'] = $this->userfactory->getUserFromSession()->toArray();
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('login');
        }

        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
    }

    public function generalPostAction (IRequest $request) {

    }

    public function defaultPostAction (IRequest $request) {
        // TODO dodělat uložení nastavení
    }
}