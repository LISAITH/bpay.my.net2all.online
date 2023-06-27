<?php

namespace App\Entity\BPayEntity\Momo;

class PayerToPay
{
    private ?string $partyIdType;
    private ?string $partyId;

    public function __construct(string $partyIdType, string $partyId)
    {
        $this->partyId = $partyId;
        $this->partyIdType = $partyIdType;
    }

    /**
     * @return string|null
     */
    public function getPartyId(): ?string
    {
        return $this->partyId;
    }

    /**
     * @return string|null
     */
    public function getPartyIdType(): ?string
    {
        return $this->partyIdType;
    }

    /**
     * @param string|null $partyId
     */
    public function setPartyId(?string $partyId): void
    {
        $this->partyId = $partyId;
    }

    /**
     * @param string|null $partyIdType
     */
    public function setPartyIdType(?string $partyIdType): void
    {
        $this->partyIdType = $partyIdType;
    }

}
