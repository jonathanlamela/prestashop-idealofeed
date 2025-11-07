<?php

namespace IdealoFeed\Controller;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use IdealoFeed\Form\FormHandler\ConfigurationFormHandler;
use IdealoFeed\Form\Type\ConfigurationFormType;

class ConfigurationController extends PrestaShopAdminController
{
    private FormFactoryInterface $formFactory;
    private ConfigurationFormHandler $formHandler;

    public function __construct(
        FormFactoryInterface $formFactory,
        #[Autowire(service: 'idealofeed.form.handler.configuration')]
        ConfigurationFormHandler $formHandler
    ) {
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
    }


    public function index(Request $request)
    {
        $formData = $this->formHandler->getData();

        $form = $this->formFactory->create(ConfigurationFormType::class, $formData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->formHandler->save($form->getData());

            $this->addFlash(
                'success',
                'Impostazioni salvate con successo'
            );
        }

        return $this->render('@Modules/idealofeed/views/templates/admin/configuration_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
