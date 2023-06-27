<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ByPayCreditCardTransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ByPayCreditCardTransactionRepository::class)
 * @ORM\Table(name="bpay_credit_card_transaction")
 */

class BPayCreditCardTransaction
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="guid", type="string", nullable=true)
     */
    private $guid;
    /**
     * @ORM\Column(name="reference", type="string", nullable=true)
     */
    private $reference;
    /**
     * @ORM\Column(name="card_number", type="string", nullable=true)
     */
    private $cardNumber;
    /**
     * @ORM\Column(name="exp_month", type="string", nullable=true)
     */
    private $exp_month;
    /**
     * @ORM\Column(name="exp_year", type="string", nullable=true)
     */
    private $exp_year;
    /**
     * @ORM\Column(name="cvc_check", type="string", nullable=true)
     */
    private $cvc_check;
    /**
     * @ORM\Column(name="brand", type="string", nullable=true)
     */
    private $brand;
    /**
     * @ORM\Column(name="country", type="string", nullable=true)
     */
    private $country;
    /**
     * @ORM\Column(name="fingerprint", type="string", nullable=true)
     */
    private $fingerprint;
    /**
     * @ORM\Column(name="funding", type="string", nullable=true)
     */
    private $funding;
    /**
     * @ORM\Column(name="last4", type="string", nullable=true)
     */
    private $last4;
    /**
     * @ORM\Column(name="amount", type="string", nullable=true)
     */
    private $amount;
    /**
     * @ORM\Column(name="automatic_payment_methods", type="string", nullable=true)
     */
    private $automaticPaymentMethods;
    /**
     * @ORM\Column(name="client_secret", type="string", nullable=true)
     */
    private $clientSecret;
    /**
     * @ORM\Column(name="created", type="string", nullable=true)
     */
    private $created;
    /**
     * @ORM\Column(name="payment_method", type="string", nullable=true)
     */
    private $paymentMethod;
    /**
     * @ORM\Column(name="status", type="string", nullable=true)
     */
    private $status;
    /**
     * @ORM\Column(name="cancellation_reason", type="string", nullable=true)
     */
    private $cancellationReason;
    /**
     * @ORM\Column(name="canceled_at", type="string", nullable=true)
     */
    private $canceledAt;
    /**
     * @ORM\Column(name="capture_method", type="string", nullable=true)
     */
    private $captureMethod;
    /**
     * @ORM\Column(name="confirmation_method", type="string", nullable=true)
     */
    private $confirmationMethod;
    /**
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    private $currency;
    /**
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(name="last_payment_error", type="string", nullable=true)
     */
    private $lastPaymentError;
    /**
     * @ORM\Column(name="object", type="string", nullable=true)
     */
    private $object;

    /**
     * @ORM\Column(name="holderName", type="string", nullable=true)
     */
    private $holderName;

    /**
     * @ORM\Column(name="payment_method_type", type="string", nullable=true)
     */
    private $paymentMethodType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCardNumber(): ?string
    {
        return $this->cardNumber;
    }

    public function setCardNumber(?string $cardNumber): self
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }

    public function getExpMonth(): ?string
    {
        return $this->exp_month;
    }

    public function setExpMonth(?string $exp_month): self
    {
        $this->exp_month = $exp_month;

        return $this;
    }

    public function getExpYear(): ?string
    {
        return $this->exp_year;
    }

    public function setExpYear(?string $exp_year): self
    {
        $this->exp_year = $exp_year;

        return $this;
    }

    public function getCvcCheck(): ?string
    {
        return $this->cvc_check;
    }

    public function setCvcCheck(?string $cvc_check): self
    {
        $this->cvc_check = $cvc_check;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFingerprint(): ?string
    {
        return $this->fingerprint;
    }

    public function setFingerprint(?string $fingerprint): self
    {
        $this->fingerprint = $fingerprint;

        return $this;
    }

    public function getFunding(): ?string
    {
        return $this->funding;
    }

    public function setFunding(?string $funding): self
    {
        $this->funding = $funding;

        return $this;
    }

    public function getLast4(): ?string
    {
        return $this->last4;
    }

    public function setLast4(?string $last4): self
    {
        $this->last4 = $last4;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAutomaticPaymentMethods(): ?string
    {
        return $this->automaticPaymentMethods;
    }

    public function setAutomaticPaymentMethods(?string $automaticPaymentMethods): self
    {
        $this->automaticPaymentMethods = $automaticPaymentMethods;

        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(?string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    public function getCreated(): ?string
    {
        return $this->created;
    }

    public function setCreated(?string $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCancellationReason(): ?string
    {
        return $this->cancellationReason;
    }

    public function setCancellationReason(?string $cancellationReason): self
    {
        $this->cancellationReason = $cancellationReason;

        return $this;
    }

    public function getCanceledAt(): ?string
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(?string $canceledAt): self
    {
        $this->canceledAt = $canceledAt;

        return $this;
    }

    public function getCaptureMethod(): ?string
    {
        return $this->captureMethod;
    }

    public function setCaptureMethod(?string $captureMethod): self
    {
        $this->captureMethod = $captureMethod;

        return $this;
    }

    public function getConfirmationMethod(): ?string
    {
        return $this->confirmationMethod;
    }

    public function setConfirmationMethod(?string $confirmationMethod): self
    {
        $this->confirmationMethod = $confirmationMethod;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLastPaymentError(): ?string
    {
        return $this->lastPaymentError;
    }

    public function setLastPaymentError(?string $lastPaymentError): self
    {
        $this->lastPaymentError = $lastPaymentError;

        return $this;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function setObject(?string $object): self
    {
        $this->object = $object;

        return $this;
    }

    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    public function setHolderName(?string $holderName): self
    {
        $this->holderName = $holderName;

        return $this;
    }

    public function getPaymentMethodType(): ?string
    {
        return $this->paymentMethodType;
    }

    public function setPaymentMethodType(?string $paymentMethodType): self
    {
        $this->paymentMethodType = $paymentMethodType;

        return $this;
    }
}
