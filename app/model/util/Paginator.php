<?php
/**
 * Created by PhpStorm.
 * User: Stechy1
 * Date: 6. 5. 2015
 * Time: 21:10
 */

namespace app\model\util;


class Paginator
{

    /**
     * @var int Poloměr oblasti kolem aktuální stránky.
     */
    private $radius;
    /**
     * @var int Počet záznamů na jednu stránku.
     */
    private $limit;
    /**
     * @var int Aktuální strana.
     */
    private $offset;
    /**
     * @var int Celkový počet stránek.
     */
    private $total;
    /**
     * @var string URL adresa pro přechod na další stránku.
     */
    private $url;

    /**
     * @var int Počet stránek vlevo.
     */
    private $left;
    /**
     * @var int Počet stránek vpravo.
     */
    private $right;

    /**
     * @var string výsledný HTML string
     */
    private $html;

    /**
     * Paginator constructor.
     * @param int $limit Počet záznamů na jednu stránku.
     * @param int $offset Aktuální stránka.
     * @param int $total Celkový počet stránek.
     * @param int $radius Počet stránek vlevo a vpravo.
     * @param string $url URL adresa pro přechod na další stránku.
     */
    public function __construct($limit, $offset, $total, $radius = 3, $url = '?strana={strana}')
    {
        $this->limit = $limit;
        settype($this->limit, "integer");
        $this->offset = $offset;
        settype($this->offset, "integer");
        $this->total = $total;
        settype($this->total, "integer");
        $this->radius = $radius;
        settype($this->radius, "integer");
        $this->url = $url;

        $this->left = ($this->offset - $this->radius) >= 1 ? ($this->offset - $this->radius) : 1;
        $this->right = ($this->offset + $this->radius) <= $this->limit ? ($this->offset + $this->radius) : $this->limit;
    }

    private function pageToURL($page)
    {
        return 'articles' . str_replace('{strana}', $page, $this->url);
    }

    /**
     * Vytvoří levou šipku s/bez odkazu na předchozí stránku.
     */
    private function leftArrow()
    {
        if ($this->offset > 1)
            //$this->html .= '<li><a href="' . $this->pageToURL($this->offset - 1) . '"></a></li>';
            $this->html .= '<li><a href="' . $this->pageToURL($this->offset - 1) . '"><span aria-hidden="true">&laquo;</span></a></li>';
        else
            $this->html .= '<li class="disabled"><span aria-hidden="true">&laquo;</span></li>';
    }

    /**
     * Vytvoří pravou šipku s/bez odkazu na další stránku.
     */
    private function rightArrow()
    {
        if ($this->offset < $this->total)
            //$this->html .= '<li><a href="' . $this->pageToURL($this->offset + 1) . '"></a></li>';
            $this->html .= '<li><a href="' . $this->pageToURL($this->offset + 1) . '"><span aria-hidden="true">&raquo;</span></a></li>';
        else
            $this->html .= '<li class="disabled"><span aria-hidden="true">&raquo;</span></li>';
    }


    /**
     * Vygeneruje stránky v radiusu.
     */
    private function generateRadius()
    {
        for ($i = $this->left; $i <= $this->right; $i++) {
            if ($i == $this->offset) {
                $this->html .= '<li class="active"><a href="#">' . $i . '<span class="sr-only"></span></a></li>';
            }
            else {
                $this->html .= '<li><a href="' . $this->pageToURL($i + 1) . '">' . ($i + 1) . '</a></li>';
            }
        }
    }

    /**
     * Sestaví paginátor.
     */
    public function build()
    {
        $this->leftArrow();
        if ($this->left > 1)
            $this->html .= '<li><a href="' . $this->pageToURL(1) . '">1</a></li>';

        if ($this->left > 2)
            $this->html .= '<li class="disabled"><span aria-hidden="true">&hellip;</span></li>';

        $this->generateRadius();

        if ($this->right < $this->total - 1)
            $this->html .= '<li class="disabled"><span aria-hidden="true">&hellip;</span></li>';

        if ($this->right < $this->total)
            $this->html .= '<li><a href="' . $this->pageToURL($this->total) . '">' . $this->total . '</a></li>';
        $this->rightArrow();


    }

    /**
     * @return string Vrátí kompletně sestavený paginátor.
     */
    public function render()
    {
        return $this->html;
    }
}