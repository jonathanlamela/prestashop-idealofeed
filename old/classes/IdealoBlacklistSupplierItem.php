<?php


class IdealoBlacklistSupplierItem extends ObjectModel
{
    public $supplier_id = null;

    public static $definition = array(
        'table' => 'idealofeed_blacklist_suppliers',
        'primary' => 'id_idealofeed_blacklist_suppliers',
        'fields' => array(
            'supplier_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
        ),
    );
}
