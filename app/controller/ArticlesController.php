<?php

namespace app\controller;


use app\model\callback\CallBackMessage;
use app\model\factory\ArticleFactory;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class ArticlesController
 * @Inject ArticleFactory
 * @package app\controller
 */
class ArticlesController extends BaseController {

    /**
     * @var ArticleFactory
     */
    private $articlefactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if (!isset($request->getParams()[0]))
            $this->redirect('categories');

        $catURL = $request->getParams()[0];

        try {
            $this->data['articles'] = $this->articlefactory->getArticlesFromCategoryURL($catURL);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
            $this->redirect("categories");
        }

        $this->header['title'] = "Články";
        $this->view = 'articles';
    }


}