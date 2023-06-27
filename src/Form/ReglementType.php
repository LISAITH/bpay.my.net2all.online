<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class ReglementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('montant', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => '', 'class' => 'form-control '],
            'label' => 'Montant à payer', ]);

        $builder->add('payment_motif', TextareaType::class, [
            'required' => true,
            'attr' => ['placeholder' => '', 'class' => 'form-control'],
            'label' => 'Motif', ]);
    }
}