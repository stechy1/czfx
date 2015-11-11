<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use app\model\service\exception\MyException;

/**
 * Class LoginController
 * @Inject UserManager
 * @package app\controller
 */
class LoginController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {

        $this->header['title'] = 'Login';
        $this->view = 'login';
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    function defaultPostAction (IRequest $request) {
        if ($this->usermanager->isLoged())
            $this->redirect('account');
        $this->header['title'] = 'Přihlášení';
            try {
                $this->usermanager->login($_POST);
                $this->addMessage(new CallBackMessage("Byl jste úspěšně přihlášen"));
                $this->redirect('profile');
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }

        $this->header['title'] = 'Login';
        $this->view = 'login';
    }
}