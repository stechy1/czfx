<?php

namespace app\model;


class Category {

    private $id;
    private $name;
    private $url;
    private $description;
    private $parent;
    private $hasSubCat;

    /**
     * Category constructor.
     * @param $id int ID kategorie
     * @param $name string Název kategorie.
     * @param $url string URL kategorie.
     * @param $description string Ikonka kategorie.
     * @param int $parent ID rodičovské kategorie. -1, pokud nemá žádné rodiče.
     * @param int $hasSubCat 0, pokud nemá žádné potomky, jinak 1.
     */
    public function __construct($id = -1, $name = null, $url = null, $description = null, $parent = -1, $hasSubCat = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
        $this->parent = $parent;
        $this->hasSubCat = $hasSubCat;

        settype($this->id, "integer");
        settype($this->parent, "integer");
        settype($this->hasSubCat, "integer");
    }

    /**
     * Vrátí ID kategorie
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Nastaví nové ID kategorie
     * @param int $id
     */
    public function setId ($id) {
        $this->id = $id;
    }

    /**
     * Vrátí název kategorie
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Nastaví nový název kategorie
     *
     * @param null|string $name
     */
    public function setName ($name) {
        $this->name = $name;
    }

    /**
     * Vrátí URL adresu kategorie
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Nastaví novou url adresu kategorie
     *
     * @param null|string $url
     */
    public function setUrl ($url) {
        $this->url = $url;
    }

    /**
     * Vrátí popis kategorie
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Nastaví nový popis kategorie
     *
     * @param null|string $description
     */
    public function setDescription ($description) {
        $this->description = $description;
    }

    /**
     * Vrátí ID rodičovské kategorie
     *
     * @return int
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Nastaví novou rodičovskou kategorii
     *
     * @param int $parent
     */
    public function setParent ($parent) {
        $this->parent = $parent;
    }

    /**
     * Vrátí 1, pokud má potomky, jinak 0
     *
     * @return int
     */
    public function getHasSubCat()
    {
        return $this->hasSubCat;
    }

    /**
     * Nastaví, zda-li kategorie obsahuje podkategorie
     *
     * @param int $hasSubCat
     */
    public function setHasSubCat ($hasSubCat) {
        $this->hasSubCat = $hasSubCat;
    }

    public function toArray() {
        return array(
            'category_name' => $this->name,
            'category_url' => $this->url,
            'category_description' => $this->description,
            'category_parent' => $this->parent,
            'category_has_subcats' => $this->hasSubCat
        );
    }
}