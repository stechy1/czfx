<?php

namespace app\controller;


use app\model\Article;
use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\manager\ArticleManager;
use app\model\manager\FileManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;

/**
 * Class ArticleManagementController
 * @Inject ArticleManager
 * @Inject ArticleFactory
 * @Inject CategoryFactory
 * @Inject FileManager
 * @package app\controller
 */
class ArticleManagerController extends BaseController {

    const
        CONTROLLER = "article-manager";

    /**
     * @var ArticleManager
     */
    private $articlemanager;
    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var CategoryFactory
     */
    private $categoryfactory;
    /**
     * @var FileManager
     */
    private $filemanager;

    private $blankArticle = [
        "article_title" => "",
        "article_description" => "",
        "article_tags" => "",
        "article_text" => "Toto je obsah editoru"];

    /**
     * Provede se před hlavním zpracováním požadavku v kontroleru
     */
    public function onStartup () {
        $this->validateUser(USER_ROLE_REDACTOR);
    }

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $this->header['title'] = 'Správce článků';
        $this->view = 'article-manager';

        try {
            $page = (isset($_GET['page']) ? $_GET['page'] : 1);

            $artCount = $this->articlemanager->getArticleCountFromCurrentUser();

            $pg = new BootPagination();
            $pg->pagenumber = $page;
            $pg->pagesize = ARTICLE_MANAGEMENT_ARTICLE_COUNT;
            $pg->totalrecords = $artCount;
            $pg->paginationstyle = 1;
            $pg->showfirst = true;
            $pg->showlast = true;
            $pg->defaultUrl = self::CONTROLLER;
            $pg->paginationUrl = self::CONTROLLER . "?page=[p]";

            $this->data['articles'] = $this->articlefactory->getXArticlesFromCurrentUser($page, ARTICLE_MANAGEMENT_ARTICLE_COUNT);
            $this->data['paginator'] = $pg;

            $this->data['hasArticles'] = true;
        } catch (MyException $ex) {
            $this->data['hasArticles'] = false;
            $this->data['errorMessage'] = $ex->getMessage();
        }
    }

    public function newAction (IRequest $request) {
        Article::clearSession();

        $this->filemanager->createTmpDirectory();
        $this->header['title'] = "Nový článek";
        $this->view = 'article-editor';
        $this->data['article'] = $this->blankArticle;
        $this->data['artAction'] = 'new';
        $this->data['categories'] = $this->categoryfactory->getAll(true);
    }

    public function newPostAction (IRequest $request) {
        try {
            $this->articlemanager->add($this->articlefactory->getArticleFromPost($_POST));
            $this->redirect(self::CONTROLLER);
        } catch (MyException $ex) {
            $this->header['title'] = "Nový článek";
            $this->view = 'article-editor';
            $this->data['artAction'] = 'new';
            $this->data['article'] = $_POST;
            $this->data['categories'] = $this->categoryfactory->getAll(true);
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function editAction (IRequest $request) {
        $this->view = 'article-editor';
        $this->data['artAction'] = 'edit';
        $article = null;
        try {
            $article = $this->articlefactory->getArticleFromURL($request->getParams()[1]);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect(self::CONTROLLER);
        }
        $article->storeToSession();

        $articleCategory = $this->categoryfactory->getCategoryFromArticle($article);
        try {
            $articlePrev = $this->articlefactory->getArticleFromID($article->getPreviousID());
            $articleNext = $this->articlefactory->getArticleFromID($article->getNextID());
        } catch (MyException $ex) {
            $articlePrev = $articleNext = null;
        }

        $this->data['article'] = $article->toArray();
        $this->data['articleCategory'] = $articleCategory;
        $this->data['articlePrev'] = $articlePrev;
        $this->data['articleNext'] = $articleNext;
        $this->data['categories'] = $this->categoryfactory->getWithout($articleCategory->getId(), true);

        $this->header['title'] = "úprava článku - " . $article->getTitle();
    }

    public function editPostAction (IRequest $request) {
        try {
            $article = $this->articlefactory->getArticleFromPost($request->getPost());
            $this->articlemanager->update($article);
            $this->redirect(self::CONTROLLER);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect(self::CONTROLLER . '/edit/' . $request->getParams()[1]);
        }
    }

    public function getAjaxAction (IRequest $request) {
        if (!$request->hasParams()) {
            $this->callBack->setFail();
            return;
        }

        $param = $request->getParams()[1];
        switch ($param) {
            case 'article-content':
                try {
                    $article = $this->articlefactory->getArticleFromSession();
                    $this->callBack->addData(new CallBackData('article', $article->getText()));
                } catch (MyException $ex) {
                    $this->callBack->setFail();
                    $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
                }
                break;
            case 'articles':
                $catID = intval($request->getParams()[2]);
                try {
                    $articles = $this->articlefactory->getArticlesFromCategoryID($catID);
                    $this->callBack->addData(new CallBackData('articles', $articles));
                } catch (MyException $ex) {
                    $this->callBack->setFail();
                    $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::INFO));
                }
                break;
            default:
                $this->callBack->setFail();
                $this->callBack->addMessage(new CallBackMessage('Nebyla zvolena žádná akce', CallBackMessage::INFO));
                break;
        }
    }
}