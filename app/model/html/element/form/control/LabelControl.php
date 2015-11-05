<?php

namespace app\model\html\element\form\control;


use app\model\html\NameValuePair;

class LabelControl extends AFormControl {

    const SIGN = 'label';

    /**
     * TextAreaControl constructor.
     * @param null $label Text v labelu.
     */
    public function __construct($label) {
        parent::__construct(self::SIGN, null);
        $this->addContent($label);

        return $this;
    }

    /**
     * Nastaví, pro jakou kontrolku má label být.
     * @param $controlID string ID kontrolky
     * @return $this Vrátí sám sebe.
     */
    public function setFor($controlID) {
        $this->addAttribute(new NameValuePair('for', $controlID));

        return $this;
    }

    /**
     *
     * @param $formID string ID formuláře
     * @return $this Vrátí sám sebe.
     */
    public function setForm($formID) {
        $this->addAttribute((new NameValuePair('form', $formID)));

        return $this;
    }
}