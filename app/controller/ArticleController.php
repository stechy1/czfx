<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\factory\UserFactory;
use app\model\manager\ArticleManager;
use app\model\service\request\IRequest;
use Exception;

/**
 * Class ArticleController
 * @Inject ArticleFactory
 * @Inject CategoryFactory
 * @Inject UserFactory
 * @package app\controller
 */
class ArticleController extends BaseController {

    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var CategoryFactory
     */
    private $categoryfactory;
    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            $artURL = $request->getParams()[0];

            try {
                $article = $this->articlefactory->getArticleFromURL($artURL);
                try {
                    $previousArticle = $this->articlefactory->getArticleFromID($article->getPreviousID())->toArray();
                } catch (Exception $ex) {
                    $previousArticle = null;
                }
                try {
                    $nextArticle = $this->articlefactory->getArticleFromID($article->getNextID())->toArray();
                } catch (Exception $ex) {
                    $nextArticle = null;
                }
                $this->header['title'] = $article->getTitle();
                $this->header['description'] = $article->getDescription();
                $this->header['key_words'] = $article->getTags();

                $this->data['article'] = $article->toArray();
                $this->data['text'] = $article->getText();
                $this->data['previousArticle'] = $previousArticle;
                $this->data['nextArticle'] = $nextArticle;
                $this->data['category'] = $this->categoryfactory->getCategoryFromArticle($article)->toArray();
                $this->data['user'] = $this->userfactory->getUserByID($article->getAuthor())->toArray();
            } catch (Exception $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect("error");
            }
        }

        $this->view = 'article';
    }


}