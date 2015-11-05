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
    }

    /**
     * @return int Vrátí ID kategorie
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string Vrátí název kategorie
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string Vrátí URL adresu kategorie
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string Vrátí popis kategorie
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int Vrátí ID rodičovské kategorie.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int Vrátí 1, pokud má potomky, jinak 0.
     */
    public function getHasSubCat()
    {
        return $this->hasSubCat;
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