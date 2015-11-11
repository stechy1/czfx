<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\CaptchaService;
use app\model\service\request\IRequest;
use app\model\service\exception\MyException;

/**
 * Class RegistrationController
 * @Inject UserManager
 * @package app\controller
 */
class RegistrationController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        $this->header['title'] = 'Registrace';
        $this->view = 'registration';
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    function defaultPostAction (IRequest $request) {
        try {
            CaptchaService::verify($request->getPost("g-recaptcha-response", null));
            $this->usermanager->register($_POST);
            $this->addMessage(new CallBackMessage("Registrace proběhla úspěšně. \nMůžete se přihlásit."));
            $this->redirect('login');
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('registration');
        }
    }
}