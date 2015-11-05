<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\manager\ForumManager;
use app\model\service\request\IRequest;
use app\model\UserRole;
use Exception;

/**
 * Class ForumController
 * @Inject ForumManager
 * @package app\controller
 */
class ForumController extends BaseController {

    /**
     * @var ForumManager
     */
    private $forummanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        /*if ($request->hasParams()) {
            $params = $request->getParams();
            $category = array_shift($params);
            if ($params) {
                $topic = array_shift($params);
                try {
                    $this->data['posts'] = $this->forummanager->getPosts($topic);

                    $topic = $this->forummanager->getTopic($topic);
                    $category = $this->forummanager->getCategory($category);

                    $this->data['categoryName'] = $category['category_name'];
                    $this->data['categoryURL'] = $category['category_url'];
                    $this->data['topicSubject'] = $topic['topic_subject'];

                    $this->header['title'] = "Forum / " . $category['category_name'] . " / " . $topic['topic_subject'];
                    $this->view = 'forum-posts';
                } catch (Exception $ex) {
                    $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                    $this->redirect('forum');
                }
            } else {
                try {
                    $this->view = 'forum-topics';

                    $category = $this->forummanager->getCategory($category);
                    $this->header['title'] = "Forum / " . $category['category_name'];
                    $this->data['categoryName'] = $category['category_name'];
                    $this->data['categoryUrl'] = $category['category_url'];

                    $topics = $this->forummanager->getTopics($category['category_url']);
                    $this->data['topics'] = $topics;

                } catch (Exception $ex) {
                    $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
                }
            }
        } else {*/
            $this->header['title'] = 'Forum';
            $this->view = 'forum-categories';

            try {
                $this->data['forumCategories'] = $this->forummanager->getCategories();
            } catch (Exception $ex) {
                $this->data['forumCategories'] = null;
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            }
        //}
    }

    public function showTopicsAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->addMessage(new CallBackMessage("Nebyla zadána žádná kategorie", CallBackMessage::INFO));
            $this->redirect('forum');
        }

        $category = (isset($request->getParams()[1]))? $request->getParams()[1] : "-1";
        try {
            $this->view = 'forum-topics';

            $category = $this->forummanager->getCategory($category);
            $this->header['title'] = "Forum / " . $category['category_name'];
            $this->data['categoryName'] = $category['category_name'];
            $this->data['categoryUrl'] = $category['category_url'];

            $topics = $this->forummanager->getTopics($category['category_url']);
            $this->data['topics'] = $topics;

        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('forum');
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

            $topic = $this->forummanager->getTopic($topic);
            $category = $this->forummanager->getCategory($category);

            $this->data['categoryName'] = $category['category_name'];
            $this->data['categoryURL'] = $category['category_url'];
            $this->data['topicSubject'] = $topic['topic_subject'];

            $this->header['title'] = "Forum / " . $category['category_name'] . " / " . $topic['topic_subject'];
            $this->view = 'forum-posts';
        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
            $this->redirect('forum');
        }
    }

    public function showPostsPostAction (IRequest $request) {
        try {
            $this->validateUser(UserRole::MEMBER);
            $this->forummanager->addPost($request->getPost('post_content'));
            $this->addMessage(new CallBackMessage("Zpráva byla úspěšně odeslána"));
        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
        }

        $this->showPostsAction($request);
    }

    public function newTopicAction (IRequest $request) {
        $this->header['title'] = "Forum / Nové vlákno";
        $this->view = 'forum-new-topic';
    }

    public function newTopicPostAction (IRequest $request) {
        try {
            $this->validateUser(UserRole::MEMBER);
            $url = $this->forummanager->addTopic($_POST['topic_subject'], $_POST['post_content']);
            $this->redirect("forum/" . $this->forummanager->getActualCategory() . "/" . $url);
        } catch (Exception $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('forum/new-topic');
        }
    }
}