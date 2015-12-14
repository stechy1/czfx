<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\factory\ForumCategoryFactory;
use app\model\manager\ForumManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\snippet\form\ForumCategoryForm;

/**
 * Class ForumController
 * @Inject ForumManager
 * @Inject ForumCategoryFactory
 * @package app\controller\admin
 */
class AdminForumManagerController extends AdminBaseController {

    /**
     * @var ForumManager
     */
    private $forummanager;
    /**
     * @var ForumCategoryFactory
     */
    private $forumcategoryfactory;

    public function onStartup () {
        parent::onStartup();

        $this->data['action'] = "new";
    }

    public function defaultAction (IRequest $request) {
        $this->header['title'] = "Správce fóra";
        $this->view = "forum";

        $this->data['forumCategories'] = $this->forummanager->getCategories();
    }

    public function newAction (IRequest $request) {
        $this->header['title'] = "Nová kategorie ve foru";
        $this->view = "forum-category";

        $this->data['form'] = new ForumCategoryForm();
    }

    public function newPostAction (IRequest $request) {
        $form = new ForumCategoryForm();
        if ($form->isPostBack()) {
            if ($form->isValid()) {
                try {
                    $category = $this->forumcategoryfactory->getCategoryFromPost($form->getData());
                    $this->forummanager->addCategory($category);
                } catch (MyException $ex) {
                    $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
                }
            } else {
                $this->addMessage(new CallBackMessage("Formulář není validní", CallBackMessage::INFO));
            }
        }

        $this->newAction($request);
    }

    public function updateAction (IRequest $request) {
        if (!$request->hasParams())
            $this->redirect("admin-forum-manager");

        $this->header['title'] = "Upravit kategorii";
        $this->view = "forum-category";

        $id = $request->getParams()[1];
        $category = $this->forumcategoryfactory->getFromID($id);

        $this->data['form'] = new ForumCategoryForm($category);
        $this->data['action'] = "update";

    }

    public function updatePostAction (IRequest $request) {
        if (!$request->hasParams())
            $this->redirect("admin-forum-manager");

        $id = $request->getParams()[1];

        $form = new ForumCategoryForm();
        if ($form->isPostBack()) {
            if ($form->isValid()) {
                try {
                    $category = $this->forumcategoryfactory->getCategoryFromPost($form->getData());
                    $category->setId($id);
                    $this->forummanager->updateCategory($category);
                } catch (MyException $ex) {
                    $this->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
                }
            } else {
                $this->addMessage(new CallBackMessage("Formulář není validní", CallBackMessage::INFO));
            }
        }

        $this->redirect("admin-forum-manager");
    }
}