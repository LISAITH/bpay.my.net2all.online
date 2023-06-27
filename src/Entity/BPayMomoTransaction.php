<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\BPayMtnMomoTransactionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bpay_momo_transaction")
 *
 * @ORM\Entity(repositoryClass=BPayMtnMomoTransactionRepository::class)
 */

class BPayMomoTransaction
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
     * @ORM\Column(type="guid",name="guid", nullable=true)
     */
    private $guid;
    /**
     * @ORM\Column(name="xreference_id", type="string",length=255,nullable=true)
     */
    private $xReferenceId;
    /**
     * @ORM\Column(name="party_id", type="string",length=255,nullable=true)
     */
    private $partyId;
    /**
     * @ORM\Column(name="payer_message", type="string",length=255,nullable=true)
     */
    private $payerMessage;
    /**
     * @ORM\Column(name="payee_note", type="string",length=255,nullable=true)
     */
    private $payeeNote;

    /**
     * @ORM\Column(name="currency", type="string",length=50,nullable=true)
     */
    private $currency;
    /**
     * @ORM\Column(name="party_id_type", type="string",length=255,nullable=true)
     */
    private $partyIdType;
    /**
     * @ORM\Column(name="transaction_Ref", type="string",length=255,nullable=true)
     */
    private $transactionRef;
    /**
     * @ORM\Column(name="holderfullName", type="string",length=50,nullable=true)
     */
    private $holderfullName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setGuid(?string $guid): void
    {
        $this->guid = $guid;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function setPayerMessage(?string $payerMessage): void
    {
        $this->payerMessage = $payerMessage;
    }

    public function setPayeeNote(?string $payeeNote): void
    {
        $this->payeeNote = $payeeNote;
    }

    public function getPayerMessage(): ?string
    {
        return $this->payerMessage;
    }

    public function getPayeeNote(): ?string
    {
        return $this->payeeNote;
    }

    public function setPartyIdType(?string $partyIdType): void
    {
        $this->partyIdType = $partyIdType;
    }

    public function setPartyId(?string $partyId): void
    {
        $this->partyId = $partyId;
    }

    public function getPartyIdType(): ?string
    {
        return $this->partyIdType;
    }

    public function getPartyId(): ?string
    {
        return $this->partyId;
    }

    public function setXReferenceId(?string $xReferenceId): void
    {
        $this->xReferenceId = $xReferenceId;
    }

    public function getXReferenceId(): ?string
    {
        return $this->xReferenceId;
    }

    public function getHolderfullName(): ?string
    {
        return $this->holderfullName;
    }

    public function getTransactionRef(): ?string
    {
        return $this->transactionRef;
    }

    public function setHolderfullName(?string $holderfullName): void
    {
        $this->holderfullName = $holderfullName;
    }

    public function setTransactionRef(?string $transactionRef): void
    {
        $this->transactionRef = $transactionRef;
    }
}
