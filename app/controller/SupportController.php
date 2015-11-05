<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\SupportManager;
use app\model\service\request\IRequest;
use Exception;

/**
 * Class SupportController
 * @Inject SupportManager
 * @package app\controller
 */
class SupportController extends BaseController {

    /**
     * @var SupportManager
     */
    private $supportmanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = 'Podpora';
        $this->view = 'support';
    }

    /**
     * Výchozí akce kontroleru po odeslání formuláře
     * @param IRequest $request
     */
    public function defaultPostAction (IRequest $request) {
        $this->validateUser();
        try {
            $this->supportmanager->addReport($_POST);
        } catch (Exception $ex) {
            //$this->callBack->setFail();
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

}