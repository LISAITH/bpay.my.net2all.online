<?php

namespace App\Form\TransferForms;

use App\Utils\constants;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class TransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('transferType', ChoiceType::class, [
            'choices' => [
                'Compte principal - Compte principal' => constants::CP_TO_CP,
                'Compte principal - Sous compte' => constants::CP_TO_SC,
                'Sous compte - Compte principal' => constants::SC_TO_CP,
                'Sous compte - Sous compte' => constants::SC_TO_SC,
            ],
            'label' => 'Choisissez le type de transfert',
            'required' => true,
            'placeholder' => 'Choisissez le type de transfert',
        ]);
    }
}
