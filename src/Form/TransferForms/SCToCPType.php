<?php

namespace App\Form\TransferForms;

use App\Entity\SousCompte;
use App\Repository\SousCompteRepository;
use App\Service\APIService\ApiService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SCToCPType extends AbstractType
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('subAccount', EntityType::class, [
            'class' => SousCompte::class,
            'query_builder' => function (SousCompteRepository $er) use ($options) {
                return $er->createQueryBuilder('s')
                    ->where('s.compteBPay=:ecash')->setParameter('ecash', $options['eCash']);
            },
            'choice_value' => function (?SousCompte $entity) {
                return null != $entity ? $entity->getNumeroSousCompte() : '';
            },
            'choice_label' => function (SousCompte $entity) {
                $service = $this->apiService->getServiceById($entity->getServiceId());
                return sprintf('%s - %s - %s XOF', strtoupper($service->getLibelle()), strtoupper($entity->getNumeroSousCompte()), $entity->getSolde());
            },
            'attr' => ['class' => 'form-control form-error input-lg'],
           'required' => true,
            'placeholder' => 'Choisissez le sous compte à débiter',
            'label' => 'Choisissez le sous compte à débiter',
            'error_bubbling' => true,
        ]);

        $builder->add('amount', NumberType::class, [
            'required' => true,
            'html5' => true,
            'attr' => ['placeholder' => 'Montant', 'class' => 'form-control form-error input-lg'],
            'label' => 'Montant à transférer', ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'eCash' => null,
        ]);
    }
}
