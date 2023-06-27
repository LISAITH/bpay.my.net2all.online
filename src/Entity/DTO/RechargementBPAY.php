<?php

namespace App\Entity\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\RechargementCompteController;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
    'crediter_compte' => [
        'method' => 'POST',
        'path' => '/rechargement-compte',
        'input' => RechargementBPAY::class,
        'controller' => RechargementCompteController::class,
        'openapi_context' => [
            'summary' => 'Cette opération permet de recharger un compte principal B-PAY',
            'responses' => [
                '200' => [
                    'description' => 'OK',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'numeroCompte' => [
                                        'type' => 'string',
                                    ],
                                    'montant' => [
                                        'type' => 'number',
                                    ],
                                    'montantDisponible' => [
                                        'type' => 'number',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'parameters' => [],
        ],
        'swagger_context' => ['parameters' => []],
    ],
],
    itemOperations: [],
)]
class RechargementBPAY
{
    #[ApiProperty(identifier: true)]
    private string $reference;

    #[Assert\NotBlank(message: 'Le numéro de compte  est obligatoire')]
    #[ApiProperty(description: 'Le numéro du compte à recharger')]
    private string $numeroCompte;

    #[Assert\NotBlank(message: 'Le montant à recharger obligatoire')]
    #[Assert\Positive(message: 'Le montant à recharger doit etre un nombre positif')]
    #[Assert\GreaterThan(value: 0, message: 'Le montant à recharger doit etre supérieur à zéro')]
    #[ApiProperty(description: 'Le montant à recharger')]
    private int $montant;

    #[ApiProperty(description: 'La référence du point de vente si elle existe')]
    private int $numeroPointVente;

    public function setMontant(int $montant): void
    {
        $this->montant = $montant;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function setNumeroPointVente(int $numeroPointVente): void
    {
        $this->numeroPointVente = $numeroPointVente;
    }

    public function getNumeroPointVente(): int
    {
        return $this->numeroPointVente;
    }

    public function setNumeroCompte(string $numeroCompte): void
    {
        $this->numeroCompte = $numeroCompte;
    }

    public function getNumeroCompte(): string
    {
        return $this->numeroCompte;
    }
}
