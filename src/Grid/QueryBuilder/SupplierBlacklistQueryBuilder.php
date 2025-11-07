<?php

namespace IdealoFeed\Grid\QueryBuilder;

use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use IdealoFeed\Constants;

final class SupplierBlacklistQueryBuilder extends AbstractDoctrineQueryBuilder
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
        s.id,
        s.id_supplier,
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
                $qb->andWhere("s.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'id_supplier' && !empty($filterValue)) {
                $qb->andWhere("s.id_supplier = :$filterName");
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
        $qb->select('COUNT(s.id)');

        // Filtri
        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ($filterName === 'id' && !empty($filterValue)) {
                $qb->andWhere("s.id = :$filterName");
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'id_supplier' && !empty($filterValue)) {
                $qb->andWhere("s.id_supplier = :$filterName");
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


        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . Constants::APP_PREFIX . 'supplier_blacklist', 's')
            ->leftJoin('s', $this->dbPrefix . 'supplier', 'l', 's.id_supplier = l.id_supplier');
    }
}
