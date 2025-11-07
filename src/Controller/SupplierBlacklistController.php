<?php

namespace IdealoFeed\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;

use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use IdealoFeed\Repository\FeedRepository;

use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use IdealoFeed\Grid\DefinitionFactory\SupplierBlacklistGridDefinitionFactory as GridDefinitionFactory;
use IdealoFeed\Grid\Filters\SupplierBlacklistFilters as Filters;
use IdealoFeed\Form\Type\SupplierBlacklistType as Type;
use IdealoFeed\Constants;

class SupplierBlacklistController extends PrestaShopAdminController
{
    private FeedRepository $repository;
    private ResponseBuilder $responseBuilder;

    public const INDEX_ROUTE = 'idealofeed_supplier_blacklist_index';
    public const CREATE_ROUTE = 'idealofeed_supplier_blacklist_create';
    public const DELETE_ROUTE = 'idealofeed_supplier_blacklist_delete';
    public const DOMAIN = "supplier";

    public function __construct(
        FeedRepository $repository,
        ResponseBuilder $responseBuilder
    ) {
        $this->repository = $repository;
        $this->responseBuilder = $responseBuilder;
    }

    public function deleteAction(Request $request): RedirectResponse
    {
        // PrestaShop può mandarlo sia nel body che nella query string
        $id = $request->request->get('id')
            ?? $request->query->get('id');

        if ($id) {
            $this->repository->deleteItem((int) $id, self::DOMAIN);
            $this->addFlash('success', $this->trans('Record eliminato correttamente.', [], Constants::TRANSLATION_DOMAIN));
        } else {
            $this->addFlash('error', $this->trans('ID non trovato nella richiesta.', [], Constants::TRANSLATION_DOMAIN));
        }

        return $this->redirectToRoute(self::INDEX_ROUTE);
    }

    public function searchAction(
        Request $request,
        #[Autowire(service: Constants::SERVICE_PREFIX . '.grid.definition.factory.' . self::DOMAIN . "_blacklist")]
        GridDefinitionFactoryInterface $gridDefinitionFactory
    ) {
        return $this->responseBuilder->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            GridDefinitionFactory::GRID_ID,
            self::INDEX_ROUTE
        );
    }

    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        #[Autowire(service: Constants::SERVICE_PREFIX . '.grid.factory.' . self::DOMAIN . "_blacklist")]
        GridFactoryInterface $gridFactory,
        Filters $filters
    ): Response {
        $grid = $gridFactory->getGrid($filters);

        $addNewUrl = $this->generateUrl(self::CREATE_ROUTE);

        return $this->render(Constants::TEMPLATE_FOLDER . "/admin/" . self::DOMAIN . '_blacklist_grid.html.twig', [
            'grid' => $this->presentGrid($grid),
            'translation_domain' => Constants::TRANSLATION_DOMAIN,
            'add_new_href' => $addNewUrl,
        ]);
    }

    public function createAction(Request $request): Response
    {
        $form = $this->createForm(Type::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->createItem($form->getData(), self::DOMAIN);

            $this->addFlash('success', $this->trans('Record creato correttamente.', [], Constants::TRANSLATION_DOMAIN));
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }

        return $this->render(Constants::TEMPLATE_FOLDER . "/admin/" . self::DOMAIN . '_blacklist_create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
