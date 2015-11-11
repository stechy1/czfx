<?php

namespace app\model;


class Article {
    private $id;
    private $categoryID;
    private $title;
    private $tags;
    private $description;
    private $url;
    private $date;
    private $previousID;
    private $nextID;
    private $author;
    private $text;

    /**
     * ArticleManager constructor.
     * @param $id int ID článku
     * @param $categoryID int ID kategorie
     * @param $title string Nadpis článku
     * @param $tags string Vyhledávací tagy pro článek
     * @param $description string Popis článku
     * @param $url string URL adresa pro daný článek
     * @param int $date Datum vytvoření článku
     * @param int $previousID ID předchozího článku
     * @param int $nextID ID následujícího článku
     * @param int $author ID autora článku
     * @param null $text Obsah článku
     */
    public function __construct($id = null, $categoryID  = null, $title  = null, $tags  = null, $description  = null, $url  = null, $date = null, $previousID = -1, $nextID = -1, $author = null, $text = null)
    {
        $this->id = $id;
        $this->categoryID = $categoryID;
        $this->title = $title;
        $this->tags = $tags;
        $this->description = $description;
        $this->url = $url;
        $this->date = ($date != null) ? $date : time();
        $this->previousID = $previousID;
        $this->nextID = $nextID;
        $this->author = $author;
        $this->text = $text;
    }

    /**
     * Vymaže z paměti rozpracovaný článek
     */
    public static function clearSession() {
        unset($_SESSION['storage']['article']);
    }

    /**
     * Uloží URL aktuálního článku do session pro pozdější načtení.
     */
    public function storeToSession() {
        $_SESSION['storage']['article'] = $this->url;
    }

    /**
     * Vrátí ID článku
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Vrátí ID kategorie
     *
     * @return int
     */
    public function getCategoryID()
    {
        return $this->categoryID;
    }

    /**
     * Vrátí titulek článku
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Vrátí vyhledávací tagy pro článek
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Vrátí popis článku
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Vrátí URL adresu článku
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Vrátí název souboru s obsahem článku
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->url;
    }

    /**
     * Vrátí ID předchozího článku nebo null
     *
     * @return int|null
     */
    public function getPreviousID()
    {
        return $this->previousID;
    }

    /**
     * Vrátí ID následujícího článku nebo null
     *
     * @return int|null
     */
    public function getNextID()
    {
        return $this->nextID;
    }

    /**
     *  Vrátí ID autora článku
     *
     * @return int|null
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Vrátí obsah článku
     *
     * @return null|string
     */
    public function getText() {
        return $this->text;
    }

    /**
     * Vrátí článek jako pole.
     */
    public function toArray() {
        return array(
            'article_category' => $this->categoryID,
            'article_title' => $this->title,
            'article_tags' => $this->tags,
            'article_description' => $this->description,
            'article_url' => $this->url,
            'article_date' => $this->date,
            'article_previous' => $this->previousID,
            'article_next' => $this->nextID,
            'article_author' => $this->author
        );
    }
}