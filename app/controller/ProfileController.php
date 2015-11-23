<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use app\model\UserRole;
use app\model\service\exception\MyException;

/**
 * Class ProfileController
 * @Inject UserManager
 * @Inject UserFactory
 * @package app\controller
 */
class ProfileController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;
    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            try {
                $this->data['user'] = $this->userfactory->getUserByID(intval($request->getParams()[0]))->toArray();
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect('index');
            }

        } else {
            try {
                $user = $this->userfactory->getUserFromSession();
                $this->data['user'] = $user->toArray();
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect("login");
            }
        }

        $this->data['isLoged'] = $this->usermanager->isLoged();
        $this->header['title'] = "Profil";
        $this->view = 'profile';
    }

    public function logoutAction (IRequest $request) {
        try {
            $this->usermanager->logout();
            $this->addMessage(new CallBackMessage("Odhlášení problěhlo v pořádku"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
        $this->redirect("login");
    }

    public function uploadAjaxAction (IRequest $request) {
        try {
            $this->validateUser(USER_ROLE_MEMBER);
            $result = $this->usermanager->changeAvatar($request->getFile('avatar'));
            $this->callBack->addData(new CallBackData("imgSrc", $result), false);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}