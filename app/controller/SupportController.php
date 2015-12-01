<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\ReportFactory;
use app\model\manager\SupportManager;
use app\model\service\CaptchaService;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class SupportController
 * @Inject SupportManager
 * @Inject ReportFactory
 * @package app\controller
 */
class SupportController extends BaseController {

    /**
     * @var SupportManager
     */
    private $supportmanager;
    /**
     * @var ReportFactory
     */
    private $reportfactory;

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
        try {
            CaptchaService::verify($request->getPost("g-recaptcha-response", null));
            $this->validateUser(USER_ROLE_MEMBER);
            $message = $this->reportfactory->getReportFromRawData($request->getPost());
            $this->supportmanager->addReport($message);
            $this->addMessage(new CallBackMessage("Zpráva byla úspěšně poslána"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->redirect('support');
    }

}