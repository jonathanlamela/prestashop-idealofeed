<?php

/**
 * 2014-2025 MyQuickList / Jonathan La Mela
 *
 * NOTICE OF LICENSE
 *
 * E' vietata la riproduzione parziale e non del modulo,
 * la vendita e la distribuzione non autorizzata dalla MyQuickList.
 *
 * @author    Jonathan La Mela
 * @copyright 2014-2025
 * @license   http://www.creativecommons.it/ Creative Commons
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer; // Per PrestaShop 8+
use Db;
use Shop;

class IdealoFeed extends Module
{

    public function __construct()
    {
        $this->name = 'idealofeed';
        $this->tab = 'administration';
        $this->version = '2.0.0';
        $this->author = 'Jonathan La Mela';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('IdealoFeed');
        $this->description = $this->l('Genera feed per idealo');

        $this->confirmUninstall = $this->l('Sei sicuro di voler disinstallare questo modulo?');
    }

    /**
     * Installazione del modulo
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        return parent::install()
            && $this->installDatabase() && $this->installTabs();
    }


    public function installTabs()
    {
        $tabMain = new TabCore();
        $tabMain->active = true;
        $tabMain->enabled = true;
        $tabMain->class_name = "IdealoFeed";
        $tabMain->route_name = "";
        $tabMain->icon = "settings";
        $tabMain->module =  $this->name;
        $tabMain->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = "Idealo";
        $tabMain->id_parent = 2;
        $tabMain->save();

        $tabBlackListProdotti = new TabCore();
        $tabBlackListProdotti->active = true;
        $tabBlackListProdotti->enabled = true;
        $tabBlackListProdotti->class_name = "IdealoFeedProductBlacklist";
        $tabBlackListProdotti->route_name = "idealofeed_product_blacklist_index";
        $tabBlackListProdotti->module =  $this->name;
        $tabBlackListProdotti->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = "Blacklist prodotti";
        $tabBlackListProdotti->id_parent = $tabMain->id;
        $tabBlackListProdotti->save();

        $tabBlackListCategorie = new TabCore();
        $tabBlackListCategorie->active = true;
        $tabBlackListCategorie->enabled = true;
        $tabBlackListCategorie->class_name = "IdealoFeedCategoryBlacklist";
        $tabBlackListCategorie->route_name = "idealofeed_category_blacklist_index";
        $tabBlackListCategorie->module =  $this->name;
        $tabBlackListCategorie->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = "Blacklist categorie";
        $tabBlackListCategorie->id_parent = $tabMain->id;
        $tabBlackListCategorie->save();

        $tabBlackListFornitori = new TabCore();
        $tabBlackListFornitori->active = true;
        $tabBlackListFornitori->enabled = true;
        $tabBlackListFornitori->class_name = "IdealoFeedSupplierBlacklist";
        $tabBlackListFornitori->route_name = "idealofeed_supplier_blacklist_index";
        $tabBlackListFornitori->module =  $this->name;
        $tabBlackListFornitori->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = "Blacklist fornitori";
        $tabBlackListFornitori->id_parent = $tabMain->id;
        $tabBlackListFornitori->save();



        return true;
    }

    /**
     * Disinstallazione
     */
    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallTabs();
    }

    public function uninstallTabs()
    {
        $db = Db::getInstance();

        $db->execute("DELETE FROM " . _DB_PREFIX_ . "tab WHERE module='" . $this->name . "'");
    }

    /**
     * Crea tabelle necessarie
     */
    protected function installDatabase()
    {
        $db = Db::getInstance();

        $queries = [
            "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . $this->name . "_product_blacklist(
                id INT AUTO_INCREMENT PRIMARY KEY,
               internal_code VARCHAR(100) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ .  $this->name . "_category_blacklist(
                id INT AUTO_INCREMENT PRIMARY KEY,
               id_category INT(11) NOT NULL
            )",
            "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . $this->name . "_supplier_blacklist(
                id INT AUTO_INCREMENT PRIMARY KEY,
               id_supplier INT(11) NOT NULL
            )",
        ];


        foreach ($queries as $sql) {
            $db->execute($sql);
        }

        return true;
    }




    public function getContent()
    {
        $container = SymfonyContainer::getInstance();
        $router = $container->get('router');
        $url = $router->generate('idealofeed_settings_index');

        Tools::redirectAdmin($url);
    }
}
