<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\factory\CategoryFactory;
use app\model\manager\CategoryManager;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;
use app\model\service\exception\MyException;

/**
 * Class AdminCategoryManagerController
 * @Inject CategoryFactory
 * @Inject CategoryManager
 * @package app\controller\admin
 */
class AdminCategoryManagerController extends AdminBaseController {

    /**
     * @var CategoryFactory
     */
    private $categoryfactory;
    /**
     * @var CategoryManager
     */
    private $categorymanager;

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {

        $page = (isset($_GET['page']) ? $_GET['page'] : 1);

        $catCount = $this->categorymanager->getCount();

        $pg = new BootPagination();
        $pg->pagenumber = $page;
        $pg->pagesize = ADMIN_CATEGORY_MANAGER_CATEGORY_COUNT;
        $pg->totalrecords = $catCount;
        $pg->paginationstyle = 1;
        $pg->showfirst = true;
        $pg->showlast = true;
        $pg->defaultUrl = "admin-article-manager";
        $pg->paginationUrl = "admin-article-manager?page=[p]";

        $this->data['paginator'] = $pg;
        try {
            $this->data['categories'] = $this->categoryfactory->getLastXCategoriesFromAll($page, ADMIN_CATEGORY_MANAGER_CATEGORY_COUNT);
        } catch (MyException $ex) {
            $this->data['categories'] = null;
        }

        $this->header['title'] = "Správce kategorií";
        $this->view = 'categories';
    }

    public function updateAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->addMessage(new CallBackMessage("Není co zobrazit", CallBackMessage::WARNING));
            $this->redirect('admin-category-manager');
        }

        $catID = $request->getParams()[1];
        $this->data['action'] = "update";

        try {
            $category = $this->categoryfactory->getCategoryFromID($catID);
            $this->data['category'] = $category->toArray();
            $this->data['categoryID'] = $category->getId();
            if ($category->getParent() != -1)
                $this->data['parent'] = $this->categoryfactory->getCategoryFromID($category->getParent())->toArray();
            $this->data['categories'] = $this->categoryfactory->getWithout($category->getParent(), true);
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('admin-category-manager');
        }

        $this->header['title'] = "Úprava kategorie";
        $this->view = 'category';
    }

    public function updatePostAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->addMessage(new CallBackMessage("Není co aktualizovat", CallBackMessage::WARNING));
            $this->redirect('admin-category-manager');
        }

        $catID = $request->getParams()[1];

        try {
            $category = $this->categoryfactory->getFromRawData($request->getPost());
            $category->setId($catID);
            $this->categorymanager->update($category);
            $this->addMessage(new CallBackMessage("Kategorie byla úspěšně aktualizována"));
            $this->redirect('admin-category-manager');
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
            $this->redirect('admin-category-manager/update/' . $catID);
        }
    }

    public function newAction (IRequest $request) {

        $blankCategory = array(
            "category_url" => $request->getPost('category-url', ""),
            "category_name" => $request->getPost('category-name', ""),
            "category_has_subcats" => $request->getPost('category-has-subcats', ""),
            "category_parent" => $request->getPost('category-parent', -1),
            "category_description" => $request->getPost('category-description', "")
        );

        $this->data['action'] = "new";
        $this->data['category'] = $blankCategory;
        $this->data['categoryID'] = "";
        $this->data['parent'] = null;
        $this->data['categories'] = $this->categoryfactory->getAll(true);
        $this->header['title'] = "Nová kategorie";
        $this->view = 'category';
    }

    public function newPostAction (IRequest $request) {
        try {
            $category = $this->categoryfactory->getFromRawData($request->getPost());
            $this->categorymanager->add($category);
            $this->addMessage(new CallBackMessage("Kategorie byla úspěšně vytvořena"));
            $this->redirect('admin-category-manager');
        } catch (MyException $ex) {
            $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::WARNING));
            $this->redirect('admin-category-manager/new');
        }
    }

    public function deleteAjaxAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co odstranit", CallBackMessage::WARNING));
            return;
        }

        $catID = $request->getParams()[1];

        try {
            $category = $this->categoryfactory->getCategoryFromID($catID);
            $this->categorymanager->delete($category);
            $this->callBack->addMessage(new CallBackMessage("Kategorie byla úspěšně odstraněna"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}