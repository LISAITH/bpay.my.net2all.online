<?php

namespace App\Entity\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\TransfertBPAYController;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'CP_TO_CP' => [
            'method' => 'POST',
            'path' => '/transfert-bpay',
            'input' => TransertBPAY::class,
            'controller' => TransfertBPAYController::class,
            'openapi_context' => [
                'summary' => 'Cette opération permet d\'initier un transferer  B-PAY',
                'description' => 'Cette opération permet le transfert entre comptes principaux B-PAY.Spécifier pour le type de transfert CP_TO_CP pour un transfert entre compte à compte, CP_TO_SC pour un transfert un compte principal et un sous-compte, SC_TO_CP pour un transfert entre un sous-compte et un compte principal et SC_TO_SC pour un transfert entre deux sous comptes du même service',
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'numeroCompteEnvoyeur' => [
                                            'type' => 'string',
                                        ],
                                        'numeroCompteReceveur' => [
                                            'type' => 'string',
                                        ],
                                        'montant' => [
                                            'type' => 'number',
                                        ],
                                        'frais' => [
                                            'type' => 'number',
                                        ],
                                        'motif' => [
                                            'type' => 'string',
                                        ],
                                        'reference' => [
                                            'type' => 'string',
                                            'description' => 'La référence de la transaction',
                                        ],
                                        'devise' => [
                                            'type' => 'string',
                                            'description' => 'La devise',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    '400' => [
                        'description' => 'Bad Request',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'parameters' => [],
        ],
        'PCP_TO_SC' => [
            'method' => 'POST',
            'path' => '/paiement-bpay',
            'security' => 'is_granted("ROLE_USER")',
            'input' => TransertBPAY::class,
            'controller' => TransfertBPAYController::class,
            'openapi_context' => [
                'summary' => 'Cette opération permet d\'initier un paiement B-PAY. Débite le compte principal du client et crédite le sous compte du service qu\'il consomme',
                'description' => 'Cette opération permet d\'effectuer un paiement B-PAY. Débite le compte principal du client et crédite le sous compte du service qu\'il consomme. Spécifier pour le type de transfert la valeur PAIEMENT_BPAY',
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'numeroCompteEnvoyeur' => [
                                            'type' => 'number',
                                        ],
                                        'numeroSousCompteReceveur' => [
                                            'type' => 'string',
                                        ],
                                        'montantEnvoyer' => [
                                            'type' => 'number',
                                        ],
                                        'frais' => [
                                            'type' => 'number',
                                        ],
                                        'motif' => [
                                            'type' => 'string',
                                        ],
                                        'reference' => [
                                            'type' => 'string',
                                            'description' => 'La référence de la transaction',
                                        ],
                                        'devise' => [
                                            'type' => 'string',
                                            'description' => 'La devise',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'parameters' => [],
        ],
    ],
    itemOperations: [],
    normalizationContext: ['groups' => ['read:collection']],
)]
class TransertBPAY
{
    #[ApiProperty(identifier: true)]
    private string $reference;
    #[Assert\NotBlank(message: 'Le numéro du compte envoyeur est obligatoire.')]
    #[ApiProperty(description: 'Le numéro du compte envoyeur')]
    private string $numeroCompteEnvoyeur;

    #[Assert\NotBlank(message: 'Le numéro du compte receveur est obligatoire.')]
    #[ApiProperty(description: 'Le numéro du compte receveur')]
    private string $numeroCompteReceveur;

    #[Assert\NotBlank(message: 'Le montant à transférer')]
    #[Assert\GreaterThan(value: 0, message: 'Le montant à transférer doit etre supérieur à zéro.')]
    #[Assert\Positive(message: 'Le montant à transférer doit etre nombre positif.')]
    #[ApiProperty(description: 'Le montant à transférer')]
    private int $montant;

    #[Assert\NotBlank(message: 'Le type du transfert est obligatoire.')]
    #[ApiProperty(description: 'Le type du transfert: CP_TO_CP pour un transfert entre compte à compte, CP_TO_SC pour un transfert un compte principal et un sous-compte, SC_TO_CP pour un transfert entre un sous-compte et un compte principal et SC_TO_SC pour un transfert entre deux sous comptes du même service')]
    private string $typeTransfert;

    #[Assert\NotBlank(message: 'Le motif du transfert est obligatoire')]
    #[ApiProperty(description: 'Le motif du transfert')]
    private string $motif;

    public function setMotif(string $motif): void
    {
        $this->motif = $motif;
    }

    public function getMotif(): string
    {
        return $this->motif;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): void
    {
        $this->montant = $montant;
    }

    public function setNumeroCompteEnvoyeur(string $numeroCompteEnvoyeur): void
    {
        $this->numeroCompteEnvoyeur = $numeroCompteEnvoyeur;
    }

    public function getNumeroCompteEnvoyeur(): string
    {
        return $this->numeroCompteEnvoyeur;
    }

    public function setNumeroCompteReceveur(string $numeroCompteReceveur): void
    {
        $this->numeroCompteReceveur = $numeroCompteReceveur;
    }

    public function getNumeroCompteReceveur(): string
    {
        return $this->numeroCompteReceveur;
    }

    public function getTypeTransfert(): string
    {
        return $this->typeTransfert;
    }

    public function setTypeTransfert(string $typeTransfert): void
    {
        $this->typeTransfert = $typeTransfert;
    }
}
