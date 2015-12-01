<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\ForumManager;
use app\model\service\CaptchaService;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class ForumController
 * @Inject ForumManager
 * @Inject UserFactory
 * @package app\controller
 */
class ForumController extends BaseController {

    /**
     * @var ForumManager
     */
    private $forummanager;
    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = 'Forum';
        $this->view = 'forum-categories';

        try {
            $user = $this->userfactory->getUserFromSession();
            $user->getRole()->valid(USER_ROLE_ADMIN);
            $this->data['isAdmin'] = true;
        } catch (MyException $ex) {
            $this->data['isAdmin'] = false;
        }

        try {
            $this->data['forumCategories'] = $this->forummanager->getCategories();
        } catch (MyException $ex) {
            $this->data['forumCategories'] = null;
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function showTopicsAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->addMessage(new CallBackMessage("Nebyla zadána žádná kategorie", CallBackMessage::INFO));
            $this->redirect('forum');
        }

        $category = (isset($request->getParams()[1]))? $request->getParams()[1] : "-1";
        try {
            $this->view = 'forum-topics';

            try {
                $user = $this->userfactory->getUserFromSession();
                $user->getRole()->valid(USER_ROLE_ADMIN);
                $this->data['isAdmin'] = true;
            } catch (MyException $ex) {
                $this->data['isAdmin'] = false;
            }

            $category = $this->forummanager->getCategory($category);
            $this->header['title'] = "Forum / " . $category['category_name'];
            $this->data['categoryName'] = $category['category_name'];
            $this->data['categoryUrl'] = $category['category_url'];

            $topics = $this->forummanager->getTopics($category['category_url']);
            $this->data['topics'] = $topics;

        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('forum');
        }
    }

    public function deleteTopicAjaxAction (IRequest $request) {
        if (!$request->hasParams() && empty($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co smazat", CallBackMessage::WARNING));
            return;
        }

        $topicID = $request->getParams()[1];

        try {
            $this->validateUser(USER_ROLE_ADMIN);
            $this->forummanager->deleteTopic($topicID);
            $this->callBack->addMessage(new CallBackMessage("Téma bylo úspěšně smazáno"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function showPostsAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->addMessage(new CallBackMessage("Nebyla zadána žádná kategorie", CallBackMessage::INFO));
            $this->redirect('forum');
        }

        $category = (isset($request->getParams()[1]))? $request->getParams()[1] : "-1";
        $topic = (isset($request->getParams()[2]))? $request->getParams()[2] : "-1";

        try {
            $this->data['posts'] = $this->forummanager->getPosts($topic);
            try {
                $user = $this->userfactory->getUserFromSession();
                $user->getRole()->valid(USER_ROLE_ADMIN);
                $this->data['isAdmin'] = true;
            } catch (MyException $ex) {
                $this->data['isAdmin'] = false;
            }

            $topic = $this->forummanager->getTopic($topic);
            $category = $this->forummanager->getCategory($category);

            $this->data['categoryName'] = $category['category_name'];
            $this->data['categoryURL'] = $category['category_url'];
            $this->data['topicSubject'] = $topic['topic_subject'];

            $this->header['title'] = "Forum / " . $category['category_name'] . " / " . $topic['topic_subject'];
            $this->view = 'forum-posts';
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
            $this->redirect('forum');
        }
    }

    public function showPostsPostAction (IRequest $request) {
        try {
            $this->validateUser(USER_ROLE_MEMBER);
            CaptchaService::verify($request->getPost("g-recaptcha-response", null));
            $this->forummanager->addPost($request->getPost('post_content'));
            $this->addMessage(new CallBackMessage("Zpráva byla úspěšně odeslána"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->showPostsAction($request);
    }

    public function deletePostAjaxAction (IRequest $request) {
        if (!$request->hasParams() && empty($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co smazat", CallBackMessage::WARNING));
            return;
        }

        $postID = $request->getParams()[1];

        try {
            $this->validateUser(USER_ROLE_ADMIN);
            $this->forummanager->deletePost($postID);
            $this->callBack->addMessage(new CallBackMessage("Příspěvek byl úspěšně smazán"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function newTopicAction (IRequest $request) {
        $this->header['title'] = "Forum / Nové vlákno";
        $this->view = 'forum-new-topic';
    }

    public function newTopicPostAction (IRequest $request) {
        try {
            $this->validateUser(USER_ROLE_MEMBER);
            $url = $this->forummanager->addTopic($_POST['topic_subject'], $_POST['post_content']);
            $this->redirect("forum/" . $this->forummanager->getActualCategory() . "/" . $url);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('forum/new-topic');
        }
    }
}