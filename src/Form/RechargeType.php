<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechargeType extends AbstractType
{
    // public function buildForm(FormBuilderInterface $builder, array $options)
    // {
    //     $builder->add('montant', NumberType::class, [
    //         'required' => true,
    //         'html5' => true,
    //         'attr' => ['placeholder' => 'Montant à recharger', 'class' => 'form-control '],
    //         'label' => 'Montant à recharger', ]);

    //         $builder->add('paymentMethodType', HiddenType::class, [
    //         'required' => true,
    //         'data' => $options['paymentMethodType'],
    //     ]);
    //     $builder->add('transactionRef', HiddenType::class, [
    //         'required' => true,
    //         'data' => $options['transactionRef'],
    //     ]);
    // }

    // public function configureOptions(OptionsResolver $resolver)
    // {
    //     $resolver->setDefaults([
    //         'paymentMethodType' => null,
    //         'transactionRef' => null,
    //     ]);
    // }
}