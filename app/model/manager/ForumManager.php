<?php

namespace app\model\manager;


use app\model\database\Database;
use app\model\service\exception\MyException;
use app\model\util\StringUtils;

/**
 * Class ForumManager
 * @Inject Database
 * @package app\model\manager
 */
class ForumManager {

    /**
     * @var Database
     */
    private $database;

    /**
     * Vrátí seznam všech kategorií na fóru
     *
     * @return array Pole kategorií
     * @throws MyException Pokud není nalezena žádná kategorie
     */
    // TODO dodělat dotaz pro category_topics_count!
    public function getCategories () {
        $query = "SELECT
                  forum_categories.category_id,
                  forum_categories.category_name,
                  forum_categories.category_url,
                  forum_categories.category_description,
                  (SELECT COUNT(topic_id) FROM forum_categories WHERE topic_cat = category_id ) AS category_topics_count,
                  (SELECT COUNT(post_id) FROM forum_topics WHERE post_topic = topic_id) AS topic_posts_count,
                  MAX(forum_posts.post_date)          AS post_date,
                  users.user_nick                     AS nick
                FROM forum_categories
                  INNER JOIN (
                      forum_topics
                      INNER JOIN (forum_posts
                      INNER JOIN users ON users.user_id = forum_posts.post_by
                      ) ON forum_topics.topic_id = forum_posts.post_topic
                    ) ON forum_topics.topic_cat = category_id
                GROUP BY category_id";
        $fromDb = $this->database->queryAll($query);

        if (!$fromDb)
            throw new MyException("Nenalezeny zádné kategorie");

        return $fromDb;
    }

