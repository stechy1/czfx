<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\UserManager;
use app\model\Relationship;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\manager\RelationshipManager;

/**
 * Class ProfileController
 * @Inject UserManager
 * @Inject UserFactory
 * @Inject RelationshipManager
 * @package app\controller
 */
class ProfileController extends BaseController {

    /**
     * @var UserManager
     */
    private $usermanager;
    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var RelationshipManager
     */
    private $relationshipmanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            try {
                $user = $this->userfactory->getUserByID(intval($request->getParams()[0]));
                $this->data['user'] = $user->toArray();
                $this->data['showFriendButton'] = true;
                $rel = $this->relationshipmanager->getFriendRelationship($user);
                $this->data['isFriend'] = $rel->isStatus(Relationship::STATUS_ACCEPTED);
                $this->data['isPending'] = $rel->isStatus(Relationship::STATUS_PENDING);

            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect('index');
            }
        } else {
            try {
                $user = $this->userfactory->getUserFromSession();
                $this->data['user'] = $user->toArray();
                $this->data['showFriendButton'] = false;
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect("login");
            }
        }

        $this->data['isLoged'] = $this->usermanager->isLoged();
        $this->header['title'] = "Profil";
        $this->view = 'profile';
    }

    public function logoutAction (IRequest $request) {
        try {
            $this->usermanager->logout();
            $this->addMessage(new CallBackMessage("Odhlášení problěhlo v pořádku"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
        $this->redirect("login");
    }

    public function uploadAjaxAction (IRequest $request) {
        try {
            $this->validateUser(USER_ROLE_MEMBER);
            $result = $this->usermanager->changeAvatar($request->getFile('avatar'));
            $this->callBack->addData(new CallBackData("imgSrc", $result), false);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    /*public function addtofriendAjaxAction (IRequest $request) {
        if ($request->hasParams()) {
            $params = $request->getParams();
            array_shift($params);
            $friendID = array_shift($params);

            try {
                $user = $this->userfactory->getUserFromSession();
                $user->getRole()->valid(USER_ROLE_MEMBER);

                $friend = $this->userfactory->getUserByID($friendID);
                $this->relationshipmanager->addFriend($friend);

            } catch (MyException $ex) {
                $this->callBack->setFail();
                $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }
        } else {
            $this->callBack->setFail();
        }
    }

    public function removefromfriendAjaxAction (IRequest $request) {
        if ($request->hasParams(1)) {
            $params = $request->getParams();
            array_shift($params);
            $friendID = array_shift($params);

            try {
                $user = $this->userfactory->getUserFromSession();
                $user->getRole()->valid(USER_ROLE_MEMBER);

                $friend = $this->userfactory->getUserByID($friendID);
                $this->relationshipmanager->unfriend($friend);

            } catch (MyException $ex) {
                $this->callBack->setFail();
                $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }
        } else {
            $this->callBack->setFail();
        }
    }*/
}