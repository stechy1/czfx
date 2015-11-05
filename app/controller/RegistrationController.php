<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use Exception;

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
            $this->usermanager->register($_POST);
            //$userManager->login($_POST);
            $this->addMessage(new CallBackMessage("Registrace proběhla úspěšně. \nMůžete se přihlásit."));
            $this->redirect('login');
        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}