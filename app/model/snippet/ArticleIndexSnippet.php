<?php

namespace app\model\snippet;


use app\model\util\DateUtils;
use app\model\util\StringUtils;
use stechy1\html\element\AnchorElement;
use stechy1\html\element\DivElement;
use stechy1\html\element\ImageElement;
use stechy1\html\element\ParagraphElement;
use stechy1\html\element\SpanElement;
use stechy1\html\HtmlBuilder;
use stechy1\html\StyleValue;

class ArticleIndexSnippet extends ASnippet {

    private $title;
    private $description;
    private $url;
    private $date;

    /**
     * ArticleIndexSnippet constructor.
     * @param $data array
     */
    public function __construct ($data) {
        $this->title = StringUtils::shorten($data['article_title'], 30);
        $this->description = StringUtils::shorten($data['article_description'], 75);
        $this->url = $data['article_url'];
        $this->date = DateUtils::prettyDateTime($data['article_date']);
    }

    /**
     * Sestaví snippet
     * @return ISnippet
     */
    public function build () {
        $panel = (new DivElement([
            (new SpanElement())->addClass("minimalization"),
            (new DivElement(
                (new AnchorElement($this->title))->setLocation("article/$this->url")
            ))->addClass("panel-heading"),
            (new DivElement([
                (new ImageElement())->setSource("img/article-64.png")->setAlt("Article")->addClass("left"),
                (new ParagraphElement($this->description))
            ]))->addClass("panel-body"),
            (new DivElement(
                (new SpanElement($this->date))->addClass("right")->addStyle(new StyleValue("margin-top", "-10px"))
            ))->addClass("panel-footer")
        ]))->addClass("panel article");

        $container = (new DivElement($panel))->addClass("col-xs-12 col-sm-6 col-md-4 col-lg-3");

        $builder = new HtmlBuilder($container);
        $this->html = $builder->render();
    }
}