<?php

namespace app\model\manager;


use app\model\Category;
use app\model\database\Database;
use app\model\service\exception\MyException;
use app\model\util\SimpleImage;

/**
 * Class CategoryManager - Správce kategorii článků
 * @Inject Database
 * @Inject FileManager
 * @package app\model\manager
 */
class CategoryManager {

    const
        CATEGORY_IMAGE_SIZE = 100;

    /**
     * @var Database
     */
    private $database;
    /**
     * @var FileManager
     */
    private $filemanager;

    private $imageFolder;

    private function initImageFolder () {
        if (!empty($this->imageFolder))
            return;

        $this->imageFolder = $this->filemanager->getDirectory(FileManager::FOLDER_IMAGE) . "category/";
    }

    /**
     * Přidá novou kategorii do databáze
     *
     * @param Category $category
     * @param null $image
     * @throws MyException Pokud se nepodaří přidat novou kategorii
     */
    public function add (Category $category, $image = null) {
        $fromDb = $this->database->insert("categories", $category->toArray());

        if (!$fromDb)
            throw new MyException("Přidání nové kategorie se nepodařilo");

        if (empty($image))
            return;

        $this->initImageFolder();

        $img = new SimpleImage($image['tmp_name']);
        $img->square(self::CATEGORY_IMAGE_SIZE);
        $name = $category->getUrl();
        $img->save("$this->imageFolder$name.png", IMAGETYPE_PNG);

        $lastInsertId = $this->database->getLastId();
        $this->database->update("categories", ["category_image" => $name], "WHERE category_id = ?", [$lastInsertId]);
    }

    /**
     * Aktualizuje kategorii
     *
     * @param Category $category
     * @param null $image
     * @throws MyException Pokud se aktualizace nezdaří
     */
    public function update (Category $category, $image = null) {

        if (!empty($image)) {

            $this->initImageFolder();

            $img = new SimpleImage($image['tmp_name']);
            $img->square(self::CATEGORY_IMAGE_SIZE);
            $name = $category->getUrl();
            $img->save("$this->imageFolder$name.png", IMAGETYPE_PNG);
            $category->setImage($name);

        }
        $fromDb = $this->database->update("categories", $category->toArray(), "WHERE category_id = ?", [$category->getId()]);

        if (!$fromDb)
            throw new MyException("Aktualizace kategorie se nezdařila");
    }

    /**
     * Odebere kategorii ze systému
     *
     * @param Category $category
     * @param bool $removeArticles True, pokud chcete odebrat všechny články které kategorie obsahuje
     * @throws MyException Pokud se odebrání nepovede
     */
    public function delete (Category $category, $removeArticles = false) {
        $fromDb = $this->database->delete("categories", "WHERE category_id = ?", [$category->getId()]);

        if (!$fromDb)
            throw new MyException("Odstranění kategorie se nezdařilo");
    }

    /**
     * Vrátí počet všech kategorií v systému
     *
     * @return int
     */
    public function getCount () {
        return $this->database->queryItself("SELECT COUNT(category_id) FROM categories");
    }
}