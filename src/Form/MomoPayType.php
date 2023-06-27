<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MomoPayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('number', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => 'Numéro', 'class' => 'form-control'],
            'label' => 'Numéro Momo', ]);

        $builder->add('amount', HiddenType::class, [
                'required' => true,
                'data' => $options['amount'],
            ]
        );
        $builder->add('isYourAccount', HiddenType::class, [
            'label' => 'Le compte vous m\'appartient',
            'data' => 'false',
        ]);
        $builder->add('holderName', TextType::class, [
            'required' => false,
            'attr' => ['placeholder' => 'Titulaire du compte', 'class' => 'form-control'],
            'label' => 'Titulaire du compte Momo',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'amount' => null,
        ]);
    }
}
