<?php

namespace app\model\manager;


use app\model\Article;
use app\model\database\Database;
use app\model\factory\CategoryFactory;
use app\model\service\exception\MyException;
use app\model\User;

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
     * Přidá článek do oblíbených
     *
     * @param Article $article Článek, který chce uživatel přidat
     * @param User $user Uživatel, který chce přidat článek
     * @throws MyException Pokud se nepodaří článek přidat do oblíbených
     */
    public function addArticleToFavorite(Article $article, User $user) {
        if ($this->isArticleFavourite($article, $user))
            throw new MyException("Článek již je v oblíbených");

        $favArray = array(
            'favorite_article_user_id' => $user->getId(),
            'favorite_article_id' => $article->getId()
        );
        $fromDb = $this->database->insert("favorite_articles", $favArray);

        if (!$fromDb)
            throw new MyException("Článek se nepodařilo přidat do oblíbených");
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
     * Odebere článek z oblíbených
     *
     * @param Article $article Článek, který má být odebrán z oblíbených
     * @param User $user Uřivatel, který článek odebírá
     * @throws MyException Pokud se nepodaří odebrat článek z oblíbených
     */
    public function deleteFavoriteArticle(Article $article, User $user) {
        $fromDb = $this->database->delete(
            "favorite_articles",
            "WHERE favorite_article_user_id = ? AND favorite_article_id = ?",
            [$user->getId(), $article->getId()]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se odebrat článek z oblíbených");
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

    /**
     * Vrátí počet oblíbených článků přihlášeného uživatele
     *
     * @return int Počet oblíbených článků
     */
    public function getFavoriteArticleCount() {
        return $this->database->queryItself("SELECT COUNT(favorite_article_user_id) FROM favorite_articles WHERE favorite_article_user_id = ?", [$_SESSION['user']['id']]);
    }

    /**
     * Zjistí, zda-li je článek v kolekci oblíbených článků vybraného uživatele
     *
     * @param Article $article Článek, který má být oblíbený
     * @param User $user Uživatel, který má oblíbený článek
     * @return bool True, pokud je článek v oblíbených, jinak false
     */
    public function isArticleFavourite(Article $article, User $user) {
        $fromDb = $this->database->queryItself("SELECT COUNT(favorite_article_id)
                                                FROM favorite_articles
                                                WHERE favorite_article_user_id = ? AND favorite_article_id = ?",
            [$user->getId(), $article->getId()]);

        return ($fromDb) ? true : false;
    }
}