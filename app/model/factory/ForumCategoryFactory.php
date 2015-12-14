<?php

namespace app\model\factory;


use app\model\database\IDatabase;
use app\model\ForumCategory;
use app\model\service\exception\MyException;
use app\model\util\StringUtils;

/**
 * Class ForumCategoryFactory
 * @Inject Database
 * @package app\model\factory
 */
class ForumCategoryFactory {

    /**
     * @var IDatabase
     */
    private $database;


    /**
     * Vytvoří novou instanci třídy ForumCategory z postu
     *
     * @param $data
     * @return ForumCategory
     * @throws MyException
     */
    public function getCategoryFromPost ($data) {
        $tmpArr = array();
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $tmpArr[$key] = $value;
            }
        }

        if (sizeof($tmpArr) != sizeof($data))
            throw new MyException('Nejsou vyplněna všechna políčka');

        $name = $data['name'];
        $url = StringUtils::removeDangerousLetters(StringUtils::removeAccents($data['url']));
        $desc = $data['description'];

        return new ForumCategory(null, $name, $url, $desc);
    }

    /**
     * Získá forum kategorii z id
     *
     * @param $id
     * @return ForumCategory
     * @throws MyException
     */
    public function getFromID ($id) {
        $fromDb = $this->database->queryOne("SELECT category_id, category_name, category_url, category_description FROM forum_categories WHERE category_id = ?", [$id]);

        if (!$fromDb)
            throw new MyException("Kategorie nebyla nelezena");

        return new ForumCategory(
            $fromDb['category_id'],
            $fromDb['category_name'],
            $fromDb['category_url'],
            $fromDb['category_description']
        );
    }
}