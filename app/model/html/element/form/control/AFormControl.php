<?php

namespace app\model\html\element\form\control;


use app\model\html\element\AElement;
use app\model\html\element\form\IControl;
use app\model\html\element\form\rule\ARule;
use app\model\html\NameValuePair;

abstract class AFormControl extends AElement implements IControl {

    /**
     * @var string Identifikační název kontrolky
     */
    protected $name;
    /**
     * @var mixed Hodnota v kontrolce.
     */
    protected $value;
    /**
     * @var LabelControl Popisek kontrolky.
     */
    protected $label;
    /**
     * @var ARule[] Pole obsahující validační pravidla.
     */
    protected $rules = array();
    /**
     * @var array Kolekce s chybami v kontrolce.
     */
    protected $errorArray = array();

    /**
     * @param string $sign Značka elementu
     * @param string $name Název kontrolky
     * @param null $label Popisek.
     */
    public function __construct($sign, $name, $label = null) {
        parent::__construct($sign);
        $this->name = $name;
        $this->setID($name);
        //$this->label = $label;
        if($label !== null) {
            $this->label = new LabelControl($label);
            $this->label->setFor($name);
            //$this->label->addClass('form-control');
        }


        return $this;
    }

    /**
     * Přidá validační pravidlo pro kontrolku.
     * @param ARule[]|ARule $rule Validační pravidlo.
     * @return $this Vrátí sám sebe.
     */
    public function addRule ($rule) {
        if(is_array($rule))
            $this->rules = array_merge($this->rules, $rule);
        else
            $this->rules[] = $rule;

        return $this;
    }

    /**
     * Sestavý validní HTML kód.
     */
    public function build() {
        if($this->label !== null && $this->label instanceof LabelControl)
            $this->html .= $this->label->render();
        if($this->name !== null)
            $this->addAttribute(new NameValuePair('name', $this->name));
        if(!empty($this->value))
            $this->addAttribute(new NameValuePair('value', $this->value));
        foreach($this->rules as $rule)
            $this->addAttribute($rule);
        parent::build();
        return $this;
    }

    /**
     * Nastaví hodnotu kontrolce
     * @param $value mixed Hodnota.
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Nastaví placeholder.
     * @param $placeholder string Obsah placeholderu.
     */
    public function setPlaceholder ($placeholder) {
        $this->addAttribute(new NameValuePair('placeholder', $placeholder));
    }

    /**
     * Vrátí hodnotu kontrolky.
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Zvaliduje kontrolku podle pravidel
     * @return boolean True, pokud je kontrolka validní.
     */
    public function isValid() {
        foreach($this->rules as $rule) {
            if(!$rule->validateRule($this->value)){
                $this->errorArray[] = $rule->getErrorMessage();
                return false;
            }
        }

        return true;
    }

    /**
     * Vrátí chyby které vznikly při validaci kontrolky.
     * @return array
     */
    public function getErrors() {
        return $this->errorArray;
    }

    /**
     * Vrátí identifikační název kontrolky.
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Vrátí referenci na label.
     * @return LabelControl
     */
    public function getLabel () {
        return $this->label;
    }

    /**
     * Nastaví label pro kontrolku.
     * @param LabelControl $label
     * @return $this Vrátí sám sebe.
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    //region Metody nastavující chování kontrolky.
    /**
     * Nastaví tooltip kontrolky.
     * @param $title string Text v tooltipu.
     * @return $this Vrátí sám sebe.
     */
    public function setTooltip ($title) {
        $this->addAttribute(new NameValuePair('title', $title));

        return $this;
    }
    //endregion

}