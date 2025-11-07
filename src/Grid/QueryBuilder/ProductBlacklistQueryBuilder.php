<?php

namespace IdealoFeed\Grid\QueryBuilder;

use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use IdealoFeed\Constants;

final class ProductBlacklistQueryBuilder extends AbstractDoctrineQueryBuilder
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
        p.id,
        p.internal_code
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
                $qb->andWhere("p.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }
            if ($filterName === 'internal_code' && !empty($filterValue)) {
                $qb->andWhere("p.internal_code LIKE :$filterName");
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
        $qb->select('COUNT(p.id)');

        // Filtri
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ($filterName === 'id' && !empty($filterValue)) {
                $qb->andWhere("p.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }
            if ($filterName === 'internal_code' && !empty($filterValue)) {
                $qb->andWhere("p.internal_code LIKE :$filterName");
                $qb->setParameter($filterName, "%" . $filterValue . "%");
                continue;
            }
        }

        return $qb;
    }

    private function getBaseQuery()
    {


        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . Constants::APP_PREFIX . 'product_blacklist', 'p');
    }
}
