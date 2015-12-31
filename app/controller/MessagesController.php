<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\MessageFactory;
use app\model\factory\UserFactory;
use app\model\manager\MessageManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class MessagesController
 * @Inject UserFactory
 * @Inject MessageManager
 * @Inject MessageFactory
 * @package app\controller
 */
class MessagesController extends BaseController {

    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var MessageManager
     */
    private $messagemanager;
    /**
     * @var MessageFactory
     */
    private $messagefactory;

    public function onStartup () {
        parent::onStartup();

        $this->validateUser(USER_ROLE_MEMBER);
    }

    public function defaultAction (IRequest $request) {
        $this->view = "messages";
        $this->header['title'] = "Zprávy";

        try {
            $this->data['conversations'] = $this->messagefactory->getAllMessages();
        } catch (MyException $ex) {
            $this->data['conversations'] = null;
        }
    }

    public function conversationAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->addMessage(new CallBackMessage("Není určena konverzace", CallBackMessage::WARNING));
            $this->redirect("messages");
        }

        $params = $request->getParams();
        array_shift($params);
        $param = array_shift($params);

        try {
            $room = $this->messagefactory->getConversationRoom($param);
            $this->data['roomHash'] = $room->getHash();
            $this->data['messages'] = $this->messagefactory->getMessages($room);
        } catch (MyException $ex) {
            $this->data['roomHash'] = null;
            $this->data['messages'] = null;
        }

        $this->view = "messages-conversation";
        $this->header['title'] = "Konverzace";
    }

    public function sendPostAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Zprávu se nepodařilo odelat"));
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $roomHash = array_shift($params);

        try {
            $content = $request->getPost('message_content');
            $room = $this->messagefactory->getConversationRoom($roomHash);
            $message = $this->messagefactory->getMessageFromRawData($content, $room);
            $this->messagemanager->send($message);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }


}