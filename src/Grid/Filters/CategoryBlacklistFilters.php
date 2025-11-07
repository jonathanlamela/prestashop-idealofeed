<?php

namespace IdealoFeed\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;
use IdealoFeed\Grid\DefinitionFactory\CategoryBlacklistGridDefinitionFactory;


final class CategoryBlacklistFilters extends Filters
{
    protected $filterId = CategoryBlacklistGridDefinitionFactory::GRID_ID;

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
