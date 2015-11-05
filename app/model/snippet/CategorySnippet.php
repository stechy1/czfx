<?php

namespace app\model\snippet;


use app\model\html\element\AnchorElement;
use app\model\html\element\DivElement;
use app\model\html\element\HeadingElement;
use app\model\html\element\ImageElement;
use app\model\html\element\ParagraphElement;
use app\model\html\element\SpanElement;
use app\model\html\HtmlBuilder;
use app\model\html\StyleValue;

class CategorySnippet extends ASnippet {

    private $hasSubcats;
    private $URL;
    private $name;
    private $img;
    private $description;

    /**
     * CategorySnippet constructor.
     * @param $data
     */
    public function __construct($data) {
        $this->hasSubcats = $data['category_has_subcats'];
        $this->URL = $data['category_url'];
        $this->name = $data['category_name'];
        //$this->img = $data['category_image'];
        $this->description = $data['category_description'];
    }

    /**
     * Build the snippet.
     * @return ISnippet
     */
    public function build() {
        $tmp = (($this->hasSubcats) ? 'categories' : 'articles') . '/' . $this->URL;
        $panel = (new DivElement(array(
            (new DivElement(
                (new HeadingElement(HeadingElement::H4,
                    (new AnchorElement(
                        $this->name
                    ))->setLocation('/' . $tmp)
                ))
            ))->addClass('panel-heading'),
            (new DivElement(array(
                (new ImageElement())->setWidth(100)->setSource('img/design_patterns.png')->addClass('left')->addStyle(new StyleValue('margin-right', '5px')),
                (new ParagraphElement(
                    $this->description
                )))))->addClass('panel-body'),
            (new DivElement(
                (new SpanElement())->addClass('right')->addStyle(new StyleValue('margin-top', '-10px'))
            ))->addClass('panel-footer'))))->addClass(['panel', 'category']);
        $mainDiv = (new DivElement($panel))->addClass(['col-xs-12', 'col-sm-6', 'col-md-4']);

        $builder = new HtmlBuilder($mainDiv);

        $this->html = $builder->render();
    }
}