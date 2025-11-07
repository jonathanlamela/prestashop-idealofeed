<?php

namespace IdealoFeed\Grid\DefinitionFactory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractFilterableGridDefinitionFactory;

use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use IdealoFeed\Controller\CategoryBlacklistController;
use IdealoFeed\Constants;

final class CategoryBlacklistGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    public const GRID_ID = Constants::APP_PREFIX . "category_blacklist";

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Blacklist categorie', [], Constants::TRANSLATION_DOMAIN);
    }

    protected function getColumns(): ColumnCollection
    {
        $columns = new ColumnCollection();

        $columns->add(
            (new DataColumn('id'))
                ->setName($this->trans('ID', [], Constants::TRANSLATION_DOMAIN))
                ->setOptions([
                    'field' => 'id',
                    'sortable' => true,
                ])
        );

         $columns->add(
            (new DataColumn('id_category'))
                ->setName($this->trans('ID Categoria', [], Constants::TRANSLATION_DOMAIN))
                ->setOptions([
                    'field' => 'id_category',
                    'sortable' => true,
                ])
        );

        $columns->add(
            (new DataColumn('name'))
                ->setName($this->trans('Nome categoria', [], Constants::TRANSLATION_DOMAIN))
                ->setOptions([
                    'field' => 'name',
                    'sortable' => true,
                ])
        );



        $actionsColumn = new ActionColumn('actions');
        $actionsColumn->setName('Actions');

        $rowActions = new RowActionCollection();


        $rowActions->add(
            (new SubmitRowAction('delete'))
                ->setName($this->trans('Delete', [], Constants::TRANSLATION_DOMAIN))
                ->setIcon('delete')
                ->setOptions([
                    'route' => CategoryBlacklistController::DELETE_ROUTE,
                    'route_param_name' => 'id',
                    'route_param_field' => 'id',
                    'confirm_message' => $this->trans(
                        'Sei sicuro di voler eliminate questo record?',
                        [],
                        Constants::TRANSLATION_DOMAIN
                    ),
                ])
        );


        $actionsColumn->setOptions([
            'actions' => $rowActions,
        ]);

        $columns->add($actionsColumn);

        return $columns;
    }


    protected function getFilters(): FilterCollection
    {
        return (new FilterCollection())

            ->add((new Filter('id', TextType::class))
                    ->setAssociatedColumn('id')
                    ->setTypeOptions([
                        'required' => false
                    ])
            )
            ->add((new Filter('name', TextType::class))
                    ->setAssociatedColumn('name')
                    ->setTypeOptions([
                        'required' => false
                    ])
            )
              ->add((new Filter('id_category', TextType::class))
                    ->setAssociatedColumn('id_category')
                    ->setTypeOptions([
                        'required' => false
                    ])
            )

            ->add((new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => CategoryBlacklistController::INDEX_ROUTE,
                    ])
            )
        ;
    }
}
