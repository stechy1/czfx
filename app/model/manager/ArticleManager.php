<?php

namespace app\model\manager;


use app\model\Article;
use app\model\database\Database;
use app\model\factory\CategoryFactory;
use app\model\service\exception\MyException;

/**
 * Class ArticleManager - Správce článků
 * @Inject FileManager
 * @Inject Database
 * @Inject CategoryFactory
 * @package app\model\manager
 */
class ArticleManager {

    /**
     * @var FileManager
     */
    private $filemanager;
    /**
     * @var Database
     */
    private $database;
    /**
     * @var CategoryFactory
     */
    private $categoryfactory;

    /**
     * Přidá nový článek do databáze
     *
     * @param Article $article Článek
     * @return bool True, pokud se článek povedlo vytvořit.
     * @throws MyException Pokud se stala chyba při vytváření článku.
     */
    public function add(Article $article) {
        $this->database->beginTransaction();
        $success = $this->database->insert('articles', $article->toArray());

        if (!$success) {
            $this->database->rollback();
            throw new MyException('Článek se nepodařilo uložit');
        }

        try {
            $category = $this->categoryfactory->getCategoryFromArticle($article);
        } catch (MyException $ex) {
            $this->database->rollback();
            throw new MyException($ex->getMessage());
        }

        // Vytvoření nové složky pro článek
        $folder = $this->filemanager->createArticleDirectory($category->getUrl(), $article->getUrl());
        $attachments = FileManager::getAttachmentsFolder($folder);

        $filePath = $folder . "/" . $article->getUrl() . ".markdown";
        $text = str_replace($this->filemanager->getRelativePath($this->filemanager->getTmpDirectory()), $this->filemanager->getRelativePath($attachments), $article->getText());
        $this->filemanager->writeFile($filePath, $text);

        FileManager::moveFiles($this->filemanager->getTmpDirectory(), $attachments);
        $this->database->commit();
        return true;
    }

    /**
     * Aktualizuje článek
     *
     * @param Article $article
     */
    public function update(Article $article) {
        $this->database->update('articles', $article->toArray(), "WHERE article_id = ?", [$article->getId()]);

        $category = $this->categoryfactory->getCategoryFromArticle($article);
        $folder = $this->filemanager->createArticleDirectory($category->getUrl(), $article->getUrl());
        $attachments = FileManager::getAttachmentsFolder($folder);

        $filePath = $folder . "/" . $article->getUrl() . ".markdown";
        $text = str_replace($this->filemanager->getRelativePath($this->filemanager->getTmpDirectory()), $this->filemanager->getRelativePath($attachments), $article->getText());
        $this->filemanager->writeFile($filePath, $text);
    }

    /**
     * Smaže článek ze systému
     *
     * @param Article $article
     * @throws MyException Pokud se článek nepodaří smazat
     */
    public function delete(Article $article) {
        $this->database->beginTransaction();

        $fromDb = $this->database->delete("articles", "WHERE article_id = ?", [$article->getId()]);
        if (!$fromDb) {
            $this->database->rollback();
            throw new MyException("Nepodařilo se smazat článek z databáze");
        }

        $category = $this->categoryfactory->getCategoryFromArticle($article);
        $folder = $this->filemanager->createArticleDirectory($category->getUrl(), $article->getUrl());

        $success = $this->filemanager->recursiveDelete($folder);
        if (!$success) {
            $this->database->rollback();
            throw new MyException("Nepodařilo se smazat článek ze souborového systému");
        }
        else
            $this->database->commit();
    }

    /**
     * Změní validaci článku
     *
     * @param $id int ID validovaného článku
     * @param $valid bool True, pokud má být článek schválen, jinak false
     * @throws MyException Pokud se validace nepovede
     */
    public function validate($id, $valid) {
        $arr = array(
            "article_validated" => ($valid)?1:0,
            "article_date" => time()
        );
        $fromDb = $this->database->update("articles", $arr, "WHERE article_id = ?", [$id]);

        if (!$fromDb)
            throw new MyException("Změna validace se neprovedla");
    }

    /**
     * Vrátí počet článků od přihlášeného uživatele
     *
     * @return int
     */
    public function getArticleCountFromCurrentUser() {
        return $this->database->queryItself("SELECT COUNT(article_id) FROM articles WHERE article_author = ?", [$_SESSION['user']['id']]);
    }

    /**
     * Vrátí počet všech článků v systému
     *
     * @return int
     */
    public function getArticleCountFromAll() {
        return $this->database->queryItself("SELECT COUNT(article_id) FROM articles");
    }
}