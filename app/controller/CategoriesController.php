<?php

namespace app\controller;


use app\model\factory\ArticleFactory;
use app\model\factory\CategoryFactory;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;

/**
 * Class CategoriesController
 * @Inject CategoryFactory
 * @Inject ArticleFactory
 * @package app\controller
 */
class CategoriesController extends BaseController {

    /**
     * @var CategoryFactory
     */
    private $categoryfactory;
    /**
     * @var ArticleFactory
     */
    private $articlefactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            $subCat = $request->getParams()[0];
            try {
                $this->data['categories'] = $this->categoryfactory->getSubcats($subCat);
                try {
                    $this->data['articles'] = $this->articlefactory->getArticlesFromCategoryURL($subCat);
                } catch (MyException $ex) {
                    $this->data['articles'] = null;
                }
                $this->header['title'] = 'Podkategorie';
                $this->view = 'categories';
            } catch (MyException $ex) {
                $this->redirect("articles/" . $subCat);
            }
        } else {
            $this->data['categories'] = $this->categoryfactory->getAll();
            $this->header['title'] = 'Kategorie';
            $this->view = 'categories';
        }
    }


}