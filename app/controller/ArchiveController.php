<?php

namespace app\controller;


use app\model\callback\CallBackData;
use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\ArticleIndexSnippet;

/**
 * Class ArchiveController
 * @Inject ArticleFactory
 * @package app\controller
 */
class ArchiveController extends BaseController {

    /**
     * @var ArticleFactory
     */
    private $articlefactory;

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {

        try {
            $articles = $this->articlefactory->getXArticlesFromAll(1, INDEX_ARTICLE_COUNT, ArticleFactory::ARTICLE_IS_VALIDATED);
        } catch (MyException $ex) {
            $articles = null;
        }

        $this->data['articles'] = $articles;
        $this->header['title'] = "Archiv článků";
        $this->view = 'archive';
    }

    public function getnextAjaxAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->callBack->addMessage(new CallBackMessage("Není co zobrazit", CallBackMessage::WARNING));
            $this->callBack->setFail();
            return;
        }

        $page = $request->getParams()[1];

        try {
            $articles = $this->articlefactory->getXArticlesFromAll($page, 3, ArticleFactory::ARTICLE_IS_VALIDATED);
            $i = 0;
            foreach ($articles as $article) {
                $this->callBack->addData(new CallBackData($i++, (new ArticleIndexSnippet($article))->render()), false);
            }
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

    }

}