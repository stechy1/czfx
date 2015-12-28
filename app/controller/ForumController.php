<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\UserFactory;
use app\model\manager\ForumManager;
use app\model\service\CaptchaService;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\ForumPostSnippet;

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

    /**
     * Zobrazí jednotlivá témata v zadané kategorii
     *
     * @param IRequest $request
     */
    public function showTopicsAction (IRequest $request) {
        $category = (isset($request->getParams()[1]))? $request->getParams()[1] : "-1";
        $this->view = 'forum-topics';

        try {
            $user = $this->userfactory->getUserFromSession();
            $user->getRole()->valid(USER_ROLE_ADMIN);
            $this->data['isAdmin'] = true;
        } catch (MyException $ex) {
            $this->data['isAdmin'] = false;
        }

        try {
            $category = $this->forummanager->getCategory($category);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('forum');
        }

        $this->header['title'] = "Forum / " . $category['category_name'];
        $this->data['categoryName'] = $category['category_name'];
        $this->data['categoryUrl'] = $category['category_url'];

        try {
            $topics = $this->forummanager->getTopics($category['category_url']);
        } catch (MyException $ex) {
            $topics = null;
        }


        $this->data['topics'] = $topics;

    }

    /**
     * Vymaže vybrené téma z kategorie
     *
     * @param IRequest $request
     */
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

    /**
     * Zobrazí příspěvky v kategorii
     *
     * @param IRequest $request
     */
    public function showPostsAction (IRequest $request) {
        if (!$request->hasParams(2)) {
            $this->addMessage(new CallBackMessage("Nedostatečný počet parametrů", CallBackMessage::INFO));
            $this->redirect('forum');
        }

        $params = $request->getParams();
        array_shift($params);

        $category = array_shift($params);
        $topic = array_shift($params);

        try {
            $this->data['posts'] = $this->forummanager->getPosts($topic);
            $this->data['topicHash'] = $this->forummanager->getTopicHash($topic);
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

    /**
     * Přidá příspěvek pomocí odeslaného formuláře bez ajaxu
     *
     * @param IRequest $request
     */
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

    /**
     * Přidá příspěvek pomocí odeslaného formuláře přes ajax a pošle update ostatním klientům
     *
     * @param IRequest $request
     */
    public function showPostsPostAjaxAction (IRequest $request) {
        try {
            $user = $this->userfactory->getUserFromSession();
            $user->getRole()->valid(USER_ROLE_MEMBER);
            CaptchaService::verify($request->getPost("g-recaptcha-response", null));
            $this->forummanager->addPost($request->getPost('post_content'));
            $postHash = $this->forummanager->getLastInsertedPostHash();
            $this->callBack->addMessage(new CallBackMessage("Zpráva byla úspěšně odeslána"));
            $this->callBack->addData(new CallBackData("post", $postHash));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    /**
     * Odstraní vybraný příspěvek
     *
     * @param IRequest $request
     */
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

    /**
     * Zobrazí stránku se založením nového tématu
     *
     * @param IRequest $request
     */
    public function newTopicAction (IRequest $request) {
        $this->header['title'] = "Forum / Nové vlákno";
        $this->view = 'forum-new-topic';
    }

    /**
     * Založí nové téma
     *
     * @param IRequest $request
     */
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