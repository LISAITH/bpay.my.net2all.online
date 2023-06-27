<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BPayPaymentTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bpay_transaction")
 * @ORM\Entity(repositoryClass=BPayPaymentTransactionRepository::class)
 */
class BPayTransaction
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    private $id = null;
    /**
     * @ORM\Column(type="guid",name="guid", nullable=true)
     */
    private $guid;
    /**
     * @ORM\Column(type="string",name="status",length=50,nullable=true)
     */
    private $status;
    /**
     * @ORM\Column(type="string",name="provider_code",length=255, nullable=true)
     */
    private $providerCode;
    /**
     * @ORM\Column(type="float",name="amount",nullable=true)
     */
    private $amount;
    /**
     * @ORM\Column(type="string",name="currency",length=50, nullable=true)
     */
    private $currency;
    /**
     * @ORM\Column(type="string",name="reason",length=200, nullable=true)
     */
    /**
     * @ORM\Column(type="datetime",name="payment_date",nullable=true)
     */
    private $transaction_date;
    /**
     * @ORM\Column(name="transaction_status_change_date", type="datetime",nullable=true)
     */
    private $transaction_change_status_date;

    /**
     * @ORM\Column(name="user_id", type="integer",nullable=true)
     */
    private $user_id;
    /**
     * @ORM\Column(type="string",name="entity_id",length=255,nullable=true)
     */
    private $entityId;
    /**
     * @ORM\Column(type="string",name="type_opteration",length=255,nullable=true)
     */
    private $typeOpteration;
    /**
     * @ORM\Column(type="string",name="transaction_type_name",length=255,nullable=true)
     */
    private $transactionTypeName;

    /**
     * @ORM\Column(name="note", type="string",length=255, nullable=true)
     */
    private $note;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProviderCode(): ?string
    {
        return $this->providerCode;
    }

    public function setProviderCode(?string $providerCode): self
    {
        $this->providerCode = $providerCode;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

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

    public function getTransactionDate(): ?\DateTimeInterface
    {
        return $this->transaction_date;
    }

    public function setTransactionDate(?\DateTimeInterface $transaction_date): self
    {
        $this->transaction_date = $transaction_date;

        return $this;
    }

    public function getTransactionChangeStatusDate(): ?\DateTimeInterface
    {
        return $this->transaction_change_status_date;
    }

    public function setTransactionChangeStatusDate(?\DateTimeInterface $transaction_change_status_date): self
    {
        $this->transaction_change_status_date = $transaction_change_status_date;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getEntityId(): ?string
    {
        return $this->entityId;
    }

    public function setEntityId(?string $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getTypeOpteration(): ?string
    {
        return $this->typeOpteration;
    }

    public function setTypeOpteration(?string $typeOpteration): self
    {
        $this->typeOpteration = $typeOpteration;

        return $this;
    }

    public function getTransactionTypeName(): ?string
    {
        return $this->transactionTypeName;
    }

    public function setTransactionTypeName(?string $transactionTypeName): self
    {
        $this->transactionTypeName = $transactionTypeName;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
