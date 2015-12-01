<?php

namespace app\controller\admin;


use app\model\factory\UserFactory;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;

/**
 * Class AdminUserManagerController
 * @Inject UserFactory
 * @package app\controller\admin
 */
class AdminUserManagerController extends AdminBaseController {

    /**
     * @var UserFactory
     */
    private $userfactory;

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {

        $page = (isset($_GET['page']) ? $_GET['page'] : 1);

        $userCount = $this->userfactory->getUserCount();

        $pg = new BootPagination();
        $pg->pagenumber = $page;
        $pg->pagesize = ADMIN_USER_MANAGER_USER_COUNT;
        $pg->totalrecords = $userCount;
        $pg->paginationstyle = 1;
        $pg->showfirst = true;
        $pg->showlast = true;
        $pg->defaultUrl = "admin-user-manager";
        $pg->paginationUrl = "admin-user-manager?page=[p]";

        $this->data['paginator'] = $pg;
        try {
            $this->data['users'] = $this->userfactory->getXUsers($page, ADMIN_USER_MANAGER_USER_COUNT);
        } catch (MyException $ex) {
            $this->data['users'] = null;
        }

        $this->header['title'] = "Správce uživatelů";
        $this->view = 'users';
    }
}