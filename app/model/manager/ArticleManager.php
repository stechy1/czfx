<?php

namespace app\model\manager;


use app\model\Article;
use app\model\database\Database;
use app\model\factory\CategoryFactory;
use Exception;

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
     * @throws Exception Pokud se stala chyba při vytváření článku.
     */
    public function add(Article $article) {
        $this->database->beginTransaction();
        $success = $this->database->insert('articles', $article->toArray());

        if (!$success) {
            $this->database->rollback();
            throw new Exception('Článek se nepodařilo uložit');
        }

        try {
            $category = $this->categoryfactory->getCategoryFromArticle($article);
        } catch (Exception $ex) {
            $this->database->rollback();
            throw new Exception($ex->getMessage());
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
     * @param $artURL string URL adresa článku
     * @return mixed
     * @throws Exception
     */
    public function get($artURL)
    {
        $fromDb = $this->database->queryOne("SELECT a1.article_id, a1.article_title , a1.article_tags, a1.article_description,
                                         a1.article_url, a1.article_date,
                                    a2.article_title AS previous_article_title, a2.article_url AS previous_article_url,
                                    a3.article_title AS next_article_title, a3.article_url AS next_article_url,
                                    categories.category_name, categories.category_url,
                                    users.user_nick, users.user_avatar, users.user_motto
                                  FROM articles a1
                                  LEFT JOIN articles a2 ON a2.article_id = a1.article_previous
                                  LEFT JOIN articles a3 ON a3.article_id = a1.article_next
                                  LEFT JOIN categories ON categories.category_id = a1.article_category
                                  LEFT JOIN users ON users.user_id = a1.article_author
                                  LEFT JOIN user_info ON user_info.user_info_user_id = a1.article_author
                                  WHERE a1.article_url = ? AND a1.article_validated = ?"
            , [$artURL, 1]);

        if (!$fromDb)
            throw new Exception("Článek nenalezen");

        /*$path = "./uploads/category/" . $fromDb['category_url'] . "/" . $fromDb['article_url'] . "/" . $fromDb['article_url'] . ".markdown";
        $file = fopen($path, "r");
        $text = fread($file, filesize($path));
        fclose($file);
        $fromDb['text'] = $text;*/
        $fromDb['text'] = $this->filemanager->getArticleContent($fromDb['category_url'], $fromDb['article_url']);

        return $fromDb;
    }

    public function update(Article $article) {
        $this->database->update('articles', $article->toArray(), "WHERE article_id = ?", [$article->getId()]);

        $category = $this->categoryfactory->getCategoryFromArticle($article);
        $folder = $this->filemanager->createArticleDirectory($category->getUrl(), $article->getUrl());
        $attachments = FileManager::getAttachmentsFolder($folder);

        $filePath = $folder . "/" . $article->getUrl() . ".markdown";
        $text = str_replace($this->filemanager->getRelativePath($this->filemanager->getTmpDirectory()), $this->filemanager->getRelativePath($attachments), $article->getText());
        $this->filemanager->writeFile($filePath, $text);
    }
}