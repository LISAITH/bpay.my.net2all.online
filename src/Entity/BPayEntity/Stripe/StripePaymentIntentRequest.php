<?php

namespace App\Entity\BPayEntity\Stripe;

class StripePaymentIntentRequest
{
    private $amount;

    private $currency;
    private $automaticEnable;

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAutomaticEnable()
    {
        return $this->automaticEnable;
    }

    /**
     * @param mixed $automaticEnable
     */
    public function setAutomaticEnable($automaticEnable): void
    {
        $this->automaticEnable = $automaticEnable;
    }
}