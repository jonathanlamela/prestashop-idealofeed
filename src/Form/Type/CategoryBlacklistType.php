<?php

namespace IdealoFeed\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use IdealoFeed\Repository\FeedRepository as Repository;

class CategoryBlacklistType extends AbstractType
{
    private Repository $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        foreach ($this->repository->getCategoriesForSelect() as $row) {
            $choices["({$row['id']}) {$row['name']}"] = $row['id'];
        }

        $builder->add('id_category', ChoiceType::class, [
            'label' => 'Categoria',
            'choices' => $choices,
            'required' => true,
            'attr' => [
                'class' => 'select2', // 🔍 attiva la ricerca
                'data-placeholder' => 'Seleziona una categoria',
            ],
        ])
            ->add('save', SubmitType::class, [
                'label' => 'Salva',
            ]);
    }
}
