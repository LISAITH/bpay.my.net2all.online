<?php

namespace App\Entity\BPayEntity\Stripe;

class StripePaymentMethodCard
{
    private $id;
    private $billing_details;
    private $card;
    private $created;
    private $customer;
    private $livemode;
    private $metadata;
    private $redaction;
    private $type;

    public function __construct()
    {
        $this->card = new StripeCustomerCard();
        $this->billing_details = new StripeCustomerAddress();
    }

    /**
     * @param mixed $redaction
     */
    public function setRedaction($redaction): void
    {
        $this->redaction = $redaction;
    }

    /**
     * @param mixed $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param mixed $livemode
     */
    public function setLivemode($livemode): void
    {
        $this->livemode = $livemode;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created): void
    {
        $this->created = $created;
    }

    /**
     * @return mixed
     */
    public function getRedaction()
    {
        return $this->redaction;
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
    public function getLivemode()
    {
        return $this->livemode;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return StripeCustomerAddress
     */
    public function getBillingDetails(): StripeCustomerAddress
    {
        return $this->billing_details;
    }

    /**
     * @return StripeCustomerCard
     */
    public function getCard(): StripeCustomerCard
    {
        return $this->card;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param StripeCustomerAddress $billing_details
     */
    public function setBillingDetails(StripeCustomerAddress $billing_details): void
    {
        $this->billing_details = $billing_details;
    }

    /**
     * @param StripeCustomerCard $card
     */
    public function setCard(StripeCustomerCard $card): void
    {
        $this->card = $card;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}