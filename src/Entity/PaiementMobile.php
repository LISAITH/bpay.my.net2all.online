<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\PaiementMobileController;

#[ApiResource(
    collectionOperations: ['paiement_mobile_v1' => [
        'path' => '/paiement-mobile/create/{user_id}',
        'method' => 'POST',
        'input' => PaiementMobile::class,
        'controller' => PaiementMobileController::class,
        'openapi_context' => [
            'summary' => 'Permet d\'initier un paiement mobile.',
            'responses' => [
                '201' => [
                    'description' => 'OK',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'reference' => [
                                        'type' => 'string',
                                        'description' => 'La référence unique',
                                    ],
                                    'status' => [
                                        'type' => 'string',
                                        'description' => 'Le status de la transaction',
                                    ],
                                    'payload' => [
                                        'type' => 'string',
                                        'description' => 'Reférence unique. Fait référence à la propriété guid de l\'entité BPayMomoTransaction pour le cas d\'un paiement par mobile money',
                                    ],
                                    'reason' => [
                                        'type' => 'string',
                                        'description' => '',
                                    ],
                                    'transactionRef' => [
                                        'type' => 'string',
                                        'description' => 'La référence de la transaction. Cas d\'une transaction à succès',
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
    ], ],
    itemOperations: [],
    paginationEnabled: false
)]
class PaiementMobile
{
    #[ApiProperty(identifier: true, description: 'La référence unique')]
    private string $reference;
    #[ApiProperty(description: 'Le numéro de téléphone du client')]
    private int $numero;

    #[ApiProperty(description: 'L\'adresse email du client')]
    private string $email;

    #[ApiProperty(description: 'Nom et prénoms du client')]
    private string $holderName;

    #[ApiProperty(description: 'Le montant de la transaction')]
    private int $montant;

    #[ApiProperty(description: 'La devise. Par défault XOF')]
    private string $currency;

    #[ApiProperty(description: 'L\'opérateur. Ex: MTN,MOOV,CELTIIS')]
    private string $operateur;

    #[ApiProperty(description: 'Le motif de la transaction')]
    private string $motif;

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function getMotif(): string
    {
        return $this->motif;
    }

    public function getHolderName(): string
    {
        return $this->holderName;
    }

    public function setMontant(int $montant): void
    {
        $this->montant = $montant;
    }

    public function setMotif(string $motif): void
    {
        $this->motif = $motif;
    }

    public function setHolderName(string $holderName): void
    {
        $this->holderName = $holderName;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    public function setOperateur(string $operateur): void
    {
        $this->operateur = $operateur;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNumero(): int
    {
        return $this->numero;
    }

    public function getOperateur(): string
    {
        return $this->operateur;
    }
}
