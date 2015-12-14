<?php

namespace app\model;


class ForumCategory {

    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $description;

    /**
     * ForumCategory constructor.
     * @param int $id
     * @param string $name
     * @param string $url
     * @param string $description
     */
    public function __construct ($id = null, $name = null, $url = null, $description = null) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getId () {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId ($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName ($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getUrl () {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl ($url) {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getDescription () {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription ($description) {
        $this->description = $description;
    }

    public function toArray() {
        return array(
            'category_name' => $this->name,
            'category_url' => $this->url,
            'category_description' => $this->description
        );
    }
}