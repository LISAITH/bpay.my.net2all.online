<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\LoadParametersController;

#[ApiResource(
    collectionOperations: [
   'parametres' => [
    'method' => 'GET',
    'path' => '/parametres',
    'controller' => LoadParametersController::class,
    'pagination_enable' => false,
    'openapi_context' => [
        'summary' => 'Cette opération permet de récupérer les paramètres de configuration. Clés stripe etc..',
        'parameters' => [],
        'responses' => [
            '200' => [
                'description' => 'OK',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'stripeSecretKey' => [
                                    'type' => 'string',
                                    'description' => 'Clé privée stripe',
                                ],
                                'stripePublicKey' => [
                                    'type' => 'string',
                                    'description' => 'Clé publique stripe',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],

    ],
]],
    itemOperations: [], paginationEnabled: false)]
class Parametres
{
    #[ApiProperty(identifier: true)]
    private string $uuid;
    #[ApiProperty(description: 'Clé secrete stripe')]
    private string $stripeSecretKey;
    #[ApiProperty(description: 'Clé public stripe')]
    private string $stripePublicKey;

    #[ApiProperty(description: 'Clé secrete payPal')]
    private string $paypalSecretKey;

    #[ApiProperty(description: 'Clé public payPal')]
    private string $paypalPublicKey;

    public function getPaypalPrivateKey(): string
    {
        return $this->paypalPrivateKey;
    }



    public function getPaypalPublicKey(): string
    {
        return $this->paypalPublicKey;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getStripePublicKey(): string
    {
        return $this->stripePublicKey;
    }

    public function setPaypalPublicKey(string $paypalPublicKey): void
    {
        $this->paypalPublicKey = $paypalPublicKey;
    }

    public function setStripePublicKey(string $stripePublicKey): void
    {
        $this->stripePublicKey = $stripePublicKey;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getPaypalSecretKey(): string
    {
        return $this->paypalSecretKey;
    }

    public function getStripePrivateKey(): string
    {
        return $this->stripePrivateKey;
    }

    public function getStripeSecretKey(): string
    {
        return $this->stripeSecretKey;
    }

    public function setPaypalPrivateKey(string $paypalPrivateKey): void
    {
        $this->paypalPrivateKey = $paypalPrivateKey;
    }

    public function setPaypalSecretKey(string $paypalSecretKey): void
    {
        $this->paypalSecretKey = $paypalSecretKey;
    }

    public function setStripePrivateKey(string $stripePrivateKey): void
    {
        $this->stripePrivateKey = $stripePrivateKey;
    }

    public function setStripeSecretKey(string $stripeSecretKey): void
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }
}
