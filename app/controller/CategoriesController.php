<?php

namespace app\controller;


use app\model\factory\CategoryFactory;
use app\model\service\request\IRequest;
use Exception;

/**
 * Class CategoriesController
 * @Inject CategoryFactory
 * @package app\controller
 */
class CategoriesController extends BaseController {

    /**
     * @var CategoryFactory
     */
    private $categoryfactory;

    /**
     * Výchozí akce kontroleru
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        if ($request->hasParams()) {
            $subCat = $request->getParams()[0];
            try {
                $this->data['categories'] = $this->categoryfactory->getSubcats($subCat);
                $this->header['title'] = 'Podkategorie';
                $this->view = 'categories';
            } catch (Exception $ex) {
                $this->redirect("articles/" . $subCat);
            }
        } else {
            $this->data['categories'] = $this->categoryfactory->getAll();
            $this->header['title'] = 'Kategorie';
            $this->view = 'categories';
        }
    }


}