<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\CaptchaService;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\form\RegisterForm;

/**
 * Class RegistrationController
 * @Inject UserManager
 * @Inject RegisterForm
 * @package app\controller
 */
class RegistrationController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;
    /**
     * @var RegisterForm
     */
    private $registerform;

    public function onStartup () {
        parent::onStartup();
        if ($this->usermanager->isLoged())
            $this->redirect('profile');

        $this->data['form'] = $this->registerform;
    }

    /**
     * Výchozí akce kontroleru
     *
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        $this->header['title'] = 'Registrace';
        $this->view = 'registration';
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     *
     * @param IRequest $request
     */
    function defaultPostAction (IRequest $request) {
        if ($this->registerform->isPostBack()) {
            try {
                CaptchaService::verify($request->getPost("g-recaptcha-response", null));
                if ($this->registerform->isValid()) {
                    $this->usermanager->register($this->registerform->getData());
                    $this->addMessage(new CallBackMessage("Registrace proběhla úspěšně. Můžete se přihlásit."));
                    $this->redirect('login');
                } else {
                    $this->addMessages($this->registerform->getErrors());
                }
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }
        }

        $this->header['title'] = 'Registrace';
        $this->view = 'registration';
    }
}