<?php

namespace app\model\snippet\form;


use app\model\ForumCategory;
use stechy1\html\element\form\control\input\TextInput;
use stechy1\html\element\form\FormElement;
use stechy1\html\element\form\rule\RequiredRule;

class ForumCategoryForm extends FormElement {
    public function __construct (ForumCategory $category = null) {
        parent::__construct("new-forum-category");


        $name = (new TextInput("name"))
            ->addClass("form-control")
            ->setPlaceholder("Název nové kategorie")
            ->addRule(new RequiredRule());
        $url = (new TextInput("url"))
            ->addClass("form-control")
            ->setPlaceholder("Url adresa")
            ->addRule(new RequiredRule());
        $description = (new TextInput("description"))
            ->addClass("form-control")
            ->setPlaceholder("Krátký popis")
            ->addRule(new RequiredRule());

        if (func_num_args() != 0) {
            $name->setDefaultValue($category->getName());
            $url->setDefaultValue($category->getUrl());
            $description->setDefaultValue($category->getDescription());
        }

        $this->addContent([
            "name" => $name,
            "url" => $url,
            "description" => $description
        ]);
    }
}