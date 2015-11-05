<?php

namespace app\model\html\element\form;


use app\model\html\element\form\control\input\HiddenInput;
use app\model\html\element\AElement;
use app\model\html\element\form\control\AFormControl;
use app\model\html\NameValuePair;

/**
 * Class FormElement Třída reprezentuje HTML formulář
 * @package html\element
 */
final class FormElement extends AElement {

    const SIGN = "form";

    const METHOD_GET = 'get';
    const METHOD_POST = 'post';

    /**
     * @var AFormControl[] Seznam všech kontrolek ve formuláři.
     */
    private $controls = array();
    /**
     * @var string Metoda odeslání formuláře.
     */
    private $method;
    /**
     * @var boolean True, pokud je formulář odeslaný, jinak false.
     */
    private $postBack;
    /**
     * @var boolean True, pokud je formulář validní, jinak false.
     */
    private $valid = null;
    /**
     * @var string[] Seznam všech validačních chyb.
     */
    private $errorArray = array();

    /**
     * FormElement constructor.
     * @param string $formName Název formuláře
     * @param string $method HTTP metoda, kterou se má formulář odeslat. Výchozí: post
     * @internal param null $content
     */
    public function __construct($formName, $method = self::METHOD_POST) {
        parent::__construct(self::SIGN);
        $this->setName($formName);
        $this->setMethod($method);

        $hidden = (new HiddenInput('form-name'))->setValue($formName);
        $this->addContent($hidden);

        $this->postBack = ($this->existKey('form-name') && $this->getData('form-name') == $formName);

        return $this;
    }

    /**
     * Metoda vydoluje z obsahu všechny formulářové kontrolky a přidá je do seznamu.
     * @param $control AElement[] Pole kontrolek.
     */
    private function addControl($control) {
        foreach($control as $c) {
            if($c instanceof AFormControl) {
                $name = $c->getName();
                if($this->existKey($name)) {
                    $value = $this->getData($name);
                    $c->setValue($value);
                }
                $this->controls[] = $c;
            }
            elseif($c instanceof AElement) {
                $this->addControl($c->content);
            }
        }
    }

    private function checkValidity() {
        $this->valid = true;

        foreach($this->controls as $control) {
            if(!$control->isValid()) {
                $this->errorArray[] = array_merge($this->errorArray, $control->getErrors());
            }
        }

        if(!empty($this->errorArray))
            $this->valid = false;
    }

    public function build() {
        $this->addAttribute(new NameValuePair('method', $this->method));
        return parent::build();
    }

    public function addContent($content) {
        parent::addContent($content);

        if($content instanceof AFormControl) {
            $name = $content->getName();
            if($this->existKey($name)) {
                $value = $this->getData($name);
                $content->setValue($value);
            }
            $this->controls[] = $content;
        }
        else
            $this->addControl($content->content);
    }

    /**
     * Funkce na kontrolu existence klíče
     * @param $key string Klíč reprezentující název kontrolky.
     * @return bool True, pokud klíč existuje, jinak false.
     */
    public function existKey($key) {
        return ($this->method == self::METHOD_POST) ? isset($_POST[$key]) : isset($_GET[$key]);
    }

    /**
     * Pokud je definován klíč, vrátí data podle klíče, jinak celé pole.
     * @param null $key Klíč
     * @return array|string Vrátí řetězec, nebo pole.
     */
    public function getData($key = null) {
        if ($key)
            return ($this->method == self::METHOD_POST) ? $_POST[$key] : $_GET[$key];
        return ($this->method == self::METHOD_POST) ? $_POST : $_GET;
    }

    /**
     * Metoda zjistí, jestli byl formulář odeslán.
     * @return boolean True, jestli byl formulář odeslán, jinak false.
     */
    public function isPostBack() {
        return $this->postBack;
    }

    /**
     * Metoda kontroluje validitu formuláře.
     * @return boolean True, pokud je formulář validní, jinak false.
     */
    public function isValid() {
        if($this->valid === null)
            $this->checkValidity();
        return $this->valid;
    }

    /**
     * Vrátí pole všech chyb vzniklích při validaci.
     * @return string[]
     */
    public function getErrors () {
        return $this->errorArray;
    }

    //region Metody, nastavující chování formuláře - nesouvisí s validací
    /**
     * Nastaví metodu odesílání dat z formuláře.
     * @param $method string Způsob odeslání dat z formuláře.
     * @return $this Vrátí sám sebe.
     */
    public function setMethod($method) {
        $this->method = $method;

        return $this;
    }

    /**
     * Definuje akci, která má být provedena, pokud je formulář odeslaný.
     * @param $action string Akce.
     * @return $this Vrátí sám sebe.
     */
    public function setAction($action) {
        $this->addAttribute(new NameValuePair('action', $action));

        return $this;
    }

    /**
     * Specifikuje znakovou sadu použitou při odeslání formuláře.
     * @param $charset string Znaková sada.
     * @return $this Vrátí sám sebe,
     */
    public function setAccentCharset ($charset) {
        $this->addAttribute(new NameValuePair('accept-charset', $charset));

        return $this;
    }

    /**
     * Specifikuje, zda-li má prohlížeč automaticky vyplnit prvek. Výchozí je true.
     * @param $autocomplete boolean False pro zakázání automatického vyplňování.
     * @return $this Vrátí sám sebe.
     */
    public function setAutocomplete($autocomplete) {
        $this->addAttribute(new NameValuePair('autocomplete', $autocomplete));

        return $this;
    }

    /**
     * Specifikuje kódování odeslaných dat. Výchozí je url-encoded
     * @param $enctype string Kódování odeslaných dat.
     * @return $this Vrátí sám sebe.
     */
    public function setEnctype($enctype) {
        $this->addAttribute(new NameValuePair('enctype', $enctype));

        return $this;
    }

    /**
     * Specifikuje název formuláře pro lepší identifikaci.
     * @param $name string Název formuláře.
     * @return $this Vrátí sám sebe.
     */
    public function setName($name) {
        $this->addAttribute(new NameValuePair('name', $name));

        return $this;
    }

    /**
     * Specifikuje, zda-li má prohlížeč validovat formulář.
     * @return $this Vrátí sám sebe.
     */
    public function setNoValidate() {
        $this->addAttribute('novalidate');

        return $this;
    }

    /**
     * Specifikuje akci atributu v cílu adresy.
     * @param $target string Cíl adresy. Výchozí: _self.
     * @return $this Vrátí sám sebe.
     */
    public function setTarget($target) {
        $this->addAttribute(new NameValuePair('target', $target));

        return $this;
    }
    //endregion

}