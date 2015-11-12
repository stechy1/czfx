<?php

namespace app\controller\admin;


use app\model\callback\CallBackMessage;
use app\model\factory\ReportFactory;
use app\model\manager\SupportManager;
use app\model\service\exception\MyException;
use app\model\service\request\IRequest;
use app\model\util\BootPagination;

/**
 * Class AdminReportManagerController
 * @Inject ReportFactory
 * @Inject SupportManager
 * @package app\controller\admin
 */
class AdminReportManagerController extends AdminBaseController {

    /**
     * @var ReportFactory
     */
    private $reportfactory;
    /**
     * @var SupportManager
     */
    private $supportmanager;

    /**
     * @param IRequest $request
     */
    public function defaultAction (IRequest $request) {
        $page = (isset($_GET['page']) ? $_GET['page'] : 1);

        $repCount = $this->supportmanager->getCount();

        $pg = new BootPagination();
        $pg->pagenumber = $page;
        $pg->pagesize = ADMIN_REPORT_MANAGER_REPORT_COUNT;
        $pg->totalrecords = $repCount;
        $pg->paginationstyle = 1;
        $pg->showfirst = true;
        $pg->showlast = true;
        $pg->defaultUrl = "admin-report-manager";
        $pg->paginationUrl = "admin-report-manager?page=[p]";

        $this->data['paginator'] = $pg;
        try {
            $this->data['reports'] = $this->reportfactory->getXReports($page, ADMIN_REPORT_MANAGER_REPORT_COUNT);
        } catch (MyException $ex) {
            $this->data['reports'] = null;
        }

        $this->header['title'] = "Podpora";
        $this->view = "support";
    }

    public function markAsReadAjaxAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co zobrazit", CallBackMessage::WARNING));
            return;
        }

        $repID = $request->getParams()[1];

        try {
            $this->supportmanager->markAsRead($repID);
            $this->callBack->addMessage(new CallBackMessage("Zpáva byla označena jako přečtená"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }

    }

    public function deleteAjaxAction (IRequest $request) {
        if (!$request->hasParams() || !isset($request->getParams()[1])) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage("Není co odstranit", CallBackMessage::WARNING));
            return;
        }

        $repID = $request->getParams()[1];

        try {
            $this->supportmanager->delete($repID);
            $this->callBack->addMessage(new CallBackMessage("Zpráva byla odstraněna"));
        } catch (MyException $ex) {
            $this->callBack->setFail();
            $this->callBack->addMessage(new CallBackMessage($ex->getMessage(), CallBackMessage::DANGER));
        }
    }
}