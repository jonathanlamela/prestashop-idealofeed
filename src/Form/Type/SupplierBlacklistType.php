<?php

namespace IdealoFeed\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use IdealoFeed\Repository\FeedRepository as Repository;

class SupplierBlacklistType extends AbstractType
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $choices = [];

        foreach ($this->repository->getSuppliersForSelect() as $row) {
            $choices["({$row['id_supplier']}) {$row['name']}"] = $row['id_supplier'];
        }

        $builder->add('id_supplier', ChoiceType::class, [
            'label' => 'Fornitore',
            'choices' => $choices,
            'required' => true,
            'attr' => [
                'class' => 'select2', // 🔍 attiva la ricerca
                'data-placeholder' => 'Seleziona un fornitore',
            ],
        ])
            ->add('save', SubmitType::class, [
                'label' => 'Salva',
            ]);
    }
}
