<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\factory\UserFactory;
use app\model\manager\ArticleManager;
use app\model\manager\UserManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class ArticleController
 * @Inject ArticleFactory
 * @Inject ArticleManager
 * @Inject CategoryFactory
 * @Inject UserFactory
 * @Inject UserManager
 * @package app\controller
 */
class ArticleController extends BaseController {

    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var ArticleManager
     */
    private $articlemanager;
    /**
     * @var CategoryFactory
     */
    private $categoryfactory;
    /**
     * @var UserFactory
     */
    private $userfactory;
    /**
     * @var UserManager
     */
    private $usermanager;

    /**
     * Univerzálně přidá článek do oblíbených
     *
     * @param $articleURL
     * @throws MyException
     */
    private function addArticleToFavorite($articleURL) {
        $article = $this->articlefactory->getArticleFromURL($articleURL);
        $user = $this->userfactory->getUserFromSession();
        $this->articlemanager->addArticleToFavorite($article, $user);
    }

    /**
     * Univerzálně odebere článek z oblíbených
     *
     * @param $articleURL string
     * @throws MyException
     */
    private function deleteArticleFromFavorite($articleURL) {
        $article = $this->articlefactory->getArticleFromURL($articleURL);
        $user = $this->userfactory->getUserFromSession();
        $this->articlemanager->deleteFavoriteArticle($article, $user);
    }

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            $artURL = $request->getParams()[0];

            try {
                $article = $this->articlefactory->getArticleFromURL($artURL, ArticleFactory::ARTICLE_IS_VALIDATED);
                $this->data['isFavorite'] = false;
                if ($this->usermanager->isLoged()) {
                    $user = $this->userfactory->getUserFromSession();
                    $this->data['isFavorite'] = $this->articlemanager->isArticleFavourite($article, $user);
                }
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

    public function addtofavoriteAction (IRequest $request) {
        if (!$request->hasParams(1))
            $this->redirect("error");

        $params = $request->getParams();
        array_shift($params);
        $articleURL = array_shift($params);

        try {
            $this->addArticleToFavorite($articleURL);
            $this->addMessage(new CallBackMessage("Článek byl přidán do oblíbených"));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->redirect("article/$articleURL");
    }

    public function deletefromfavoriteAction (IRequest $request) {
        if (!$request->hasParams(1))
            $this->redirect("error");

        $params = $request->getParams();
        array_shift($params);
        $articleURL = array_shift($params);

        try {
            $this->deleteArticleFromFavorite($articleURL);
            $this->addMessage(new CallBackMessage("Článek byl odebrán z oblíbených", CallBackMessage::INFO));
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

        $this->redirect("article/$articleURL");
    }

    public function addtofavoriteAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $articleURL = array_shift($params);

        try {
            $this->addArticleToFavorite($articleURL);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function deletefromfavoriteAjaxAction (IRequest $request) {
        if (!$request->hasParams(1)) {
            $this->callBack->setFail();
            return;
        }

        $params = $request->getParams();
        array_shift($params);
        $articleURL = array_shift($params);

        try {
            $this->deleteArticleFromFavorite($articleURL);
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}