<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\form\LoginForm;

/**
 * Class LoginController
 * @Inject UserManager
 * @Inject LoginForm
 * @package app\controller
 */
class LoginController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;
    /**
     * @var LoginForm
     */
    private $loginform;

    public function onStartup () {
        parent::onStartup();

        if ($this->usermanager->isLoged())
            $this->redirect('profile');

        $this->data['form'] = $this->loginform;
    }

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        $this->header['title'] = 'Přihlášení';
        $this->view = 'login';
    }

    function newPasswordRequestAction (IRequest $request) {
        $this->header['title'] = "Poslat žádost o nové heslo";
        $this->view = "password-reset-request";
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    function defaultPostAction (IRequest $request) {
        $this->header['title'] = 'Přihlášení';
        if ($this->loginform->isPostBack()) {
            if ($this->loginform->isValid()) {
                try {
                    $this->usermanager->login($this->loginform->getData());
                    $this->redirect('profile');
                } catch (MyException $ex) {
                    $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
                }
            } else {
                $this->addMessage(new CallBackMessage("Formulář není správně vyplněn", CallBackMessage::DANGER));
            }
        }

        $this->header['title'] = 'Přihlášení';
        $this->view = 'login';
    }
}