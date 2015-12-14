<?php

namespace app\model\snippet;



use stechy1\html\element\AnchorElement;
use stechy1\html\element\DivElement;
use stechy1\html\element\HeadingElement;
use stechy1\html\element\ImageElement;
use stechy1\html\element\ParagraphElement;
use stechy1\html\element\SpanElement;
use stechy1\html\HtmlBuilder;
use stechy1\html\StyleValue;

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
        $this->img = $data['category_image'];
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
            (new DivElement([
                (new ImageElement())
                    ->setWidth(100)
                    ->setSource("uploads/image/category/$this->img.png")
                    ->addClass('left img-rounded')
                    ->addStyle(new StyleValue('margin-right', '5px')),
                (new ParagraphElement(
                    $this->description))
                ]
            ))->addClass('panel-body'),
            (new DivElement(
                (new SpanElement())
                    ->addClass('right')
                    ->addStyle(new StyleValue('margin-top', '-10px'))
            ))->addClass('panel-footer'))))->addClass(['panel', 'category']);
        $mainDiv = (new DivElement($panel))->addClass(['col-xs-12', 'col-sm-6', 'col-md-4']);

        $builder = new HtmlBuilder($mainDiv);

        $this->html = $builder->render();
    }
}