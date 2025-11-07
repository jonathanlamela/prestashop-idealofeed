<?php

namespace IdealoFeed\Grid\QueryBuilder;

use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Configuration;
use IdealoFeed\Constants;

final class CategoryBlacklistQueryBuilder extends AbstractDoctrineQueryBuilder
{

    public function __construct(Connection $connection, string $dbPrefix)
    {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();

        // Seleziona le colonne
        $qb->select('
        c.id,
        c.id_category,
        l.name
        ')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset() ?? 0)
            ->setMaxResults($searchCriteria->getLimit());

        // Filtri
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ($filterName === 'id' && !empty($filterValue)) {
                $qb->andWhere("c.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'id_category' && !empty($filterValue)) {
                $qb->andWhere("c.id_category = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'name' && !empty($filterValue)) {
                $qb->andWhere("l.name LIKE :$filterName");
                $qb->setParameter($filterName, "%" . $filterValue . "%");
                continue;
            }
        }


        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();

        // Conta i record totali
        $qb->select('COUNT(c.id)');

        // Filtri
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ($filterName === 'id' && !empty($filterValue)) {
                $qb->andWhere("c.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'id_category' && !empty($filterValue)) {
                $qb->andWhere("c.id_category = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'name' && !empty($filterValue)) {
                $qb->andWhere("l.name LIKE :$filterName");
                $qb->setParameter($filterName, "%" . $filterValue . "%");
                continue;
            }
        }

        return $qb;
    }

    private function getBaseQuery()
    {

        $langDefault = Configuration::get("PS_LANG_DEFAULT");

        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . Constants::APP_PREFIX . 'category_blacklist', 'c')
            ->leftJoin('c', $this->dbPrefix . 'category_lang', 'l', 'c.id_category = l.id_category', 'l.id_lang=' . $langDefault);
    }
}
