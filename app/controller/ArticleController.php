<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\factory\UserFactory;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

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
                $article = $this->articlefactory->getArticleFromURL($artURL, ArticleFactory::ARTICLE_IS_VALIDATED);
                try {
                    $previousArticle = $this->articlefactory->getArticleFromID($article->getPreviousID(), ArticleFactory::ARTICLE_IS_VALIDATED)->toArray();
                } catch (MyException $ex) {
                    $previousArticle = null;
                }
                try {
                    $nextArticle = $this->articlefactory->getArticleFromID($article->getNextID(), ArticleFactory::ARTICLE_IS_VALIDATED)->toArray();
                } catch (MyException $ex) {
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
            } catch (MyException $ex) {
                $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
                $this->redirect("error");
            }
        }

        $this->view = 'article';
    }


}