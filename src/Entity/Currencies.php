<?php

namespace App\Entity;

use App\Repository\CurrenciesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrenciesRepository::class)
 */
class Currencies
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string",name="currency_iso_code",length=255,nullable=false)
     */
    private $currencyIsoCode;
    /**
     * @ORM\Column(type="string",name="currency_name",length=255,nullable=true)
     */
    private $currencyName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyIsoCode(): ?string
    {
        return $this->currencyIsoCode;
    }

    public function setCurrencyIsoCode(string $currencyIsoCode): self
    {
        $this->currencyIsoCode = $currencyIsoCode;

        return $this;
    }

    public function getCurrencyName(): ?string
    {
        return $this->currencyName;
    }

    public function setCurrencyName(?string $currencyName): self
    {
        $this->currencyName = $currencyName;

        return $this;
    }
}
