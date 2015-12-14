<?php

namespace app\model\snippet\form;


use stechy1\html\element\form\control\input\html5\EmailInput;
use stechy1\html\element\form\FormElement;
use stechy1\html\element\form\rule\RequiredRule;

class NewPasswordRequestForm extends FormElement {


    /**
     * NewPasswordRequestForm constructor.
     */
    public function __construct () {
        parent::__construct("new-password-request-form");

        $this->addContent([
            'email' => (new EmailInput("email"))
                ->setPlaceholder('E-mail')
                ->addClass('form-control')
                ->addRule(new RequiredRule())
        ]);
    }
}