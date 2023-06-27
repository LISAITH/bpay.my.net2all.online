<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('accountNumber', TextType::class, [
            'required' => true,
            'attr' => ['placeholder' => 'Numéro de compte', 'class' => 'form-control form-error'],
            'label' => 'Montant à recharger', ]);

        $builder->add('amount', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => 'Montant', 'class' => 'form-control form-error'],
            'label' => 'Montant à transférer', ]);
    }
}
