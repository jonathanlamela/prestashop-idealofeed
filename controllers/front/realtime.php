<?php

use IdealoFeed\Constants;

class IdealoFeedRealtimeModuleFrontController extends ModuleFrontController
{
    public $auth = false;
    public $ajax = true;

    public $display_header = false;
    public $display_footer = false;

    public function initContent(): void
    {
        parent::initContent();

        // Disabilita il template
        $this->setTemplate("module:idealofeed/views/templates/front/index.html.twig");

        $this->writeFeed();
    }


    public function writeFeed()
    {
        ini_set('memory_limit', '4096M');

        $db = Db::getInstance();

        $str_query = "
        SELECT
            pl.link_rewrite,
            ps.id_supplier,
            p.id_product,
            p.ean13,
            p.reference,
            p.id_category_default,
            pm.name AS brand,
            pl.name,
            p.price,
            pl.delivery_in_stock,
            pl.description_short,
            wi.image_url AS image_url,
            p.condition,
            wp.internal_code
        FROM " . _DB_PREFIX_ . "product p
        INNER JOIN " . _DB_PREFIX_ . "product_lang pl
            ON p.id_product = pl.id_product AND pl.id_lang = 1
        INNER JOIN " . _DB_PREFIX_ . "webfeed_product wp
            ON wp.id_product = p.id_product
        INNER JOIN " . _DB_PREFIX_ . "webfeed_images wi
            ON wp.internal_code = wi.internal_code AND wi.image_url IS NOT NULL
        INNER JOIN " . _DB_PREFIX_ . "stock_available st
            ON st.id_product = p.id_product AND st.quantity > 0
        LEFT JOIN " . _DB_PREFIX_ . "manufacturer pm
            ON pm.id_manufacturer = p.id_manufacturer
        LEFT JOIN " . _DB_PREFIX_ . "product_supplier ps
            ON ps.id_product = p.id_product
        WHERE p.id_category_default >= 2;
        ";

        $filePath = _PS_ROOT_DIR_ . "/datafeed/idealo.csv";
        $file = fopen($filePath, "w");

        $header = [
            "Numero articolo nello shop",
            "EAN / GTIN / codice a barre / UPC",
            "Numero articolo produttore originale (HAN/MPN)",
            "Produttore / Marca",
            "Nome prodotto",
            "Prezzo (lordo)",
            "Tempi di consegna",
            "Categoria di prodotti nello shop",
            "Descrizione prodotto",
            "URL prodotto",
            "URL immagine",
            "Spese di spedizione",
            "Condizioni",
            "Tipo condizione",
        ];
        fputcsv($file, $header, "|");

        $results = $db->executeS($str_query);

        $suppliers = [];
        $suppliers_query = $db->executeS("SELECT id_supplier FROM " . _DB_PREFIX_ . Constants::APP_PREFIX . "supplier_blacklist");

        foreach ($suppliers_query as $supplier) {
            $suppliers[$supplier['id_supplier']] = $supplier['id_supplier'];
        }

        $categories = [];
        $categories_query = $db->executeS("SELECT id_category FROM " . _DB_PREFIX_ . Constants::APP_PREFIX . "category_blacklist");

        foreach ($categories_query as $category) {
            $categories[$category['id_category']] = $category['id_category'];
        }

        $internal_codes = [];
        $internal_codes_query = $db->executeS("SELECT internal_code FROM " . _DB_PREFIX_ . Constants::APP_PREFIX . "product_blacklist");

        foreach ($internal_codes_query as $item) {
            $internal_codes[] = $item["internal_code"];
        }

        //Ottieni i prezzi specifici attivi
        $specific_prices_query = $db->executeS("SELECT * FROM " . _DB_PREFIX_ . "specific_price WHERE `to` > NOW()");
        $specific_prices = [];

        foreach ($specific_prices_query as $specific_price) {
            $specific_prices[$specific_price["id_product"]] = [
                "reduction" => $specific_price["reduction"],
                "reduction_type" => $specific_price["reduction_type"],
                "price" => $specific_price["price"],
            ];
        }

        if ($results) {
            foreach ($results as $row) {

                if (in_array($row["internal_code"], $internal_codes)) {
                    continue; // Skip products in internal codes
                }

                if (in_array($row["id_supplier"], $suppliers)) {
                    continue; // Skip products not in suppliers
                }

                if (in_array($row["id_category_default"], $categories)) {
                    continue; // Skip products from excluded suppliers
                }

                if ($row["id_category_default"]) {
                    $category = new Category($row["id_category_default"]);
                    $parents = $category->getParentsCategories();
                    // Ordina i genitori dal root alla categoria corrente
                    $parents = array_reverse($parents);
                    // Estrai solo i nomi
                    $names = array_column($parents, 'name');
                    // Unisci con " > "
                    $path = implode(' > ', $names);
                    $row["category_tree"] = $path;
                } else {
                    continue; // Skip products with no category
                }

                if (isset($specific_prices[$row["id_product"]])) {
                    $specific_price = $specific_prices[$row["id_product"]];
                    if ($specific_price["reduction_type"] == "amount") {
                        $row["price"] = $specific_price["price"];
                    }
                }

                $link = "https://" . Tools::getShopDomain() . "/" . $row["id_product"] . "-" . $row["link_rewrite"] . ".html?utm_source=idealo";

                $condition = $row["condition"];

                fputcsv($file, [
                    $row["id_product"],
                    $row["ean13"],
                    $row["reference"],
                    $row["brand"],
                    $row["name"],
                    round($row["price"] * 1.22, 2),
                    $row["delivery_in_stock"],
                    $row["category_tree"],
                    $row["description_short"],
                    $link,
                    $row["image_url"],
                    0,
                    "GOOD",
                    $condition ? strtoupper($condition) : "NEW",
                ], "|");
            }
        }

        fclose($file);

        // Controlla se il file è vuoto
        if (filesize($filePath) === 0) {
            http_response_code(500);
            exit('Errore: il file idealo.txt è vuoto.');
        }

        ini_set('max_execution_time', '60');
        ini_set('memory_limit', '256M');


        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="idealo.txt"');
        readfile($filePath);
    }
}