    /**
     * Vrátí informace o zadané kategorii
     *
     * @param null $catURL
     * @return array Informace o kategorii v poli
     * @throws MyException Pokud kategorie neni nalezena
     */
    public function getCategory ($catURL = null) {
        if ($catURL == null)
            $catURL = $_SESSION['forum']['categoryURL']; else
            $_SESSION['forum']['categoryURL'] = $catURL;

        $fromDb = $this->database->queryOne("SELECT category_id, category_name, category_url, category_description
                                    FROM forum_categories
                                    WHERE category_url = ?", [$catURL]);

        if (!$fromDb)
            throw new  MyException("Kategorie nenalezena");

        $_SESSION['forum']['category_id'] = $fromDb['category_id'];
        return $fromDb;
    }

    /**
     * Vrátí aktuálně vybranou kategorii, pokud existuje, jinak null
     *
     * @return string|null
     */
    public function getActualCategory () {
        return $_SESSION['forum']['categoryURL'] ?: null;
    }

    /**
     * Vrátí seznam všech vláken v dané kategorii
     *
     * @param null $catURL URL kategoie
     * @param int $from
     * @return array Pole všech vláken v dané kategorii
     * @throws MyException Pokud kategorie neobsahuje žádná vlákna
     */
    public function getTopics ($catURL = null, $from = PHP_INT_MAX) {
        if ($catURL == null)
            $catURL = $_SESSION['forum']['categoryURL']; else
            $_SESSION['forum']['categoryURL'] = $catURL;

        $fromDb = $this->database->queryAll("SELECT forum_topics.topic_id, forum_topics.topic_subject, forum_topics.topic_date,
                                           forum_topics.topic_url, forum_topics.topic_by,
                                           (SELECT
                                              COUNT(forum_posts.post_id)
                                              FROM forum_posts
                                              WHERE forum_posts.post_topic = forum_topics.topic_id
                                           ) AS topic_posts_count
                                    FROM forum_categories
                                    INNER JOIN forum_topics
                                      ON forum_topics.topic_cat = forum_categories.category_id
                                    WHERE category_url = ? AND forum_topics.topic_id < ?
                                    ORDER BY forum_topics.topic_id DESC
                                    LIMIT 12", [$catURL, $from]);

        if (!$fromDb)
            throw new MyException("Kategorie neobsahuje žádná vlákna");

        return $fromDb;
    }

    /**
     * Vrátí informace o topicu
     *
     * @param $topicUrl
     * @return array
     * @throws MyException Pokud topic neexistuje
     */
    public function getTopic ($topicUrl) {
        $fromDb = $this->database->queryOne("SELECT topic_id, topic_subject, topic_url, topic_date, topic_by
                                    FROM forum_topics
                                    WHERE topic_url = ?", [$topicUrl]);
        if (!$fromDb)
            throw new MyException("Vlákno neexistuje");

        $_SESSION['forum']['topic_id'] = $fromDb['topic_id'];
        return $fromDb;
    }

    /**
     * Smaže z fora topic i s jeho příspěvky
     *
     * @param $topicID int ID topicu
     * @throws MyException
     */
    public function deleteTopic ($topicID) {
        $fromDb = $this->database->delete("forum_topics", "WHERE topic_id = ?", [$topicID]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se odstranit topic číslo: " . $topicID);
    }

    /**
     * Vrátí všechny příspevky v daném topicu
     *
     * @param $topicURL string
     * @return array
     * @throws MyException Pokud se v topicu nenacházejí žádné příspěvky
     */
    public function getPosts ($topicURL = null) {
        if ($topicURL == null)
            $topicURL = $_SESSION['forum']['topicURL'];
        else
            $_SESSION['forum']['topicURL'] = $topicURL;

        $fromDb = $this->database->queryAll("SELECT forum_posts.post_id, forum_posts.post_content, forum_posts.post_date, forum_posts.post_by,
                                           users.user_nick, users.user_avatar
                                           FROM forum_topics
                                           LEFT JOIN (forum_posts, users)
                                           ON (
                                             forum_posts.post_topic = topic_id AND
                                             users.user_id = forum_posts.post_by
                                           )
                                           WHERE topic_url = ?", [$topicURL]);

        if (!$fromDb || !isset($fromDb[0]['post_content']))
            throw new MyException("Žádné posty nenalezeny");

        return $fromDb;
    }

    /**
     * Přidá nové vklákno do databáze
     *
     * @param $subject string Název vlákna
     * @param $message string Obsah zprávy
     * @return string Vrátí url adresu odkazující na nově vytvořené vlákno
     * @throws MyException Pokud se vlákno nepodaří vytvořit
     */
    public function addTopic ($subject, $message) {
        if (!$subject)
            throw new MyException("Není vyplněn nadpis");
        if (!$message)
            throw new MyException("Není vyplněna zpráva");
        $date = time();
        $topicUrl = StringUtils::hyphenize($subject);
        $topic = [
            "topic_subject" => $subject,
            "topic_url" => $topicUrl,
            "topic_date" => $date,
            "topic_cat" => $_SESSION['forum']['category_id'],
            "topic_by" => $_SESSION['user']['id']];

        $this->database->insert("forum_topics", $topic);

        $topicId = $this->database->getLastId();
        $post = [
            "post_content" => $message,
            "post_date" => $date,
            "post_topic" => $topicId,
            "post_by" => $_SESSION['user']['id']];
        $this->database->insert("forum_posts", $post);

        return $topicUrl;
    }

    /**
     * Vrátí x posledních příspěvků
     *
     * @param $count int Počet příspěvků
     * @param bool|true $resetCounter True, pokud se má restartovat čítač příspěvků
     * @return array Pole příspěvků
     * @throws MyException Pokud žádné příspěvky nebyly nalezeny
     */
    public function getLastPosts ($count, $resetCounter = true) {
        $params = array();
        $query = "SELECT forum_posts.post_id, forum_posts.post_content, forum_posts.post_date,
                                        forum_topics.topic_subject, forum_topics.topic_url,
                                        forum_categories.category_url,
                                        users.user_nick, users.user_id
                                 FROM forum_posts
                                 LEFT JOIN (users, forum_topics, forum_categories)
                                 ON (
                                     users.user_id = forum_posts.post_by AND
                                     forum_topics.topic_id = forum_posts.post_topic AND
                                     forum_categories.category_id = forum_topics.topic_cat
                                 )";
        if (!$resetCounter) {
            $query .= " WHERE forum_posts.post_id < ?";
            $params[] = $_SESSION['index']['lastArtID'];
        }

        $query .= " ORDER BY forum_posts.post_id DESC LIMIT ?";

        $params[] = $count;

        $fromDb = $this->database->queryAll($query, $params);
        if (empty($fromDb))
            throw new MyException("Forum neobsahuje více příspěvků");

        $_SESSION['index']['lastArtID'] = $fromDb[sizeof($fromDb) - 1]['post_id'];

        return $fromDb;

    }

    /**
     * Přidá post do topicu
     *
     * @param $content string Obsah komentáře
     * @throws MyException Pokud se nepodaří komentář přidat
     */
    public function addPost ($content) {
        if (!$content)
            throw new MyException("Musíte vyplnit zprávu");
        $post = [
            "post_content" => $content,
            "post_date" => time(),
            "post_topic" => $_SESSION['forum']['topic_id'],
            "post_by" => $_SESSION['user']['id']];

        $this->database->insert("forum_posts", $post);

    }

    /**
     * Smaže příspěvek podle ID
     *
     * @param $postID int ID příspěvku, který má být smazán
     * @throws MyException Pokud se nepodaří příspěvek odstranit
     */
    public function deletePost ($postID) {
        $fromDb = $this->database->delete("forum_posts", "WHERE post_id = ?", [$postID]);

        if (!$fromDb)
            throw new MyException("Nepodařilo se odstranit příspěvek číslo: " . $postID);
    }
}