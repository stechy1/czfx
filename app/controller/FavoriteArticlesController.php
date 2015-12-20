<?php

namespace app\controller;


use app\model\factory\ArticleFactory;
use app\model\manager\ArticleManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;

/**
 * Class FavoriteArticlesController
 * @Inject ArticleFactory
 * @Inject ArticleManager
 * @package app\controller
 */
class FavoriteArticlesController extends BaseController {

    const
        CONTROLLER = "favorite-articles";

    /**
     * @var ArticleFactory
     */
    private $articlefactory;
    /**
     * @var ArticleManager
     */
    private $articlemanager;

    public function onStartup () {
        parent::onStartup();

        $this->validateUser(USER_ROLE_MEMBER);
    }


    public function defaultAction (IRequest $request) {
        $this->header['title'] = 'Oblíbené články';
        $this->view = 'favorite-articles';

        try {
            $page = (isset($_GET['page']) ? $_GET['page'] : 1);

            $artCount = $this->articlemanager->getFavoriteArticleCount();

            $pg = new BootPagination();
            $pg->pagenumber = $page;
            $pg->pagesize = ARTICLE_MANAGEMENT_ARTICLE_COUNT;
            $pg->totalrecords = $artCount;
            $pg->paginationstyle = 1;
            $pg->showfirst = true;
            $pg->showlast = true;
            $pg->defaultUrl = self::CONTROLLER;
            $pg->paginationUrl = self::CONTROLLER . "?page=[p]";

            $this->data['articles'] = $this->articlefactory->getXFavoritesArticles($page, ARTICLE_MANAGEMENT_ARTICLE_COUNT);
            $this->data['paginator'] = $pg;

            $this->data['hasArticles'] = true;
        } catch (MyException $ex) {
            $this->data['hasArticles'] = false;
            $this->data['errorMessage'] = $ex->getMessage();
        }

    }


}