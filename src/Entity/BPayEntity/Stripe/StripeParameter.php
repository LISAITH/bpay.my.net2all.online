<?php

namespace App\Entity\BPayEntity\Stripe;

class StripeParameter
{
    private ?string $secretKey;
    private ?string $publicKey;

    public function setSecretKey(?string $secretKey): void
    {
        $this->secretKey = $secretKey;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    public function setPublicKey(?string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }
}
