<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\UserManager;
use app\model\service\request\IRequest;
use Exception;

/**
 * Class CheckCodeController
 * @Inject UserFactory
 * @Inject UserManager
 * @package app\controller
 */
class CheckCodeController extends BaseController {

    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var UserManager
     */
    private $usermanager;


    private function activate($code) {
        try {
            $this->usermanager->checkCode($code);
            $this->addMessage(new CallBackMessage("Váš účet byl aktivován"));
            if ($this->usermanager->isLoged()) {
                $_SESSION['user']['user_activated'] = 1;
                $this->redirect("profile");
            }
            $this->redirect("login");
        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     */
    public function onStartup () {
        try {
            $user = $this->userfactory->getUserFromSession();
            if ($user->isOnline() && $user->isActivated()) $this->redirect('profile');
        } catch (Exception $ex) {}


        $this->header['title'] = "Ověření kódu";
        $this->view = 'check-code';
    }

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            $code = $request->getParams()[0];
            $this->activate($code);
        }
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    public function defaultPostAction (IRequest $request) {
        $this->activate($request->getPost("code"));
    }
}