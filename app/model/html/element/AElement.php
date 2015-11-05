<?php

namespace app\model\html\element;


use app\model\html\StyleValue;
use app\model\html\NameValuePair;

abstract class AElement implements IElement, IDecorative {

    /**
     * @var string Obsah HTML kódu.
     */
    protected $html = '';
    /**
     * @var string  Značka HTML elementu.
     */
    protected $sign;
    /**
     * @var string ID HTML elementu.
     */
    protected $id;
    /**
     * @var string[] Pole CSS tříd.
     */
    protected $classArray = array();
    /**
     * @var StyleValue[] Pole StyleValue objektů představující styly.
     */
    protected $styleArray = array();
    /**
     * @var NameValuePair[] Pole NameValuePair objektů představující jednotlivé attributy.
     */
    protected $attributeArray = array();
    /**
     * @var string[]|AElement[] Pole řetězců, nebo AElement objektů představující obsah elementu
     */
    protected $contentArray = array();
    /**
     * @var bool True, pokud je HTML element párový, jinak false.
     */
    protected $pair;

    /**
     * AElement constructor.
     * @param $sign string HTML značka popisující HTML element.
     * @param AElement|array|string $content
     */
    public function __construct($sign, $content = null) {
        $this->sign = $sign;
        if(is_array($content))
            $this->content = $content;
        elseif($content != null && ($content instanceof AElement || is_string($content)))
            $this->content[] = $content;
        $this->pair = true;

        return $this;
    }

    /**
     * Nastaví elementu ID.
     * @param $id string Název IDcka.
     * @return $this
     */
    public function setID($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Odebere elementu ID.
     */
    public function removeID() {
        $this->id = null;
    }

    /**
     * Přidá elementu třídu stylu.
     * @param $class string|array Název třídy.
     * @return $this Vrátí sám sebe.
     */
    public function addClass($class) {
        if(is_array($class))
            $this->classArray = array_merge($this->classArray, $class);
        else
            $this->classArray[] = $class;

        return $this;
    }

    /**
     * Odebere třídu z atributů.
     * @param $class string Název třídy
     */
    public function removeClass($class) {
        $key = array_search($class, $this->classArray);
        if($key)
            unset($this->classArray[$key]);
        //array_$this->classArray
    }

    /**
     * Přidá danému elementu styl.
     * @param $style StyleValue|array Nový styl.
     * @return $this Vrátí sám sebe.
     */
    public function addStyle($style) {
        if(is_array($style))
            $this->styleArray = array_merge($this->styleArray, $style);
        else
            $this->styleArray[] = $style;

        return $this;
    }

    /**
     * Přidá danému elementu atribut.
     * @param $attribute NameValuePair|string|array Nový atribut.
     * @return $this Vrátí sám sebe.
     */
    public function addAttribute($attribute) {
        if(is_array($attribute))
            $this->attributeArray = array_merge($this->attributeArray, $attribute);
        else
            $this->attributeArray[] = $attribute;

        return $this;
    }

    /**
     * Nastaví obsah.
     * @param $content AElement|string Obsah elementu.
     * @return $this
     */
    public function addContent($content) {
        $this->content[] = $content;

        return $this;
    }

    /**
     * Sestavý validní HTML kód.
     */
    public function build() {
        /*$this->html .= '<' . $this->sign . ' ';
        if($this->id != null)
            $this->html .= 'id="' . htmlspecialchars($this->id) . '" ';
        if(!empty($this->classArray))
            $this->html .= 'class="' . (htmlspecialchars(join(' ', $this->classArray))) . '" ';
        if(!empty($this->styleArray))
            $this->html .= 'style="' . htmlspecialchars(join(' ', $this->styleArray)) . '" ';
        if(!empty($this->attributeArray))
            $this->html .= join(' ', $this->attributeArray);
        if($this->pair)
            $this->html .= '>';
        else
            $this->html .= '/>';*/
        $this->openTag();
        /*if (!empty($this->content))
            foreach ($this->content as $content) {
                if ($content instanceof AElement)
                    $this->html .= $content->render();
                elseif ($content != null)
                    $this->html .= htmlspecialchars($content);
            }*/
        $this->beforeWriteContent();
        $this->writeContent();
        $this->afterWriteContent();
        /*if($this->pair)
            $this->html .= '</' . $this->sign . '>';*/
        $this->closeTag();

        return $this;
    }

    /**
     * Otevře značku a zapíše k ní všechny atributy.
     */
    protected function openTag() {
        $this->html .= '<' . $this->sign . ' ';
        if($this->id != null)
            $this->html .= 'id="' . htmlspecialchars($this->id) . '" ';
        if(!empty($this->classArray))
            $this->html .= 'class="' . (htmlspecialchars(join(' ', $this->classArray))) . '" ';
        if(!empty($this->styleArray))
            $this->html .= 'style="' . htmlspecialchars(join(' ', $this->styleArray)) . '" ';
        if(!empty($this->attributeArray))
            $this->html .= join(' ', $this->attributeArray);
        if($this->pair)
            $this->html .= '>';
        else
            $this->html .= '/>';
    }

    /**
     * Metoda je zavolána před začátkem sestavování obsahu elementu.
     */
    protected function beforeWriteContent() {

    }

    /**
     * Metoda, která se stará o samotné sestavení obsahu elementu.
     */
    protected function writeContent() {
        if (!empty($this->content))
            foreach ($this->content as $content) {
                if ($content instanceof AElement)
                    $this->html .= $content->render();
                elseif ($content != null)
                    $this->html .= htmlspecialchars($content);
            }
    }

    /**
     * Metoda je zavolána po sestavení obsahu elementu.
     */
    protected function afterWriteContent() {

    }

    protected function closeTag() {
        if($this->pair)
            $this->html .= '</' . $this->sign . '>';
    }
    /**
     * @return string Vrátí validní HTML kód, pokud není sestavený, tak ho sestaví.
     */
    public function render() {
        if($this->html == null)
            $this->build();

        return $this->html;
    }

    /**
     * @return boolean
     */
    public function isPair() {
        return $this->pair;
    }

    function __toString() {
        return $this->render();
    }
}