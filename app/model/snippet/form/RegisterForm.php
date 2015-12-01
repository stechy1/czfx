<?php

namespace app\model\snippet\form;


use stechy1\html\element\form\control\input\html5\EmailInput;
use stechy1\html\element\form\control\input\PasswordInput;
use stechy1\html\element\form\control\input\TextInput;
use stechy1\html\element\form\FormElement;
use stechy1\html\element\form\rule\RequiredRule;

class RegisterForm extends FormElement {

    /**
     * RegisterForm constructor.
     */
    public function __construct () {
        parent::__construct('register-form');

        $this->addContent([
            'username' => (new TextInput('username'))
                ->addClass('form-control')
                ->setPlaceholder('Uživatelské jméno')
                ->addRule(new RequiredRule()),
            'email' => (new EmailInput('email'))
                ->addClass('form-control')
                ->setPlaceholder('E-mail')
                ->addRule(new RequiredRule()),
            'password' => (new PasswordInput('password'))
                ->addClass('form-control')
                ->setPlaceholder('Heslo')
                ->hideValue()
                ->addRule(new RequiredRule()),
            'password2' => (new PasswordInput('password2'))
                ->addClass('form-control')
                ->setPlaceholder('Heslo znovu')
                ->hideValue()
                ->addRule(new RequiredRule())
        ]);
    }
}