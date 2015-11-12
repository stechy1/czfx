<?php

namespace app\model\snippet;


use app\model\html\element\AnchorElement;
use app\model\html\element\DivElement;
use app\model\html\element\ParagraphElement;
use app\model\html\element\SpanElement;
use app\model\html\HtmlBuilder;
use app\model\html\NameValuePair;

class ReportSnippet extends ASnippet {

    private static $repTypes = array("warning", "info", "danger", "success");

    private $id;
    private $userID;
    private $userNick;
    private $reportType;
    private $message;
    private $read;
    private $date;

    /**
     * ReportSnippet constructor.
     * @param $data array
     */
    public function __construct ($data) {
        $this->id = $data['report_id'];
        $this->userID = $data['report_by'];
        $this->userNick = $data['user_nick'];
        $this->reportType = self::$repTypes[$data['report_type']];
        $this->message = $data['report_message'];
        $this->read = $data['report_read'];
        $this->date = $data['report_date'];
    }


    /**
     * Sestaví snippet
     * @return ISnippet
     */
    public function build () {
        $footer = null;
        if (!$this->read)
            $footer = (new DivElement(
                (new AnchorElement("Označit za přečteno"))->addClass("btn btn-xs btn-primary mark-as-read")->addAttribute(new NameValuePair("data-reportid", $this->id))
            ))->addClass("panel-footer");
        $panel = (new DivElement([
            (new DivElement([
                (new SpanElement())->addClass("glyphicon glyphicon-remove report-delete")->addAttribute(new NameValuePair("data-reportid", $this->id)),
                (new SpanElement($this->userNick))
            ]))->addClass("panel-heading"),
            (new DivElement(
                (new ParagraphElement($this->message))
            ))->addClass("panel-body"),
            $footer
        ]))->addClass(["panel read", "panel-$this->reportType"]);

        $div = (new DivElement($panel))->addClass("col-sm-6 col-md-4 col-lg-3")->setID("report-$this->id");

        $htmlBuilder = new HtmlBuilder($div);
        $this->html = $htmlBuilder->render();
    }
}