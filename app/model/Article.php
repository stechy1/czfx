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
        //$_SESSION['storage']['article'] = null;
        unset($_SESSION['storage']['article']);
    }

    /**
     * Uloží URL aktuálního článku do session pro pozdější načtení.
     */
    public function storeToSession() {
        $_SESSION['storage']['article'] = $this->url;
    }

    /**
     * @return int Vrátí ID článku.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int Vrátí ID kategorie.
     */
    public function getCategoryID()
    {
        return $this->categoryID;
    }

    /**
     * @return string Vrátí titulek článku.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string Vrátí vyhledávací tagy pro článek.
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return string Vrátí popis článku.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string Vrátí URL adresu článku.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string Vrátí název souboru s obsahem článku.
     */
    public function getFileName()
    {
        return $this->url;
    }

    /**
     * @return int|null Vrátí ID předchozího článku nebo null.
     */
    public function getPreviousID()
    {
        return $this->previousID;
    }

    /**
     * @return int|null Vrátí ID následujícího článku nebo null.
     */
    public function getNextID()
    {
        return $this->nextID;
    }

    /**
     * @return int|null Vrátí ID autora článku
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * @return null|string Vrátí obsah článku
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