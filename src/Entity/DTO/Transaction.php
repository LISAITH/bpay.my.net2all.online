<?php

namespace App\Entity\DTO;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetTransactionController;
use App\Api\Controller\UserTransactionList;

#[ApiResource(
    collectionOperations: [
        'get' => ['method' => 'GET'],
        'liste_des_transaction' => [
            'method' => 'GET',
            'path' => 'liste-transactions/{user_id}/user',
            'controller' => UserTransactionList::class,
            'read' => false,
            'pagination_enable' => false,
            'openapi_context' => [
                'summary' => 'Rècupère la liste des transactions d\'un utilisateur',
                'description' => 'Rècupère la liste des transactions d\'un utilisateur',
            ],
        ],
    ],
    itemOperations: ['get' => ['method' => 'GET', 'controller' => GetTransactionController::class, 'read' => false]],
    paginationEnabled: false,
)]
class Transaction
{
    #[ApiProperty(identifier: true)]
    private int $Id;
    #[ApiProperty(description: 'Le moyen de paiement')]
    private $providerName;
    #[ApiProperty(description: 'Le montant total de la transaction')]
    private $amountPayed;

    #[ApiProperty(description: 'Le montant de la transaction')]
    private $amount;
    #[ApiProperty(description: 'La référence de la transaction')]
    private $transactionID;
    #[ApiProperty(description: 'La taxe de la transaction')]
    private $taxe;
    #[ApiProperty(description: 'La devise')]
    private $currency;
    #[ApiProperty(description: 'Le statut')]
    private $status;
    #[ApiProperty(description: 'La cause pour le cas d\'une transaction echouée')]
    private $reason;
    #[ApiProperty(description: 'L\'email du client')]
    private $holderEmail;

    #[ApiProperty(description: 'Le nom du client')]
    private $holderName;

    #[ApiProperty(description: 'La date de la transaction')]
    private $paymentDate;

    #[ApiProperty(description: 'Le type d\'opération: Crédit ou débit')]
    private $operationType;

    #[ApiProperty(description: 'Catégorie de la transaction: REGLEMENT, PAYEMENT,RECHARGEMENT')]
    private $transactionTypeName;

    #[ApiProperty(description: 'Url image du moyen de paiement utilisé')]
    private $providerImg;

    #[ApiProperty(description: 'Note')]
    private $note;

    #[ApiProperty(description: 'Identifiant unique')]
    private $guid;

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @return mixed
     */
    public function getHolderEmail()
    {
        return $this->holderEmail;
    }

    /**
     * @param mixed $guid
     */
    public function setGuid($guid): void
    {
        $this->guid = $guid;
    }

    /**
     * @return mixed
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     * @return mixed
     */
    public function getHolderName()
    {
        return $this->holderName;
    }

    /**
     * @param mixed $holderEmail
     */
    public function setHolderEmail($holderEmail): void
    {
        $this->holderEmail = $holderEmail;
    }

    /**
     * @param mixed $holderName
     */
    public function setHolderName($holderName): void
    {
        $this->holderName = $holderName;
    }

    /**
     * @return mixed
     */
    public function getProviderImg()
    {
        return $this->providerImg;
    }

    /**
     * @param mixed $providerImg
     */
    public function setProviderImg($providerImg): void
    {
        $this->providerImg = $providerImg;
    }

    /**
     * @return mixed
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @param mixed $paymentDate
     */
    public function setPaymentDate($paymentDate): void
    {
        $this->paymentDate = $paymentDate;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getAmountPayed()
    {
        return $this->amountPayed;
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
    public function getProviderName()
    {
        return $this->providerName;
    }

    /**
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getTaxe()
    {
        return $this->taxe;
    }

    /**
     * @return mixed
     */
    public function getTransactionID()
    {
        return $this->transactionID;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $amountPayed
     */
    public function setAmountPayed($amountPayed): void
    {
        $this->amountPayed = $amountPayed;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $providerName
     */
    public function setProviderName($providerName): void
    {
        $this->providerName = $providerName;
    }

    public function getId(): int
    {
        return $this->Id;
    }

    public function setId(int $Id): void
    {
        $this->Id = $Id;
    }

    /**
     * @param mixed $reason
     */
    public function setReason($reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @param mixed $taxe
     */
    public function setTaxe($taxe): void
    {
        $this->taxe = $taxe;
    }

    /**
     * @return mixed
     */
    public function getTransactionTypeName()
    {
        return $this->transactionTypeName;
    }

    /**
     * @param mixed $transactionTypeName
     */
    public function setTransactionTypeName($transactionTypeName): void
    {
        $this->transactionTypeName = $transactionTypeName;
    }

    /**
     * @return mixed
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     * @param mixed $operationType
     */
    public function setOperationType($operationType): void
    {
        $this->operationType = $operationType;
    }

    /**
     * @param mixed $transactionID
     */
    public function setTransactionID($transactionID): void
    {
        $this->transactionID = $transactionID;
    }
}