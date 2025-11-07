<?php

namespace IdealoFeed\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use IdealoFeed\Grid\DefinitionFactory\SupplierBlacklistGridDefinitionFactory;


final class SupplierBlacklistFilters extends Filters
{
    protected $filterId = SupplierBlacklistGridDefinitionFactory::GRID_ID;

    public static function getDefaults(): array
    {
        return [
            'limit' => 100,
            'offset' => 0,
            'orderBy' => 'id',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
