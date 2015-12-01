<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\UserManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\form\SettingsPersonalForm;

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

    public function onStartup () {
        parent::onStartup();

        if (!$this->usermanager->isLoged())
            $this->redirect('login');
    }

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        try {
            $user = $this->userfactory->getUserFromSession();
            $this->data['user'] = $user->toArray();
            $this->data['personalForm'] = new SettingsPersonalForm($user);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('login');
        }



        $this->header['title'] = "Nastavení";
        $this->view = 'settings';
    }

    public function defaultPostAction (IRequest $request) {
        $this->redirect('settings');
    }

    public function generalPostAction (IRequest $request) {
        $this->redirect('settings');
    }

    public function passwordPostAction (IRequest $request) {
        try {
            $this->usermanager->changePassword($request->getPost());
            $this->addMessage(new CallBackMessage("Heslo bylo úspěšně změněno"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->redirect('settings');
    }

    public function personalPostAction (IRequest $request) {
        $personalForm = new SettingsPersonalForm($this->userfactory->getUserFromSession());
        $keyArray = $personalForm->getControlNames();
        $pass = $personalForm->getData('password');

        unset($keyArray[sizeof($keyArray) - 1]);

        if ($personalForm->isPostBack() && $personalForm->isValid()) {
            try {
                $this->usermanager->updateData($personalForm->getData(), $keyArray, $pass);
                $this->addMessage(new CallBackMessage("Údaje byly úspěšně upraveny"));
            } catch (MyException $ex) {
                $this->addMessage(new CallbackMessage($ex->getMessage(), CallbackMessage::DANGER));
            }
        } else {
            $this->addMessages($personalForm->getErrors());
        }

        $this->redirect('settings');
    }

    public function deletePostAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->addMessage(new CallBackMessage("Nesnažte se provádět nekalé praktiky!", CallBackMessage::DANGER));
            $this->redirect('settings');
        }

        $whom = $request->getParams()[1];
        $pass = $request->getPost("password");
        if ($whom == "me") {
            try {
                $this->usermanager->delete($pass);
                $this->usermanager->logout(false);
                $this->redirect('login');
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }
        } else {
            $this->addMessage(new CallBackMessage("Můžete smazat pouze svůj účet", CallBackMessage::WARNING));
        }
        $this->redirect('settings');
    }


}