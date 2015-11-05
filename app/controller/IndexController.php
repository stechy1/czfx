<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\manager\ArticleManager;
use app\model\manager\ForumManager;
use app\model\service\request\IRequest;
use app\model\snippet\PostSnippet;
use Exception;

/**
 * Class IndexController
 * @Inject ArticleFactory
 * @Inject ForumManager
 * @package app\controller
 */
class IndexController extends BaseController {

    const
        ARTICLE_COUNT = 7,
        POST_COUNT = 10;

    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var ForumManager
     */
    private $forummanager;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    function defaultAction (IRequest $request) {
        $articles = $posts = null;
        try {
            $articles = $this->articlefactory->getLastXArticles(self::ARTICLE_COUNT);
            $posts = $this->forummanager->getLastPosts(self::POST_COUNT);
        } catch (Exception $ex) {}

        $this->data['articles'] = $articles;
        $this->data['posts'] = $posts;

        $this->header['title'] = "Index";
        $this->view = "index";
    }

    /**
     * Výchozí reakce kontroleru na ajaxový požadavek
     * @param IRequest $request
     */
    function defaultAjaxAction(IRequest $request) {
        try {
            $posts = $this->forummanager->getLastPosts(self::POST_COUNT, false);

            $i = 0;
            foreach ($posts as $post) {
                $snippet = new PostSnippet($post);
                $this->callBack->addData(new CallBackData($i++, $snippet->render()), false);
            }
        } catch (Exception $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
        }
    }
}