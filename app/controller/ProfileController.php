<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\RelationshipManager;
use app\model\manager\UserManager;
use app\model\Relationship;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

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
                $params = $request->getParams();
                $userID = array_shift($params);
                $user = $this->userfactory->getUserByID($userID);
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
}