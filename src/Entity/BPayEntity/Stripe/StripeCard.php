<?php

namespace App\Entity\BPayEntity\Stripe;

use App\Utils\constants;

class StripeCard
{
    private $cardType;
    private $cardNumber;
    private $exp_month;
    private $exp_year;
    private $cvc;
    private $currency;
    private $name;
    private $metadata;
    private $default_for_currency;
    private $address_line1;
    private $address_line2;
    private $address_city;
    private $address_state;
    private $address_zip;
    private $address_country;

    public function __construct()
    {
        $this->cardType = \strtolower(constants::CARDETYPE);
    }

    /**
     * @param mixed $exp_year
     */
    public function setExpYear($exp_year): void
    {
        $this->exp_year = $exp_year;
    }

    /**
     * @param mixed $exp_month
     */
    public function setExpMonth($exp_month): void
    {
        $this->exp_month = $exp_month;
    }

    /**
     * @param mixed $address_zip
     */
    public function setAddressZip($address_zip): void
    {
        $this->address_zip = $address_zip;
    }

    /**
     * @param mixed $address_state
     */
    public function setAddressState($address_state): void
    {
        $this->address_state = $address_state;
    }

    /**
     * @param mixed $address_line2
     */
    public function setAddressLine2($address_line2): void
    {
        $this->address_line2 = $address_line2;
    }

    /**
     * @param mixed $address_line1
     */
    public function setAddressLine1($address_line1): void
    {
        $this->address_line1 = $address_line1;
    }

    /**
     * @param mixed $address_country
     */
    public function setAddressCountry($address_country): void
    {
        $this->address_country = $address_country;
    }

    /**
     * @param mixed $address_city
     */
    public function setAddressCity($address_city): void
    {
        $this->address_city = $address_city;
    }

    /**
     * @return mixed
     */
    public function getExpYear()
    {
        return $this->exp_year;
    }

    /**
     * @return mixed
     */
    public function getExpMonth()
    {
        return $this->exp_month;
    }

    /**
     * @return mixed
     */
    public function getAddressZip()
    {
        return $this->address_zip;
    }

    /**
     * @return mixed
     */
    public function getAddressState()
    {
        return $this->address_state;
    }

    /**
     * @return mixed
     */
    public function getAddressLine2()
    {
        return $this->address_line2;
    }

    /**
     * @return mixed
     */
    public function getAddressLine1()
    {
        return $this->address_line1;
    }

    /**
     * @return mixed
     */
    public function getAddressCountry()
    {
        return $this->address_country;
    }

    /**
     * @return mixed
     */
    public function getAddressCity()
    {
        return $this->address_city;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

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
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @return string
     */
    public function getCardType(): string
    {
        return $this->cardType;
    }

    /**
     * @return mixed
     */
    public function getCvc()
    {
        return $this->cvc;
    }

    /**
     * @return mixed
     */
    public function getDefaultForCurrency()
    {
        return $this->default_for_currency;
    }

    /**
     * @param mixed $cardNumber
     */
    public function setCardNumber($cardNumber): void
    {
        $this->cardNumber = $cardNumber;
    }

    /**
     * @param string $cardType
     */
    public function setCardType(string $cardType): void
    {
        $this->cardType = $cardType;
    }

    /**
     * @param mixed $cvc
     */
    public function setCvc($cvc): void
    {
        $this->cvc = $cvc;
    }

    /**
     * @param mixed $default_for_currency
     */
    public function setDefaultForCurrency($default_for_currency): void
    {
        $this->default_for_currency = $default_for_currency;
    }
}
