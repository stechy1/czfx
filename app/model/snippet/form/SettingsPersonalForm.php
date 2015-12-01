<?php

namespace app\model\snippet\form;


use app\model\User;
use stechy1\html\element\form\control\input\html5\NumberInput;
use stechy1\html\element\form\control\input\PasswordInput;
use stechy1\html\element\form\control\input\TextInput;
use stechy1\html\element\form\FormElement;
use stechy1\html\element\form\rule\RequiredRule;

class SettingsPersonalForm extends FormElement {


    /**
     * SettingsPersonalForm constructor.
     * @param User $user
     */
    public function __construct ($user) {
        parent::__construct('personal-form');

        $this->addContent([
            'name' => (new TextInput('name'))
                ->setPlaceholder('Jméno')
                ->addClass('form-control input-md')
                ->setDefaultValue($user->getName()),
            'age' => (new NumberInput('age'))
                ->setPlaceholder('Věk')
                ->addClass('form-control input-md')
                ->setDefaultValue($user->getAge()),
            'motto' => (new TextInput('motto'))
                ->setPlaceholder('Motto')
                ->addClass('form-control input-md')
                ->setDefaultValue($user->getMotto()),
            'password' => (new PasswordInput('password'))
                ->setPlaceholder('Aktuální heslo')
                ->addClass('form-control input-md')
                ->addRule(new RequiredRule())
        ]);
    }
}