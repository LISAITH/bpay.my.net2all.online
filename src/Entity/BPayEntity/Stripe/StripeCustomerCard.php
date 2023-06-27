<?php

namespace App\Entity\BPayEntity\Stripe;

class StripeCustomerCard
{
    private $id;
    private $object;
    private $address_city;
    private $address_country;
    private $address_line1;
    private $address_line1_check;
    private $address_line2;
    private $address_state;
    private $address_zip;
    private $address_zip_check;
    private $brand;
    private $country;
    private $customer;
    private $cvc_check;
    private $dynamic_last4;
    private $exp_month;
    private $exp_year;
    private $fingerprint;
    private $funding;
    private $last4;
    private $metadata;
    private $name;
    private $redaction;
    private $tokenization_method;

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
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
    public function getRedaction()
    {
        return $this->redaction;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @param mixed $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param mixed $redaction
     */
    public function setRedaction($redaction): void
    {
        $this->redaction = $redaction;
    }

    /**
     * @param mixed $object
     */
    public function setObject($object): void
    {
        $this->object = $object;
    }

    /**
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param mixed $brand
     */
    public function setBrand($brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
    public function getAddressCity()
    {
        return $this->address_city;
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
    public function getAddressLine1()
    {
        return $this->address_line1;
    }

    /**
     * @return mixed
     */
    public function getAddressLine1Check()
    {
        return $this->address_line1_check;
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
    public function getAddressState()
    {
        return $this->address_state;
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
    public function getAddressZipCheck()
    {
        return $this->address_zip_check;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getCvcCheck()
    {
        return $this->cvc_check;
    }

    /**
     * @return mixed
     */
    public function getDynamicLast4()
    {
        return $this->dynamic_last4;
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
    public function getExpYear()
    {
        return $this->exp_year;
    }

    /**
     * @return mixed
     */
    public function getFingerprint()
    {
        return $this->fingerprint;
    }

    /**
     * @return mixed
     */
    public function getFunding()
    {
        return $this->funding;
    }

    /**
     * @return mixed
     */
    public function getLast4()
    {
        return $this->last4;
    }

    /**
     * @return mixed
     */
    public function getTokenizationMethod()
    {
        return $this->tokenization_method;
    }

    /**
     * @param mixed $address_city
     */
    public function setAddressCity($address_city): void
    {
        $this->address_city = $address_city;
    }

    /**
     * @param mixed $address_country
     */
    public function setAddressCountry($address_country): void
    {
        $this->address_country = $address_country;
    }

    /**
     * @param mixed $address_line1
     */
    public function setAddressLine1($address_line1): void
    {
        $this->address_line1 = $address_line1;
    }

    /**
     * @param mixed $address_line1_check
     */
    public function setAddressLine1Check($address_line1_check): void
    {
        $this->address_line1_check = $address_line1_check;
    }

    /**
     * @param mixed $address_line2
     */
    public function setAddressLine2($address_line2): void
    {
        $this->address_line2 = $address_line2;
    }

    /**
     * @param mixed $address_state
     */
    public function setAddressState($address_state): void
    {
        $this->address_state = $address_state;
    }

    /**
     * @param mixed $address_zip
     */
    public function setAddressZip($address_zip): void
    {
        $this->address_zip = $address_zip;
    }

    /**
     * @param mixed $address_zip_check
     */
    public function setAddressZipCheck($address_zip_check): void
    {
        $this->address_zip_check = $address_zip_check;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @param mixed $cvc_check
     */
    public function setCvcCheck($cvc_check): void
    {
        $this->cvc_check = $cvc_check;
    }

    /**
     * @param mixed $dynamic_last4
     */
    public function setDynamicLast4($dynamic_last4): void
    {
        $this->dynamic_last4 = $dynamic_last4;
    }

    /**
     * @param mixed $exp_month
     */
    public function setExpMonth($exp_month): void
    {
        $this->exp_month = $exp_month;
    }

    /**
     * @param mixed $exp_year
     */
    public function setExpYear($exp_year): void
    {
        $this->exp_year = $exp_year;
    }

    /**
     * @param mixed $fingerprint
     */
    public function setFingerprint($fingerprint): void
    {
        $this->fingerprint = $fingerprint;
    }

    /**
     * @param mixed $funding
     */
    public function setFunding($funding): void
    {
        $this->funding = $funding;
    }

    /**
     * @param mixed $last4
     */
    public function setLast4($last4): void
    {
        $this->last4 = $last4;
    }

    /**
     * @param mixed $tokenization_method
     */
    public function setTokenizationMethod($tokenization_method): void
    {
        $this->tokenization_method = $tokenization_method;
    }
}