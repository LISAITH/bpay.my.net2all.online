<?php

namespace App\Form\TransferForms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransfertVersSousCompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class, [
            'attr' => ['placeholder' => 'Email', 'class' => 'form-control form-error input-lg'],
            'label' => 'Email',
            'required' => true,
        ]);
        $builder->add('amount', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => 'Montant à envoyer', 'class' => 'form-control form-error input-lg'],
            'label' => 'Montant à envoyer', ]);

        $builder->add('subAccountId', HiddenType::class, [
            'required' => true,
            'data' => $options['subAccountId']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
[
    'subAccountId' => null
]
        );
    }
}