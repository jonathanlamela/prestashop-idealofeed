<?php

namespace IdealoFeed\Repository;

use Db;
use Configuration;
use IdealoFeed\Constants;

class FeedRepository
{

    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    // Get Categories
    public function getCategoriesForSelect()
    {
        $defaultLang = Configuration::get("PS_LANG_DEFAULT");

        return $this->db->executeS("
            SELECT
                c.id_category as id,
                l.name as name
            FROM
                " . _DB_PREFIX_ . "category c
            LEFT JOIN
                " . _DB_PREFIX_ . "category_lang l
            ON c.id_category=l.id_category
            WHERE l.id_lang=$defaultLang
        ");
    }

    public function getItem($id, $type)
    {
        return $this->db->executeS("SELECT * FROM " . _DB_PREFIX_ . Constants::APP_PREFIX . $type . "_blacklist WHERE id=$id");
    }

    public function deleteItem($id, $type)
    {
        return $this->db->delete(Constants::APP_PREFIX . $type . "_blacklist", "id=$id");
    }

    public function createItem($data, $type)
    {
        return $this->db->insert(Constants::APP_PREFIX . $type . "_blacklist", $data);
    }


    // Get suppliers
    public function getSuppliersForSelect()
    {
        return $this->db->executeS("
            SELECT
                id_supplier,
                name
            FROM
                " . _DB_PREFIX_ . "supplier
        ");
    }
}
