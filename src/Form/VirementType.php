<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class VirementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('iban', TextType::class, [
            'attr' => ['placeholder' => 'Iban', 'class' => 'form-control'],
            'label' => 'Iban',
            'required' => true,
        ]);
        $builder->add('beneficiaire', TextType::class, [
            'attr' => ['placeholder' => 'Bénéficiaire', 'class' => 'form-control'],
            'label' => 'Bénéficiaire',
            'required' => true,
        ]);
        $builder->add('montant', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => 'Montant', 'class' => 'form-control'],
            'label' => 'Montant', ]);

        $builder->add('ribFile', FileType::class, [
            'required' => false,
            'label' => 'RIB',
            'error_bubbling' => true,
            'mapped' => false,
        ]);
    }
}
