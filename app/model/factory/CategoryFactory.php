<?php

namespace app\model\factory;


use app\model\Article;
use app\model\Category;
use app\model\database\Database;
use Exception;

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
     * Vrátí instanci třídy Category.
     * @param $id int ID kategorie.
     * @return Category Instance třídy Category se všemy údaji.
     * @throws Exception Pokud je zadaný špatný formát ID kategorie, nebo pokud kategorie neexistuje.
     */
    public function getCategoryFromID($id) {
        $fromDb = $this->database->queryOne("SELECT category_id, category_name, category_url, category_description,
                                         category_parent, category_has_subcats
                                  FROM categories p1
                                  WHERE p1.category_id = ?", [$id]);

        if(!$fromDb)
            throw new Exception("Kategorie neexistuje");

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
     * @param Article $article Článek, který je v dané kategorii
     * @return Category Novou kategorii, ve které je článek
     * @throws Exception Pokud je zadaný špatný formát ID kategorie, nebo pokud kategorie neexistuje.
     */
    public function getCategoryFromArticle(Article $article) {
        return $this->getCategoryFromID($article->getCategoryID());
    }

    /**
     * Vrátí všechny kategorie, které nemají žádného rodiče.
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
     * @param $subCat array
     * @return array
     * @throws Exception
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
            throw new Exception("V zadané kategorii nejsou žádné podkategorie");

        return $fromDb;
    }

    /**
     * Vrátí všechny kategorie kromě jedné
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
}