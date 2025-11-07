<?php

namespace IdealoFeed\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductBlacklistType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('internal_code', TextType::class, [
            'label' => 'internal-code del prodotto',
            'required' => true,
        ])
            ->add('save', SubmitType::class, [
                'label' => 'Salva',
            ]);
    }
}
