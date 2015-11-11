<?php

namespace app\model\manager;


use app\model\Category;
use app\model\database\Database;
use app\model\service\exception\MyException;

/**
 * Class CategoryManager - Správce kategorii článků
 * @Inject Database
 * @package app\model\manager
 */
class CategoryManager {

    /**
     * @var Database
     */
    private $database;

    /**
     * Přidá novou kategorii do databáze
     *
     * @param Category $category
     * @throws MyException Pokud se nepodaří přidat novou kategorii
     */
    public function add (Category $category) {
        $fromDb = $this->database->insert("categories", $category->toArray());

        if (!$fromDb)
            throw new MyException("Přidání nové kategorie se nepodařilo");
    }

    /**
     * Aktualizuje kategorii
     *
     * @param Category $category
     * @throws MyException Pokud se aktualizace nezdaří
     */
    public function update (Category $category) {
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
    public function getCount() {
        return $this->database->queryItself("SELECT COUNT(category_id) FROM categories");
    }
}