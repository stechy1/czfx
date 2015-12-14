<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\form\LoginForm;
use app\model\snippet\form\NewPasswordRequestForm;

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

    public function onStartup () {
        parent::onStartup();

        if ($this->usermanager->isLoged())
            $this->redirect('profile');
    }

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = 'Přihlášení';
        $this->view = 'login';

        $this->data['form'] = new LoginForm();
    }

    public function newPasswordRequestAction (IRequest $request) {
        $this->header['title'] = "Poslat žádost o nové heslo";
        $this->view = "password-reset-request";

        $this->data['form'] = new NewPasswordRequestForm();
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    public function defaultPostAction (IRequest $request) {
        $this->header['title'] = 'Přihlášení';

        /** @var LoginForm $form */
        $form = new LoginForm();
        if ($form->isPostBack()) {
            if ($form->isValid()) {
                try {
                    $this->usermanager->login($form->getData());

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
        $this->data['form'] = $form;
    }
}