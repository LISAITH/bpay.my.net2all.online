<?php

namespace App\Entity\BPayEntity\Momo;

class RequestToPayData
{
    private ?string $amount;

    private ?string $currency;
    private ?string $externalId;
    private ?PayerToPay $payer;
    private ?string $payerMessage;
    private ?string $payeeNote;
    private ?string $financialTransactionId;

    private ?string $status;

    private ?string $reason;

    private ?string $name;

    public function __construct(string $amount = '', string $currency = '', string $externalId = '', PayerToPay $payer = null, string $payerMessage = '', $payeeNote = '')
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->externalId = $externalId;
        $this->payer = $payer;
        $this->payerMessage = $payerMessage;
        $this->payeeNote = $payeeNote;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getFinancialTransactionId(): ?string
    {
        return $this->financialTransactionId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPayeeNote(): ?string
    {
        return $this->payeeNote;
    }

    public function getPayer(): ?PayerToPay
    {
        return $this->payer;
    }

    /**
     * @param PayerToPay|null $payer
     */
    public function setPayer(?PayerToPay $payer): void
    {
        $this->payer = $payer;
    }

    public function getPayerMessage(): ?string
    {
        return $this->payerMessage;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setAmount(?string $amount): void
    {
        $this->amount = $amount;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }

    public function setFinancialTransactionId(?string $financialTransactionId): void
    {
        $this->financialTransactionId = $financialTransactionId;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function setPayeeNote(?string $payeeNote): void
    {
        $this->payeeNote = $payeeNote;
    }



    public function setPayerMessage(?string $payerMessage): void
    {
        $this->payerMessage = $payerMessage;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}
