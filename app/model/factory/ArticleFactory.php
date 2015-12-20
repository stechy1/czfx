<?php

namespace app\model\factory;


use app\model\Article;
use app\model\database\IDatabase;
use app\model\manager\FileManager;
use app\model\service\exception\MyException;
use app\model\util\StringUtils;

/**
 * Class ArticleFactory - Továrna na články
 * @Inject Database
 * @Inject FileManager
 * @package app\model\factory
 */
class ArticleFactory {

    const
        ARTICLE_IS_VALIDATED = true;

    /**
     * @var IDatabase
     */
    private $database;

    /**
     * @var FileManager
     */
    private $filemanager;

    /**
     * Vrátí novou instanci třídy Article na základě poskytnutých dat
     *
     * Všechna data musí být platná
     * @param $data array Data pro vytvoření nové instance
     * @return Article Novou referenci na třídu article
     * @throws MyException Pokud nejsou všechna políčka vyplněna, nebo nějaké není validní
     */
    public function getArticleFromPost($data) {
        $tmpArr = array();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $tmpArr[$key] = $value;
            }
        }
        if (sizeof($tmpArr) != sizeof($data))
            throw new MyException('Nejsou vyplněna všechna políčka');

        $title = $tmpArr['article_title'];
        $url = strtolower(StringUtils::removeDangerousLetters(StringUtils::removeAccents($title)));

        return new Article(
            null,
            $tmpArr['article_category'],
            $title,
            $tmpArr['article_tags'],
            $tmpArr['article_description'],
            $url,
            time(),
            $tmpArr['article_previous'],
            $tmpArr['article_next'],
            $_SESSION['user']['id'],
            $tmpArr['content']);
    }

    /**
     * Vytvoří a vrátí novou instanci článku na základě URL adresy článku
     *
     * @param $artURL string URL adresa článku
     * @param bool $validated
     * @return Article Nová instance reprezentující článek
     * @throws MyException Pokud článek není nalezen
     */
    public function getArticleFromURL($artURL, $validated = false) {
        $query = "SELECT a1.article_id, a1.article_title , a1.article_tags, a1.article_description,
                                         a1.article_url, a1.article_date, a1.article_next, a1.article_previous, a1.article_category,
                                         a1.article_author,
                                         a2.article_title AS previous_article_title, a2.article_url AS previous_article_url,
                                         a3.article_title AS next_article_title, a3.article_url AS next_article_url,
                                         categories.category_name, categories.category_url,
                                         users.user_nick, user_info.user_info_avatar, user_info.user_info_motto
                                  FROM articles a1
                                  LEFT JOIN articles a2 ON a2.article_id = a1.article_previous
                                  LEFT JOIN articles a3 ON a3.article_id = a1.article_next
                                  LEFT JOIN categories ON categories.category_id = a1.article_category
                                  LEFT JOIN users ON users.user_id = a1.article_author
                                  LEFT JOIN user_info ON user_info.user_info_user_id = a1.article_author
                                  WHERE a1.article_url = ?";
        if($validated)
            $query .= " AND a1.article_validated = 1";
        $fromDb = $this->database->queryOne($query
            , [$artURL]);

        if (!$fromDb)
            throw new MyException("Článek nenalezen");

        $fromDb['text'] = $this->filemanager->getArticleContent($fromDb['category_url'], $fromDb['article_url']);

        return new Article(
            $fromDb['article_id'],
            $fromDb['article_category'],
            $fromDb['article_title'],
            $fromDb['article_tags'],
            $fromDb['article_description'],
            $fromDb['article_url'],
            $fromDb['article_date'],
            $fromDb['article_previous'],
            $fromDb['article_next'],
            $fromDb['article_author'],
            $fromDb['text']);
    }

    /**
     * Vytvoří novou instanci článku obnovenou ze session
     *
     * @return Article Novou instanci článku
     * @throws MyException Pokud není v session žádný článek
     */
    public function getArticleFromSession() {
        if (!isset($_SESSION['storage']['article']))
            throw new MyException("Žádný článek nebyl uložen");

        return self::getArticleFromURL($_SESSION['storage']['article']);
    }

    /**
     * Vytvoří a vrátí novou instanci článku na základě ID článku
     *
     * @param $artID integer ID článku
     * @param bool $validated
     * @return Article Nová instance reprezentující článek
     * @throws MyException Pokud článek není nalezen
     */
    public function getArticleFromID($artID, $validated = false) {
        $query = "SELECT a1.article_id, a1.article_title , a1.article_tags, a1.article_description,
                         a1.article_url, a1.article_date, a1.article_next, a1.article_previous, a1.article_category,
                         a2.article_title AS previous_article_title, a2.article_url AS previous_article_url,
                         a3.article_title AS next_article_title, a3.article_url AS next_article_url,
                         categories.category_name, categories.category_url,
                         users.user_nick, user_info.user_info_avatar, user_info.user_info_motto
                  FROM articles a1
                  LEFT JOIN articles a2 ON a2.article_id = a1.article_previous
                  LEFT JOIN articles a3 ON a3.article_id = a1.article_next
                  LEFT JOIN categories ON categories.category_id = a1.article_category
                  LEFT JOIN users ON users.user_id = a1.article_author
                  LEFT JOIN user_info ON user_info.user_info_user_id = a1.article_author
                  WHERE a1.article_id = ?";
        if($validated)
            $query .= " AND a1.article_validated = 1";
        $fromDb = $this->database->queryOne($query
            , [$artID]);

        if (!$fromDb)
            throw new MyException("Článek nenalezen");

        $fromDb['text'] = $this->filemanager->getArticleContent($fromDb['category_url'], $fromDb['article_url']);

        return new Article(
            $fromDb['article_id'],
            $fromDb['article_category'],
            $fromDb['article_title'],
            $fromDb['article_tags'],
            $fromDb['article_description'],
            $fromDb['article_url'],
            $fromDb['article_date'],
            $fromDb['article_previous'],
            $fromDb['article_next'],
            $fromDb['user_nick'],
            $fromDb['text']);
    }

    /**
     * Vrátí posledních X článků od přihlášeného uživatele
     *
     * @param $page int Aktuální stránka
     * @param $recordsOnPage int Počet článků, které se mají zobrazit
     * @return mixed Pole obsahující články
     * @throws MyException Pokud není nalezen žádný článek
     */
    public function getXArticlesFromCurrentUser($page, $recordsOnPage) {
        $fromDb = $this->database->queryAll("SELECT articles.article_id, articles.article_title, articles.article_validated, articles.article_url
                                                FROM articles
                                                WHERE articles.article_author = ?
                                                ORDER BY articles.article_validated, articles.article_id DESC LIMIT ?, ?", [$_SESSION['user']['id'], ($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Zatím nemáte žádné články");

        return $fromDb;
    }

    /**
     * Vrátí posledních X článků odevšech uživatelů
     *
     * @param $page int Aktuální stránka
     * @param $recordsOnPage int Počet článků, které se mají zobrazit
     * @param bool $validated
     * @return mixed Pole obsahující články
     * @throws MyException Pokud není nalezen žádný článek
     */
    public function getXArticlesFromAll($page, $recordsOnPage, $validated = false) {
        $query = "SELECT article_id, article_title, article_validated, article_url, article_description, article_date, categories.category_name, users.user_nick
                     FROM articles
                     LEFT JOIN categories ON categories.category_id = article_category
                     LEFT JOIN users ON users.user_id = article_author";
        if($validated)
            $query .= " WHERE article_validated = 1";
        $query .= " ORDER BY article_id DESC LIMIT ?, ?";
        $fromDb = $this->database->queryAll($query, [($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Žádné články nenalezeny");

        return $fromDb;
    }

    /**
     * Vrátí seznam všech článků v dané kategorii
     *
     * @param $catID int ID kategorie
     * @param null $without array Pole ID článků, který se mají vynechat
     * @return array Pole všech článků
     * @throws MyException Pokud se v zadané kategorii žádné články nenacházeji
     */
    public function getArticlesFromCategoryID($catID, $without = null) {
        $query = "SELECT article_id, article_title
                  FROM articles
                  WHERE article_category = ?";
        if ($without)
            $query .= " AND article_id NOT IN (" . join(", ", $without) . ")";
        $query .= " ORDER BY articles.article_date DESC";
        $fromDb = $this->database->queryAll($query, [$catID]);

        if (!$fromDb)
            throw new MyException("V zadané kategorii nejsou žádné články");

        return $fromDb;
    }

    /**
     * Vrátí všechny články dané kategorie
     *
     * @param $catURL string URL adresa kategorie
     * @return mixed array Pole článků.
     * @throws MyException
     */
    public function getArticlesFromCategoryURL($catURL)
    {
        $fromDb = $this->database->queryAll("SELECT articles.article_title, articles.article_url, articles.article_description, articles.article_date
                                                FROM categories
                                                LEFT JOIN articles
                                                ON (articles.article_category = categories.category_id)
                                                WHERE categories.category_url = ? AND articles.article_validated = ?
                                                ORDER BY articles.article_date ASC",
            [$catURL, 1]);

        if (!$fromDb)
            throw new MyException("V zadané kategorii nejsou žádné články");

        return $fromDb;
    }

    /**
     * Vrátí posledních X článků
     *
     * @param $count int Počet článků, které se mají vrátit.
     * @return mixed
     * @throws MyException
     */
    public function getLastXArticles($count) {
        $fromDb = $this->database->queryAll("SELECT article_title, article_url, article_description, article_date
                                                FROM articles
                                                WHERE articles.article_validated = ? ORDER BY article_date DESC LIMIT ?"
            , [1, $count]);

        if (!$fromDb)
            throw new MyException("Nebyly nalezeny žádné články");

        return $fromDb;
    }

    /**
     * Vrátí posledních X oblíbených článků přihlášeného uživatele
     *
     * @param $page int Aktuální stránka
     * @param $recordsOnPage int Počet článků, které se mají zobrazit
     * @return mixed Pole obsahující oblíbené články
     * @throws MyException Pokud není nalezen žádný článek
     */
    public function getXFavoritesArticles($page, $recordsOnPage) {
        $fromDb = $this->database->queryAll("SELECT articles.article_id, articles.article_title, articles.article_url,
                                                    categories.category_url, categories.category_name,
                                                    users.user_id, users.user_nick
                                             FROM favorite_articles
                                             LEFT JOIN articles ON articles.article_id = favorite_article_id
                                             LEFT JOIN categories ON categories.category_id = articles.article_category
                                             LEFT JOIN users ON users.user_id = articles.article_author
                                             WHERE favorite_article_user_id = ?
                                             ORDER BY articles.article_validated, articles.article_id DESC LIMIT ?, ?",
            [$_SESSION['user']['id'], ($page - 1) * $recordsOnPage, $recordsOnPage]);

        if (!$fromDb)
            throw new MyException("Nemáte žádné oblíbené články");

        return $fromDb;
    }
}