<?php

namespace app\model\factory;


use app\model\Article;
use app\model\Category;
use app\model\database\Database;
use app\model\service\exception\MyException;

/**
 * Class CategoryFactory - Továrna na kategorie
 * @Inject Database
 * @package app\model\factory
 */
class CategoryFactory {

    /**
     * @var Database
     */
    private $database;

    /**
     * Vrátí instanci třídy Category
     *
     * @param $id int ID kategorie.
     * @return Category Instance třídy Category se všemy údaji.
     * @throws MyException Pokud je zadaný špatný formát ID kategorie, nebo pokud kategorie neexistuje.
     */
    public function getCategoryFromID($id) {
        $fromDb = $this->database->queryOne("SELECT category_id, category_name, category_url, category_description,
                                         category_parent, category_has_subcats
                                  FROM categories p1
                                  WHERE p1.category_id = ?", [$id]);

        if(!$fromDb)
            throw new MyException("Kategorie neexistuje");

        return new Category(
            $fromDb['category_id'],
            $fromDb['category_name'],
            $fromDb['category_url'],
            $fromDb['category_description'],
            $fromDb['category_parent'],
            $fromDb['category_has_subcats']
        );
    }

    /**
     * Vytvoří novou instanci kategorie z článku
     *
     * @param Article $article Článek, který je v dané kategorii
     * @return Category Novou kategorii, ve které je článek
     * @throws MyException Pokud je zadaný špatný formát ID kategorie, nebo pokud kategorie neexistuje.
     */
    public function getCategoryFromArticle(Article $article) {
        return $this->getCategoryFromID($article->getCategoryID());
    }

    /**
     * Vrátí všechny kategorie, které nemají žádného rodiče
     *
     * @param bool|false True, pokud se mají vybrat i rodičovské kategorie, jinak false.
     * @return array Vrátí kategorie.
     */
    public function getAll($showAll = false) {

        if ($showAll)
            return $this->database->queryAll("SELECT category_id, category_name, category_url, category_description, category_parent, category_has_subcats
                                     FROM categories");

        return $this->database->queryAll("SELECT category_id, category_name, category_url, category_description, category_parent, category_has_subcats
                                 FROM categories
                                 WHERE category_parent = ?", [-1]);
    }

    /**
     * Vrátí všechny podkategorie dané kategorie
     *
     * @param $subCat array
     * @return array
     * @throws MyException
     */
    public function getSubcats($subCat)
    {
        $fromDb = $this->database->queryAll("SELECT category_id, category_name, category_url, category_description, category_parent, category_has_subcats
                                    FROM categories
                                    WHERE category_parent = (
                                       SELECT category_id
                                       FROM categories
                                       WHERE category_name = ?
                                    )", [$subCat]);
        if(!$fromDb)
            throw new MyException("V zadané kategorii nejsou žádné podkategorie");

        return $fromDb;
    }

    /**
     * Vrátí všechny kategorie kromě jedné
     *
     * @param $categoryID integer Nechtěná kategorie
     * @param bool|false $showAll True, pokud se mají vybrat i rodičovské kategorie, jinak false
     * @return array
     */
    public function getWithout($categoryID, $showAll = false) {
        if ($showAll)
            return $this->database->queryAll("SELECT category_id, category_name, category_url, category_description, category_parent, category_has_subcats
                                     FROM categories WHERE category_id != ?", [$categoryID]);

        return $this->database->queryAll("SELECT category_id, category_name, category_url, category_description, category_parent, category_has_subcats
                                 FROM categories
                                 WHERE category_parent = ? AND category_id != ?", [-1, $categoryID]);
    }

    /**
     * Vrátí posledních X kategorií
     *
     * @param $page int Aktuální stránka
     * @param $recordsOnPage int Počet záznamů na stránku
     * @return array Pole kategorií
     * @throws MyException Pokud nebyly nalezeny žádné kategorie
     */
    public function getLastXCategoriesFromAll($page, $recordsOnPage) {
        $fromDb = $this->database->queryAll("SELECT category_id, category_name, category_url, category_parent, category_has_subcats
                                 FROM categories
                                 ORDER BY category_id DESC LIMIT ?, ?", [($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Žádné články nenalezeny");

        return $fromDb;
    }

    /**
     * Vytvoří novou instanci třídy kategorie z asociativního pole
     *
     * @param $data array Asociativní pole dat
     * @return Category
     * @throws MyException Pokud se nepodaří vytvořit novou instanci kategorie
     */
    public function getFromRawData($data) {
        unset($data['category-id']);
        $data['category-has-subcats'] = (isset($data['category-has-subcats'])) ? 1 : 0;
        $arr = array(
            "category-parent",
            "category-has-subcats",
            "category-name",
            "category-url",
            "category-description"
        );
        $count = 0;

        foreach ($data as $key => $value) {
            $count++;
            if (!in_array($key, $arr) || $value === "")
                throw new MyException("Nebyly vyplněny všechny položky");
        }

        if ($count != count($arr))
            throw new MyException("Nebyly vyplněny všechny položky");

        return new Category(
            -1,
            $data['category-name'],
            $data['category-url'],
            $data['category-description'],
            $data['category-parent'],
            $data['category-has-subcats']
        );
    }
}