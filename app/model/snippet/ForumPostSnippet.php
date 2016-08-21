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

class ForumPostSnippet extends ASnippet {

    private $postId;
    private $userId;
    private $userAvatar;
    private $userNick;
    private $postContent;
    private $postDate;

    /**
     * ForumPostSnippet constructor
     * @param $data
     */
    public function __construct ($data) {
        $this->postId = $data['post_id'];
        $this->userId = $data['user_id'];
        $this->userAvatar = $data['user_avatar'];
        $this->userNick = $data['user_nick'];
        $this->postContent = $data['post_content'];
        $this->postDate = $data['post_date'];
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
                        ->setSource("uploads/image/avatar/$this->userAvatar.png")
                        ->setAlt($this->userNick)
                        ->addClass("img-responsive img-circle center-block user-image"),
                    (new ParagraphElement($this->userNick))->addClass("nodecoration")
                ]))->setLocation("profile/$this->userId")
            ]))->addClass("col-xs-3 col-sm-2 col-md-1 author"),
            (new DivElement(
                MyParsedown::instance()->text($this->postContent)
            ))->addClass("col-xs-9 col-md-10 col-lg-11")
              ->addStyle(new StyleValue("min-height", "70px"))
              ->setEscape(false),
            (new SpanElement(DateUtils::prettyDateTime($this->postDate)))
                ->addClass("small right")
        ]))->setID("post-$this->postId")
           ->addClass("row list-item");

        $this->html = $row->render();
    }
}