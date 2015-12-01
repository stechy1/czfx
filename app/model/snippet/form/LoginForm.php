<?php

namespace app\model\snippet\form;


use stechy1\html\element\form\AFormFactory;
use stechy1\html\element\form\control\input\html5\EmailInput;
use stechy1\html\element\form\control\input\PasswordInput;
use stechy1\html\element\form\FormElement;
use stechy1\html\element\form\rule\RequiredRule;

class LoginForm extends FormElement {


    /**
     * LoginForm constructor.
     */
    public function __construct () {
        parent::__construct('login-form');

        $this->addContent([
            'email' => (new EmailInput('email'))
                ->setPlaceholder('E-mail')
                ->addClass('form-control')
                ->addRule(new RequiredRule()),
            'password' => (new PasswordInput('password'))
                ->setPlaceholder('Heslo')
                ->addClass('form-control')
                ->hideValue()
                ->addRule(new RequiredRule())
        ]);
    }
}