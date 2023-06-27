<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GeneratePaymentIntent;
use App\Api\Controller\PaiementCarteV2Controller;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    collectionOperations: [
        'generate-payment-intent' => [
            'method' => 'POST',
            'path' => '/generate-stripe-payment-intent-key',
            'controller' => GeneratePaymentIntent::class,
            'denormalization_context' => ['groups' => ['post:intent']],
            'openapi_context' => [
                'summary' => '',
                'description' => '',
                'responses' => [
                    '200' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'clientSecret' => [
                                            'type' => 'string',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
        /*'paiement_carte_credit' => [
        'path' => '/paiement-carte/create_v1',
        'method' => 'POST',
        'input' => PaiementCarte::class,
        'controller' => PaiementCarteV1Controller::class,
       'openapi_context' => [
           'summary' => 'Permet d\'initier un paiement par carte bancaire',
           'responses' => [
               '201' => [
                   'description' => 'OK',
                   'content' => [
                       'application/json' => [
                           'schema' => [
                               'type' => 'object',
                               'properties' => [
                                   'numeroCarte' => [
                                       'type' => 'string',
                                       'description' => 'Le numéro de la carte',
                                   ],
                                   'brand' => [
                                       'type' => 'string',
                                       'description' => 'La marque de la carte: VISA, MASTERCARD,MAESTRO',
                                   ],
                                   'fingerprint' => [
                                       'type' => 'string',
                                       'description' => 'L\'empreinte de la transaction',
                                   ],
                                   'payment_method' => [
                                       'type' => 'string',
                                       'description' => 'Identifiant unique de la transaction',
                                   ],
                                   'status' => [
                                       'type' => 'string',
                                       'description' => 'Statut de la transaction',
                                   ],
                                   'holderName' => [
                                       'type' => 'string',
                                       'description' => 'Le titulaire de la carte',
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
    ],*/],
    itemOperations: [
        'retrieve_credit_card_paiement' => [
            'path' => '/paiement-carte/{paymentIntentKey}',
            'method' => 'GET',
            'controller' => PaiementCarteV2Controller::class,
            'read' => false,
            'openapi_context' => [
                'summary' => 'Permet d\'initier un paiement par stripe',
                'description' => 'Le formulaire de paiement doit etre initialisé en utilisant l\'SDK de stripe. Une fois
                 la validation effectuée, recupérer le token généré par stripe et passer le à cette fonction pour récupérer le détail concernant la transaction',
                'responses' => [
                    '201' => [
                        'description' => 'OK',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'numeroCarte' => [
                                            'type' => 'string',
                                            'description' => 'Le numéro de la carte',
                                        ],
                                        'cvv' => [
                                            'type' => 'string',
                                            'description' => 'Le CVV de la carte',
                                        ],
                                        'expMonth' => [
                                            'type' => 'string',
                                            'description' => 'Le mois d\'expiration de la carte',
                                        ],
                                        'expYear' => [
                                            'type' => 'string',
                                            'description' => 'L\'année d\'expiration de la carte',
                                        ],
                                        'brand' => [
                                            'type' => 'string',
                                            'description' => 'La marque de la carte: VISA, MASTERCARD,MAESTRO',
                                        ],
                                        'fingerprint' => [
                                            'type' => 'string',
                                            'description' => 'L\'empreinte de la transaction',
                                        ],
                                        'payment_method' => [
                                            'type' => 'string',
                                            'description' => 'Identifiant unique de la transaction',
                                        ],
                                        'status' => [
                                            'type' => 'string',
                                            'description' => 'Statut de la transaction',
                                        ],
                                        'holderName' => [
                                            'type' => 'string',
                                            'description' => 'Le titulaire de la carte',
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
        ],
    ],
    paginationEnabled: false
)]
class PaiementCarte
{
    #[ApiProperty(identifier: true)]
    private string $paymentIntentKey;
    #[ApiProperty(description: 'Le numéro de la carte bancaire')]
    private string $numeroCarte;

    #[ApiProperty(description: 'Le numéro CVV')]
    private string $cvv;
    #[ApiProperty(description: 'Le mois d\'expiration.')]
    private string $expDay;
    #[ApiProperty(description: 'L\'année expiration.')]
    private string $expMonth;

    #[ApiProperty(description: 'Nom du tiltulaire de la carte.')]
    private string $holderName;

    #[ApiProperty(description: 'Le montant à payer.')]
    #[Groups(['post:intent'])]
    private int $montant;

    #[ApiProperty(description: 'Le motif.')]
    private string $motif;

    public function setHolderName(string $holderName): void
    {
        $this->holderName = $holderName;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function setMotif(string $motif): void
    {
        $this->motif = $motif;
    }

    public function setMontant(int $montant): void
    {
        $this->montant = $montant;
    }

    public function setCvv(string $cvv): void
    {
        $this->cvv = $cvv;
    }

    public function setExpDay(string $expDay): void
    {
        $this->expDay = $expDay;
    }

    public function setExpMonth(string $expMonth): void
    {
        $this->expMonth = $expMonth;
    }

    public function setNumeroCarte(string $numeroCarte): void
    {
        $this->numeroCarte = $numeroCarte;
    }

    public function getHolderName(): string
    {
        return $this->holderName;
    }

    public function getMotif(): string
    {
        return $this->motif;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getMontant(): int
    {
        return $this->montant;
    }

    public function getExpMonth(): string
    {
        return $this->expMonth;
    }

    public function getPaymentIntentKey(): string
    {
        return $this->paymentIntentKey;
    }

    public function setPaymentIntentKey(string $paymentIntentKey): void
    {
        $this->paymentIntentKey = $paymentIntentKey;
    }

    public function getCvv(): string
    {
        return $this->cvv;
    }

    public function getExpDay(): string
    {
        return $this->expDay;
    }

    public function getNumeroCarte(): string
    {
        return $this->numeroCarte;
    }
}
