<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\manager\ArticleManager;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;
use app\model\service\exception\MyException;

/**
 * Class AdminArticleManagerController
 * @Inject ArticleFactory
 * @Inject ArticleManager
 * @package app\controller\admin
 */
class AdminArticleManagerController extends AdminBaseController {

    const
        ARTICLES_ON_PAGE = 10;

    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var ArticleManager
     */
    private $articlemanager;

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {

        $page = (isset($_GET['page']) ? $_GET['page'] : 1);

        $artCount = $this->articlemanager->getArticleCountFromAll();

        $pg = new BootPagination();
        $pg->pagenumber = $page;
        $pg->pagesize = self::ARTICLES_ON_PAGE;
        $pg->totalrecords = $artCount;
        $pg->paginationstyle = 1;
        $pg->showfirst = true;
        $pg->showlast = true;
        $pg->defaultUrl = "admin-article-manager";
        $pg->paginationUrl = "admin-article-manager?page=[p]";

        $this->data['paginator'] = $pg;
        try {
            $this->data['articles'] = $this->articlefactory->getXArticlesFromAll($page, self::ARTICLES_ON_PAGE);
        } catch (MyException $ex) {
            $this->data['articles'] = null;
        }

        $this->header['title'] = "Správce článků";
        $this->view = 'articles';
    }

    public function previewAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->addMessage(new CallBackMessage("Není co zobrazit", CallBackMessage::WARNING));
            $this->redirect('admin-article-manager');
        }

        $artID = $request->getParams()[1];

        try {
            $article = $this->articlefactory->getArticleFromID($artID);
            $this->data['article'] = $article->toArray();
            $this->data['text'] = $article->getText();
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('admin-article-manager');
        }

        $this->header['title'] = "Náhled článku";
        $this->view = 'article';
    }

    public function deleteAjaxAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co odebírat", CallBackMessage::WARNING));
            return;
        }

        $artID = $request->getParams()[1];

        try {
            $article = $this->articlefactory->getArticleFromID($artID);
            $this->articlemanager->delete($article);
            $this->callBack->addMessage(new CallBackMessage("Článek byl úspěšně odebrán"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

    public function validatePostAjaxAction (IRequest $request) {
        $artID = $request->getPost("id");
        $validated = $request->getPost("validated");

        if ($artID == null || $validated == null) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Nejsou vyplněny všechny údaje", CallBackMessage::WARNING));
            return;
        }

        try {
            $artID = intval($artID);
            $validated = filter_var($validated, FILTER_VALIDATE_BOOLEAN);
            $this->articlemanager->validate($artID, $validated);
            $this->callBack->addMessage(new CallBackMessage("Validace proběhla úspěšně"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }

}