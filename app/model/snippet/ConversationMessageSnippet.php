<?php

namespace app\model\snippet;


use app\model\util\DateUtils;
use app\model\util\MyParsedown;
use stechy1\html\element\AnchorElement;
use stechy1\html\element\DivElement;
use stechy1\html\element\ImageElement;
use stechy1\html\element\ParagraphElement;
use stechy1\html\element\SpanElement;
use stechy1\html\StyleValue;

class ConversationMessageSnippet extends ASnippet {

    private $id;
    private $nick;
    private $avatar;
    private $content;
    private $date;

    /**
     * ConversationMessage constructor
     * @param $data
     */
    public function __construct ($data) {
        $this->id = $data['user_id'];
        $this->nick = $data['user_nick'];
        $this->avatar = $data['user_avatar'];
        $this->content = MyParsedown::instance()->text($data['message_content']);
        $this->date = DateUtils::prettyDateTime($data['message_time']);
    }

    /**
     * Sestaví snippet
     * @return ISnippet
     */
    public function build () {
        $row = (new DivElement([
            (new DivElement([
                (new AnchorElement([
                    (new ImageElement())
                        ->setSource("uploads/image/avatar/$this->avatar.png")
                        ->setAlt($this->nick)
                        ->addClass("img-responsive img-circle center-block user-image"),
                    (new ParagraphElement($this->nick))->addClass("nodecoration")
                ]))->setLocation("profile/$this->id")
            ]))->addClass("col-xs-3 col-sm-2 col-md-1 author"),
            (new DivElement($this->content))
                ->addClass("col-xs-9 col-md-10 col-lg-11")
                ->addStyle(new StyleValue("min-height", "70px"))
                ->setEscape(false),
            (new SpanElement($this->date))
                ->addClass("small right")
        ]))->addClass("col-xs-12 list-item");

        $this->html = $row->render();
    }
}