<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\manager\ForumManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\PostSnippet;

/**
 * Class IndexController
 * @Inject ArticleFactory
 * @Inject ForumManager
 * @package app\controller
 */
class IndexController extends BaseController {

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
            $articles = $this->articlefactory->getLastXArticles(INDEX_ARTICLE_COUNT);
            $posts = $this->forummanager->getLastPosts(INDEX_POST_COUNT);
        } catch (MyException $ex) {}

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
            $posts = $this->forummanager->getLastPosts(INDEX_POST_COUNT, false);

            $i = 0;
            foreach ($posts as $post) {
                $snippet = new PostSnippet($post);
                $this->callBack->addData(new CallBackData($i++, $snippet->render()), false);
            }
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
        }
    }
}