<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SecretAuthType extends  AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('one', TextType::class, [
            'attr' => ['class' => 'input_pad pad_1 no-disabled form-control-custom-pad', 'data-index' => '1', 'max' => 1, 'placeholder' => '0', 'pattern' => '[0-9]*',
                'size' => 1, 'max_length' => 1, ],
            'required' => true,
        ]);
        $builder->add('two', TextType::class, [
            'attr' => ['class' => 'input_pad pad_2 form-control-custom-pad', 'data-index' => '2', 'placeholder' => '0',  'pattern' => '[0-9]*',
                'size' => 1, ],
            'required' => true,
        ]);
        $builder->add('three', TextType::class, [
            'attr' => ['class' => 'input_pad pad_3 form-control-custom-pad', 'data-index' => '3', 'placeholder' => '0',  'pattern' => '[0-9]*',
                'size' => 1, ],
            'required' => true,
        ]);
        $builder->add('four', TextType::class, [
            'attr' => ['class' => 'input_pad pad_4 form-control-custom-pad', 'data-index' => '4', 'placeholder' => '0', 'pattern' => '[0-9]*',
                'size' => 1, 'max_length' => 1, ],
            'required' => true,
        ]);
    }
}