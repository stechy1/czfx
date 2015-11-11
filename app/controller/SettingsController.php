<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
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
        /*try {
            $this->validateUser(UserRole::MEMBER);
            $this->usermanager->updateUser($request->getPost());
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->redirect('settings');*/
    }
}