<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\RelationshipManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class FriendsController
 * @Inject RelationshipManager
 * @Inject UserFactory
 * @package app\controller
 */
class FriendsController extends BaseController {

    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var RelationshipManager
     */
    private $relationshipmanager;

    public function onStartup () {
        parent::onStartup();

        $this->validateUser(USER_ROLE_MEMBER);
    }

    public function defaultAction (IRequest $request) {
        $this->view = "friends";
        $this->header['title'] = "Přátelé";

        $this->data['friends'] = $this->relationshipmanager->getFriendList();
        $this->data['myPendings'] = $this->relationshipmanager->getMyFriendRequests();
        $this->data['pendings'] = $this->relationshipmanager->getFriendRequests();
        $this->data['blockeds'] = $this->relationshipmanager->getBlockedFriends();
    }

    
    public function addAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->addFriend($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function acceptAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->acceptFriendRequest($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function declineAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->declineFriendRequest($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function cancelAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->cancelFriendRequest($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function unfriendAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->unfriend($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function blockAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->block($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function unblockAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $friendID = array_shift($params);

        try {
            $friend = $this->userfactory->getUserByID($friendID);
            $this->relationshipmanager->unblockFriend($friend);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}