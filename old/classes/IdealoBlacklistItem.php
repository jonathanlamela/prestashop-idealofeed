<?php


class IdealoBlacklistItem extends ObjectModel
{
    public $internal_code = null;

    public static $definition = array(
        'table' => 'idealofeed_blacklist',
        'primary' => 'id_idealofeed_blacklist',
        'fields' => array(
            'internal_code' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 100),
        ),
    );
}
